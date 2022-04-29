<?php

class PocketButtonExtension extends Minz_Extension {
	public function init() {
		$this->registerTranslates();

		Minz_View::appendScript($this->getFileUrl('jquerymin.js', 'js'), false, false, false);
		Minz_View::appendScript($this->getFileUrl('script.js', 'js'), false, false, false);
		Minz_View::appendStyle($this->getFileUrl('style.css', 'css'));
		Minz_View::appendScript(_url('pocketButton', 'jsVars'), false, true, false);

		$this->registerController('pocketButton');
		$this->registerViews();
	}

	public function handleConfigureAction() {
		if (Minz_Request::isPost()) {
			$consumer_key = Minz_Request::param('consumer_key', '');
			$keyboard_shortcut = Minz_Request::param('keyboard_shortcut', '');
			FreshRSS_Context::$user_conf->pocket_consumer_key = $consumer_key;
			FreshRSS_Context::$user_conf->pocket_keyboard_shortcut = $keyboard_shortcut;
			FreshRSS_Context::$user_conf->save();
		}
	}
}
