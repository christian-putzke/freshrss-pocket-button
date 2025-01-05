<?php

return array(
	'pocketButton' => array(
		'configure' => array(
			'consumer_key' => 'Consumer Key',
			'consumer_key_description' => '<ul class="pb_listedNumbers">
				<li>Go to <a href="https://getpocket.com/developer/apps/" target="_blank">Pocket\'s Developer Portal</a></li>
				<li>Create an Application with at least the \'Add\' permission</li>
				<li>Enter your Consumer Key and hit "Connect to Pocket"</li>
			</ul>
			<span>Details can be found on <a href="https://github.com/christian-putzke/freshrss-pocket-button" target="_blank">GitHub</a>!',
			'connect_to_pocket' => 'Connect to Pocket',
			'username' => 'Username',
			'access_token' => 'Access Token',
			'keyboard_shortcut' => 'Keyboard shortcut',
			'extension_disabled' => 'You need to enable the extension before you can connect to Pocket!',
			'connected_to_pocket' => 'Your are connected to Pocket with the account <b>%s</b> using the access token <b>%s</b>.',
			'revoke_access' => 'Disconnect from Pocket!'
		),
		'notifications' => array(
			'added_article_to_pocket' => 'Successfully added <i>\'%s\'</i> to Pocket!',
			'failed_to_add_article_to_pocket' => 'Adding article to Pocket failed! Pocket API error code: %s',
			'ajax_request_failed' => 'Ajax request failed!',
			'authorized_success' => 'Authorization successful!',
			'authorized_aborted' => 'Authorization aborted!',
			'authorized_failed' => 'Authorization failed! Pocket API error code: %s',
			'request_access_failed' => 'Access request failed! Pocket API error code: %s',
			'article_not_found' => 'Can\'t find article!',
		)
	),
);
