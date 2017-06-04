<?php

/**
 * Imgur Extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GNU GPL-2.0
 */

namespace alfredoramos\imgur\controller;

use Imgur\Client as ImgurClient;
use phpbb\json_response;
use phpbb\config\config;
use phpbb\request\request;
use phpbb\request\request_interface;
use phpbb\controller\helper;
use phpbb\filesystem\filesystem;
use phpbb\language\language;
use phpbb\exception\runtime_exception;

class imgur
{

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var \phpbb\filesystem\filesystem $filesystem */
	protected $filesystem;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \Imgur\Client $imgur */
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

		// Add exception translations
		$this->language->add_lang('exceptions', 'alfredoramos/imgur');

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

		// Refresh token
		if ($this->imgur->checkAccessTokenExpired())
		{
			$this->imgur->refreshToken();

			// Update the token in database
			foreach($this->imgur->getAccessToken() as $key => $value)
			{
				// scope can be null, and the configuration
				// table does not accept null values
				if ($key == 'scope') {
					$value = empty($value) ? '' : $value;
				}

				// Save changes
				$this->config->set(sprintf('imgur_%s', $key), $value, false);
			}
		}
	}

	/**
	 * Upload controller handler. AJAX calls only.
	 *
	 * @return string
	 */
	public function upload()
	{
		if (!$this->request->is_ajax())
		{
			throw new runtime_exception('EXCEPTION_IMGUR_AJAX_ONLY');
		}

		// Not using $request->file() because I need an array of arrays
		$images = $this->request->variable(
			'imgur_image',
			['' => [0 => '']],
			true,
			request_interface::FILES
		);

		// Upload responses
		$upload_data = [];

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
				$upload_data[] = $this->imgur->api('image')->upload([
					'image' => base64_encode(file_get_contents($images['tmp_name'][$key])),
					'type'	=> 'base64',
					'album'	=> $this->config['imgur_album'],
					'name'	=> $value
				]);
			}
		}

		// Return a JSON response
		$response = new json_response;

		return $response->send($upload_data);
	}

}
