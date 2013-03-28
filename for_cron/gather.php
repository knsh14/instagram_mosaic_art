<?php
		$db = new PDO('mysql:host=localhost;dbname=database name', 'user name', 'user password', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		$stmt = $db->prepare('INSERT INTO popular_image (id, url, color_r,color_g,color_b,likes) VALUES (?,?,?,?,?,?)');
		$cxContext = NULL;
		define('CLIENT_ID', 'client id for this application');//実際はこの二つはいらなかった
		define('CLIENT_SECRET', 'client secret for this application');

		$content = json_decode(file_get_contents("https://api.instagram.com/v1/media/popular?client_id=client id for this application", False, $cxContext));
		foreach ($content->data as $value) {
			$url = $value -> images -> standard_resolution -> url;
			$likes = $value -> likes -> count;
            $stmt->execute(array(NULL, $url, 0, 0, 0, $likes));
		}
