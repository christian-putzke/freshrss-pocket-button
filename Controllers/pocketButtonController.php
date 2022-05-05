<?php

class FreshExtension_pocketButton_Controller extends Minz_ActionController
{
	public function jsVarsAction()
	{
		$extension = Minz_ExtensionManager::findExtension('Pocket Button');

		$this->view->pocket_button_vars = json_encode(array(
			'keyboard_shortcut' => FreshRSS_Context::$user_conf->pocket_keyboard_shortcut,
			'icons' => array(
				'add_to_pocket' => $extension->getFileUrl('add_to_pocket.svg', 'svg'),
				'adding_to_pocket' => $extension->getFileUrl('adding_to_pocket.svg', 'svg'),
				'added_to_pocket' => $extension->getFileUrl('added_to_pocket.svg', 'svg'),
			),
			'i18n' => array(
				'added_article_to_pocket' => _t('ext.pocketButton.notifications.added_article_to_pocket', '%s'),
				'failed_to_add_article_to_pocket' => _t('ext.pocketButton.notifications.failed_to_add_article_to_pocket', '%s'),
				'ajax_request_failed' => _t('ext.pocketButton.notifications.ajax_request_failed'),
				'article_not_found' => _t('ext.pocketButton.notifications.article_not_found'),
			)
		));

		$this->view->_layout(false);
		$this->view->_path('pocketButton/vars.js');

		header('Content-Type: application/javascript; charset=utf-8');
	}

	public function authorizeAction()
	{
		$post_data = array(
			'consumer_key' => FreshRSS_Context::$user_conf->pocket_consumer_key,
			'code' => FreshRSS_Context::$user_conf->pocket_request_token
		);

		$result = $this->curlPostRequest('https://getpocket.com/v3/oauth/authorize', $post_data);
		$url_redirect = array('c' => 'extension', 'a' => 'configure', 'params' => array('e' => 'Pocket Button'));

		if ($result['status'] == 200) {
			FreshRSS_Context::$user_conf->pocket_username = $result['response']->username;
			FreshRSS_Context::$user_conf->pocket_access_token = $result['response']->access_token;
			FreshRSS_Context::$user_conf->save();

			Minz_Request::good(_t('ext.pocketButton.notifications.authorized_success'), $url_redirect);
		} else {
			if ($result['errorCode'] == 158) {
				Minz_Request::bad(_t('ext.pocketButton.notifications.authorized_aborted'), $url_redirect);
			} else {
				Minz_Request::bad(_t('ext.pocketButton.notifications.authorized_failed', $result['errorCode']), $url_redirect);
			}
		}
	}

	public function requestAccessAction()
	{
		$consumer_key = Minz_Request::param('consumer_key', '');
		FreshRSS_Context::$user_conf->pocket_consumer_key = $consumer_key;
		FreshRSS_Context::$user_conf->save();

		$post_data = array(
			'consumer_key' => $consumer_key,
			'redirect_uri' => 'not_needed'
		);

		$result = $this->curlPostRequest('https://getpocket.com/v3/oauth/request', $post_data);

		if ($result['status'] == 200) {
			FreshRSS_Context::$user_conf->pocket_request_token = $result['response']->code;
			FreshRSS_Context::$user_conf->save();

			$redirect_url = Minz_Url::display(array('c' => 'pocketButton', 'a' => 'authorize'), 'html', true);
			$redirect_url = str_replace('&', urlencode('&'), $redirect_url);
			$pocket_redirect_url = 'https://getpocket.com/auth/authorize?request_token=' . $result['response']->code . '&redirect_uri=' . $redirect_url;

			Minz_Request::forward($pocket_redirect_url);
		} else {
			$url_redirect = array('c' => 'extension', 'a' => 'configure', 'params' => array('e' => 'Pocket Button'));
			Minz_Request::bad(_t('ext.pocketButton.notifications.request_access_failed', $result['errorCode']), $url_redirect);
		}
	}

	public function revokeAccessAction()
	{
		FreshRSS_Context::$user_conf->pocket_request_token = '';
		FreshRSS_Context::$user_conf->pocket_access_token = '';
		FreshRSS_Context::$user_conf->pocket_consumer_key = '';
		FreshRSS_Context::$user_conf->pocket_username = '';
		FreshRSS_Context::$user_conf->save();

		$url_redirect = array('c' => 'extension', 'a' => 'configure', 'params' => array('e' => 'Pocket Button'));
		Minz_Request::forward($url_redirect);
	}

	public function shareAction()
	{
		$this->view->_layout(false);

		$entry_id = Minz_Request::param('id');
		$entry_dao = FreshRSS_Factory::createEntryDao();
		$entry = $entry_dao->searchById($entry_id);

		if ($entry === null) {
			echo json_encode(array('status' => 404));
			return;
		}

		$post_data = array(
			'consumer_key' => FreshRSS_Context::$user_conf->pocket_consumer_key,
			'access_token' => FreshRSS_Context::$user_conf->pocket_access_token,
			'url' => $entry->link(),
			'title' => $entry->title(),
			'time' => time()
		);

		$result = $this->curlPostRequest('https://getpocket.com/v3/add', $post_data);
		$result['response'] = array('title' => $entry->title());

		echo json_encode($result);
	}

	private function curlPostRequest($url, $post_data)
	{
		$headers = array(
			'Content-Type: application/json; charset=UTF-8',
			'X-Accept: application/json'
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));

		$response = curl_exec($curl);

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$response_header = substr($response, 0, $header_size);
		$response_body = substr($response, $header_size);
		$response_headers = $this->httpHeaderToArray($response_header);

		return array(
			'response' => json_decode($response_body),
			'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
			'errorCode' => isset($response_headers['x-error-code']) ? intval($response_headers['x-error-code']) : 0
		);
	}

	private function httpHeaderToArray($header)
	{
		$headers = array();
		$headers_parts = explode("\r\n", $header);

		foreach ($headers_parts as $header_part) {
			// skip empty header parts
			if (strlen($header_part) <= 0) {
				continue;
			}

			// Filter the beginning of the header which is the basic HTTP status code
			if (strpos($header_part, ':')) {
				$header_name = substr($header_part, 0, strpos($header_part, ':'));
				$header_value = substr($header_part, strpos($header_part, ':') + 1);
				$headers[$header_name] = trim($header_value);
			}
		}

		return $headers;
	}
}
