<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0
 */

namespace alfredoramos\imgur\controller;

use phpbb\config\config;
use phpbb\request\request;
use phpbb\controller\helper;
use phpbb\filesystem\filesystem;
use phpbb\language\language;
use phpbb\exception\runtime_exception;
use phpbb\exception\http_exception;
use phpbb\request\request_interface;
use phpbb\json_response;
use Imgur\Client as ImgurClient;
use Imgur\Exception\ErrorException as ImgurErrorException;

class imgur
{

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\filesystem\filesystem */
	protected $filesystem;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \Imgur\Client */
	protected $imgur;


	/**
	 * Controller constructor.
	 *
	 * @param \phpbb\config\config			$config
	 * @param \phpbb\request\request		$request
	 * @param \phpbb\controller\helper		$helper
	 * @param \phpbb\filesystem\filesystem	$filesystem
	 * @param \phpbb\language\language		$language
	 * @param \Imgur\Client					$imgur
	 *
	 * @return void
	 */
	public function __construct(config $config, request $request, helper $helper, filesystem $filesystem, language $language, ImgurClient $imgur)
	{
		$this->config = $config;
		$this->request = $request;
		$this->helper = $helper;
		$this->filesystem = $filesystem;
		$this->language = $language;
		$this->imgur = $imgur;

		// Add controller translations
		$this->language->add_lang('controller', 'alfredoramos/imgur');

		// Mandatory API data
		if (empty($this->config['imgur_client_id']) || empty($this->config['imgur_client_secret']))
		{
			throw new runtime_exception('EXCEPTION_IMGUR_NO_API_DATA');
		}

		// Setup Imgur API
		$this->imgur->setOption('client_id', $this->config['imgur_client_id']);
		$this->imgur->setOption('client_secret', $this->config['imgur_client_secret']);

		// Construct Imgur token
		$token = [
			'access_token'		=> $this->config['imgur_access_token'],
			'expires_in'		=> (int) $this->config['imgur_expires_in'],
			'token_type'		=> $this->config['imgur_token_type'],
			'scope'				=> (empty($this->config['imgur_scope']) ? null : $this->config['imgur_scope']),
			'refresh_token'		=> $this->config['imgur_refresh_token'],
			'account_id'		=> (int) $this->config['imgur_accound_id'],
			'account_username'	=> $this->config['imgur_account_username'],
			'created_at'		=> (int) $this->config['imgur_created_at']
		];

		// Set token
		$this->imgur->setAccessToken($token);

		// Check if token expired
		if ($this->imgur->checkAccessTokenExpired())
		{
			$this->refreshToken();
		}
	}

	/**
	 * Refresh token helper.
	 *
	 * @return void
	 */
	private function refreshToken()
	{
		$this->imgur->refreshToken();

		// Generate new token
		$new_token = array_merge($this->imgur->getAccessToken(), [
			'created_at' => time()
		]);

		// Update the token in database
		foreach ($new_token as $key => $value)
		{
			// The scope column can be null, and the configuration
			// table does not accept null values
			if ($key == 'scope')
			{
				$value = empty($value) ? '' : $value;
			}

			// Save changes
			$this->config->set(sprintf('imgur_%s', $key), $value, false);
		}
	}

	/**
	 * Upload controller handler. AJAX calls only.
	 *
	 * @return string
	 */
	public function upload()
	{
		// This route only responds to AJAX calls
		if (!$this->request->is_ajax())
		{
			throw new runtime_exception('EXCEPTION_IMGUR_AJAX_ONLY');
		}

		// Add CSFR protection
		if (!check_link_hash($this->request->variable('hash', ''), 'imgur_upload'))
		{
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		// Check if token expired
		if ($this->imgur->checkAccessTokenExpired())
		{
			$this->refreshToken();
		}

		// Not using $request->file() because I need an array of arrays
		$images = $this->request->variable(
			'imgur_image',
			['' => [0 => '']],
			true,
			request_interface::FILES
		);

		// Upload responses
		$data = [];

		// Upload images
		if (!empty($images['name']))
		{
			foreach ($images['name'] as $key => $value)
			{
				// Image file must exist and be readable
				if (!$this->filesystem->is_readable($images['tmp_name'][$key]))
				{
					continue;
				}

				// Upload image and save response, it will be used
				// later to show a JSON object
				try
				{
					$data[] = $this->imgur->api('image')->upload([
						'image' => base64_encode(file_get_contents($images['tmp_name'][$key])),
						'type'	=> 'base64',
						'album'	=> $this->config['imgur_album'],
						'name'	=> $value
					]);
				}
				catch (ImgurErrorException $ex)
				{
					// Update token as it expired unexpectedly
					$this->refreshToken();

					// Clear previous data
					$data = [];

					// Try again to upload the image, if succeed it will add
					// the BBCode of the image, otherwise an error message
					try
					{
						$data[] = $this->imgur->api('image')->upload([
							'image' => base64_encode(file_get_contents($images['tmp_name'][$key])),
							'type'	=> 'base64',
							'album'	=> $this->config['imgur_album'],
							'name'	=> $value
						]);
					}
					catch (ImgurErrorException $ex)
					{
						throw new runtime_exception('EXCEPTION_IMGUR_BAD_REQUEST', [$ex->getMessage()], $ex);
					}
				}
			}
		}

		// Return a JSON response
		$response = new json_response;

		return $response->send($data);
	}

}
