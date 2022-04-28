<?php

class PocketButtonExtension extends Minz_Extension {
	public function init() {
		$this->registerTranslates();

		Minz_View::appendScript($this->getFileUrl('jquerymin.js', 'js'), false, false, false);
		Minz_View::appendScript($this->getFileUrl('script.js', 'js'), false, false, false);
		Minz_View::appendStyle($this->getFileUrl('style.css', 'css'));

		$this->registerController('pocketButton');
		$this->registerViews();
	}

	public function handleConfigureAction() {
		if (Minz_Request::isPost()) {
			$consumer_key = Minz_Request::param('consumer_key', '');
			FreshRSS_Context::$user_conf->pocket_consumer_key = $consumer_key;
			FreshRSS_Context::$user_conf->save();
		}
	}
}
