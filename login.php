<?php

$client_id = "9514285b99a94f4881dc0a166cef4030";
$client_secret = "be02313e95c24e92bcf934d6b71f5db3";

session_start();

if (empty($_GET['code'])) {
    // 認証前の準備
    $params = array(
        'client_id' => $client_id,
        'redirect_uri' => 'http://133.2.195.66/kamata/mosaic_art/login.php',
        'scope' => 'basic',
        'response_type' => 'code'
    );
    $url = 'https://api.instagram.com/oauth/authorize/?'.http_build_query($params);
    
    // instagramへ飛ばす
    header('Location: '.$url);
    exit;
} else {
    // 認証後の処理
    // user情報の取得
    $params = array(
        'client_id' => $client_id,
        'client_secret' => $client_secret ,
        'code' => $_GET['code'],
        'redirect_uri' => 'http://133.2.195.66/kamata/mosaic_art/login.php',
        'grant_type' => 'authorization_code'
    );
    $url = "https://api.instagram.com/oauth/access_token";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    $res = curl_exec($curl);
    curl_close($curl);
    $json = json_decode($res);
    
     var_dump($res);
	 
	 $_SESSION['user_info'] = $json;
    
	header('Location: show_images.php');
}