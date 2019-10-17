<?php

/**
 * Imgur extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\imgur\controller;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\template\template;
use phpbb\request\request;
use phpbb\controller\helper as controller_helper;
use phpbb\filesystem\filesystem;
use phpbb\language\language;
use phpbb\user;
use phpbb\log\log;
use phpbb\exception\runtime_exception;
use phpbb\exception\http_exception;
use phpbb\request\request_interface;
use Imgur\Client as ImgurClient;
use alfredoramos\imgur\includes\helper;
use Symfony\Component\HttpFoundation\JsonResponse;

class imgur
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\controller\helper */
	protected $controller_helper;

	/** @var \phpbb\filesystem\filesystem */
	protected $filesystem;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \Imgur\Client */
	protected $imgur;

	/** @var \alfredoramos\imgur\includes\helper */
	protected $helper;

	/**
	 * Controller constructor.
	 *
	 * @param \phpbb\auth\auth						$auth
	 * @param \phpbb\config\config					$config
	 * @param \phpbb\template\template				$template
	 * @param \phpbb\request\request				$request
	 * @param \phpbb\controller\helper				$controller_helper
	 * @param \phpbb\filesystem\filesystem			$filesystem
	 * @param \phpbb\language\language				$language
	 * @param \phpbb\user							$user
	 * @param \phpbb\log\log						$log
	 * @param \Imgur\Client							$imgur
	 * @param \alfredoramos\imgur\includes\helper	$helper
	 *
	 * @return void
	 */
	public function __construct(auth $auth, config $config, template $template, request $request, controller_helper $controller_helper, filesystem $filesystem, language $language, user $user, log $log, ImgurClient $imgur, helper $helper)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->template = $template;
		$this->request = $request;
		$this->controller_helper = $controller_helper;
		$this->filesystem = $filesystem;
		$this->language = $language;
		$this->user = $user;
		$this->log = $log;
		$this->imgur = $imgur;
		$this->helper = $helper;
	}

	/**
	 * Imgur authorization controller handler.
	 *
	 * @param string $hash
	 *
	 * @throws \phpbb\exception\http_exception
	 *
	 * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function authorize($hash = '')
	{
		// Load translations
		$this->language->add_lang(['controller', 'acp/info_acp_common'], 'alfredoramos/imgur');

		// This route can only be used by admins
		// Users do not need to know this page exist
		if (!$this->auth->acl_get('a_'))
		{
			throw new http_exception(404, 'PAGE_NOT_FOUND');
		}

		// Get Imgur token
		$token = $this->helper->imgur_token();

		// Parse response fom Imgur API
		if (!$this->request->is_ajax())
		{
			$this->template->assign_vars([
				'IMGUR_IS_AUTHORIZED' => (!empty($token['access_token']) && !empty($token['refresh_token'])),
				'IMGUR_AUTHORIZE_URL' => vsprintf('%1$s/%2$s', [
					$this->controller_helper->route('alfredoramos_imgur_authorize'),
					generate_link_hash('imgur_authorize')
				])
			]);

			return $this->controller_helper->render('imgur_authorize.html', $this->language->lang('IMGUR_AUTHORIZATION'));
		}

		// Security hash
		$hash = trim($hash);

		// CSRF protection
		if (empty($hash) || !check_link_hash($hash, 'imgur_authorize'))
		{
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		// Construct new token
		$new_token = [
			'access_token'		=> '',
			'expires_in'		=> 0,
			'token_type'		=> '',
			'refresh_token'		=> '',
			'account_id'		=> 0,
			'account_username'	=> '',
			'scope'				=> null
		];

		// Generate new token
		foreach ($new_token as $key => $value)
		{
			// Cast values
			$value = (in_array($key, ['expires_in', 'account_id'])) ? (int) $value : trim($value);

			// Generate new token
			$new_token[$key] = $this->request->variable($key, $value);
		}

		// Set token
		$this->imgur->setAccessToken($new_token);

		// Update token
		$token = $this->imgur->getAccessToken();

		// Save new token
		foreach ($token as $key => $value)
		{
			// Scope can be NULL
			// Configuration table does not allow NULL values
			if ($key === 'scope')
			{
				$value = trim($value);
			}

			$this->config->set(sprintf('imgur_%s', $key), $value, false);
		}

		// Admin log
		$this->log->add(
			'admin',
			$this->user->data['user_id'],
			$this->user->ip,
			'LOG_IMGUR_DATA',
			false,
			[$this->language->lang('IMGUR_AUTHORIZATION')]
		);

		// Return a JSON response
		return new JsonResponse(['message' => 'success']);
	}

	/**
	 * Upload controller handler. AJAX calls only.
	 *
	 * @param string $hash
	 *
	 * @throws \phpbb\exception\runtime_exception
	 * @throws \phpbb\exception\http_exception
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function upload($hash = '')
	{
		// Add translations
		$this->language->add_lang('controller', 'alfredoramos/imgur');

		// This route only responds to AJAX calls
		if (!$this->request->is_ajax())
		{
			throw new runtime_exception('EXCEPTION_IMGUR_AJAX_ONLY');
		}

		// Mandatory API data
		if (empty($this->config['imgur_client_id']) || empty($this->config['imgur_client_secret']))
		{
			throw new runtime_exception('EXCEPTION_IMGUR_NO_API_DATA');
		}

		// Setup Imgur API
		$this->imgur->setOption('client_id', $this->config['imgur_client_id']);
		$this->imgur->setOption('client_secret', $this->config['imgur_client_secret']);

		// Security hash
		$hash = trim($hash);

		// CSRF protection
		if (empty($hash) || !check_link_hash($hash, 'imgur_upload'))
		{
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		// Get Imgur token
		$token = $this->helper->imgur_token();

		// Set token
		$this->imgur->setAccessToken($token);

		// Check if token expired
		if ($this->imgur->checkAccessTokenExpired())
		{
			$this->refresh_token();
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
		if (!empty($images['tmp_name']))
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
				$data[] = $this->imgur->api('image')->upload([
					'image' => base64_encode(file_get_contents($images['tmp_name'][$key])),
					'type'	=> 'base64',
					'album'	=> $this->config['imgur_album'],
					'name'	=> $value
				]);
			}
		}

		// Image errors
		if (!empty($images['error']))
		{
			$errors = [];

			foreach ($images['error'] as $key => $value)
			{
				$value = (int) $value;

				switch ($value)
				{
					case UPLOAD_ERR_INI_SIZE:
						$errors[]['message'] = $this->language->lang('UPLOAD_ERR_INI_SIZE');
					break;

					case UPLOAD_ERR_FORM_SIZE:
						$errors[]['message'] = $this->language->lang('UPLOAD_ERR_FORM_SIZE');
					break;

					case UPLOAD_ERR_PARTIAL:
						$errors[]['message'] = $this->language->lang('UPLOAD_ERR_PARTIAL');
					break;

					case UPLOAD_ERR_NO_FILE:
						$errors[]['message'] = $this->language->lang('UPLOAD_ERR_NO_FILE');
					break;

					case UPLOAD_ERR_NO_TMP_DIR:
						$errors[]['message'] = $this->language->lang('UPLOAD_ERR_NO_TMP_DIR');
					break;

					case UPLOAD_ERR_CANT_WRITE:
						$errors[]['message'] = $this->language->lang('UPLOAD_ERR_CANT_WRITE');
					break;

					case UPLOAD_ERR_EXTENSION:
						$errors[]['message'] = $this->language->lang('UPLOAD_ERR_EXTENSION');
					break;

					default: // UPLOAD_ERR_OK
						continue 2;
					break;
				}
			}

			if (!empty($errors))
			{
				return new JsonResponse($errors, 400);
			}
		}

		// Return a JSON response
		return new JsonResponse($data);
	}

	/**
	 * Refresh token helper.
	 *
	 * @return void
	 */
	private function refresh_token()
	{
		$this->imgur->refreshToken();

		// Generate new token
		$new_token = array_merge($this->imgur->getAccessToken(), [
			'created_at' => time()
		]);

		// Update the token in database
		foreach ($new_token as $key => $value)
		{
			// Scope can be NULL
			// Configuration table does not allow NULL values
			if ($key === 'scope')
			{
				$value = trim($value);
			}

			// Save changes
			$this->config->set(sprintf('imgur_%s', $key), $value, false);
		}
	}
}
