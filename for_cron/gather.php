<?php
		$db = new PDO('mysql:host=localhost;dbname=klab', 'root', 'kaho1018', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		$stmt = $db->prepare('INSERT INTO popular_image (id, url, color_r,color_g,color_b,likes) VALUES (?,?,?,?,?,?)');
		$cxContext = NULL;
		define('CLIENT_ID', 'c0eabe7586214e62a05402e7b72f532b');
		define('CLIENT_SECRET', '0c792e69ced5439ba6ca86c8c4ce5258');

		$content = json_decode(file_get_contents("https://api.instagram.com/v1/media/popular?client_id=c0eabe7586214e62a05402e7b72f532b", False, $cxContext));
		foreach ($content->data as $value) {
			$url = $value -> images -> standard_resolution -> url;
			$likes = $value -> likes -> count;
            $stmt->execute(array(NULL, $url, 0, 0, 0, $likes));
		}