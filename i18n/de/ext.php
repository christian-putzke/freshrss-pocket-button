<?php

return array(
	'pocketButton' => array(
		'configure' => array(
			'consumer_key' => 'Verbraucher-Schlüssel',
			'consumer_key_description' => '<ul class="listedNumbers">
				<li>Gehe zum <a href="https://getpocket.com/developer/apps/" target="_blank">Pocket Entwickler-Portal</a></li>
				<li>Erstelle eine Applikation welche mindestens die \'Add\' Berechtigung beseitzt</li>
				<li>Gebe deinen Verbraucher-Schlüssel (Consumer Key) ein und drücke auf "Mit Pocket verbinden"</li>
			</ul>
			<span>Weitere Details findest auf <a href="https://github.com/christian-putzke/freshrss-pocket-button" target="_blank">GitHub</a>!',
			'connect_to_pocket' => 'Mit Pocket verbinden',
			'username' => 'Benutzername',
			'access_token' => 'Zugangs-Token',
			'keyboard_shortcut' => 'Tastaturkürzel',
			'extension_disabled' => 'Du must die Erweiterung aktivieren, bevor du dich mit Pocket verbinden kannst!',
			'connected_to_pocket' => 'Du bist über den Account <b>%s</b> mit dem Zugangs-Token <b>%s</b> mit Pocket verbunden!',
			'revoke_access' => 'Verbindung zu Pocket trennen!',
		),
		'notifications' => array(
			'added_article_to_pocket' => '<i>\'%s\'</i> erfolgreich zu Pocket hinzugefügt!',
			'failed_to_add_article_to_pocket' => 'Fehler beim hinzufügen des Artikels! Pocket API Fehlercode: %s',
			'ajax_request_failed' => 'Ajax-Anfrage fehlgeschlagen!',
			'authorized_success' => 'Autorisierung erfolgreich!',
			'authorized_aborted' => 'Autorisierung abgebrochen!',
			'authorized_failed' => 'Autorisierung fehlgeschlagen! Pocket API Fehlercode: %s',
			'request_access_failed' => 'Zugangsanfrage fehlgeschlagen! Pocket API Fehlercode: %s',
			'article_not_found' => 'Artikel konnte nicht gefunden werden!',
		)
	),
);
