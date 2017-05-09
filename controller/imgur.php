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
use phpbb\exception\runtime_exception;
use phpbb\request\request_interface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class imgur {

	protected $container;
	protected $config;
	protected $request;
	protected $template;
	protected $helper;
	protected $imgur;

	public function __construct(Container $container) {
		$this->container = $container;
		$this->config = $this->container->get('config');
		$this->request = $this->container->get('request');
		$this->template = $this->container->get('template');
		$this->helper = $this->container->get('controller.helper');
		$this->imgur = $this->container->get('j0k3r.imgur_api.imgur_client');

		// Mandatory API data
		if (empty($this->config['imgur_client_id']) || empty($this->config['imgur_client_secret'])) {
			throw new runtime_exception('EXCEPTION_IMGUR_NO_API_DATA');
		}

		// Setup Imgur API
		$this->imgur->setOption('client_id', $this->config['imgur_client_id']);
		$this->imgur->setOption('client_secret', $this->config['imgur_client_secret']);

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

		$this->imgur->setAccessToken($token);

		// Refresh token
		if ($this->imgur->checkAccessTokenExpired()) {
			$this->imgur->refreshToken();

			// Update the token in database
			foreach($this->imgur->getAccessToken() as $key => $value) {

				// The configuration table does not accept null values
				$value = ($key == 'scope') ? '' : $value;

				// Save changes
				$this->config->set(sprintf('imgur_%s', $key), $value, false);
			}
		}
	}

	public function upload() {
		if (!$this->request->is_ajax()) {
			exit;
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
		if (!empty($images['name'])) {
			foreach ($images['name'] as $key => $value) {
				if (!file_exists($images['tmp_name'][$key])) {
					break;
				}

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
