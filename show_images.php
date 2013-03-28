<?php

session_start();

$user_info = $_SESSION["user_info"];

$images = json_decode(file_get_contents("https://api.instagram.com/v1/users/self/media/recent?access_token=".$user_info->access_token));

//Smartyの設定ここから
// Smartyパス設定
define('SMARTY_PATH', '/var/smarty/');
define('SMARTY_TEMPLATES_DIR', SMARTY_PATH . 'templates/instagram/');
define('SMARTY_COMPIlE_DIR', SMARTY_PATH . 'templates_c/');
define('SMARTY_CACHE_DIR', SMARTY_PATH . 'chache/');

// インスタンス生成
require_once(SMARTY_PATH . 'libs/Smarty.class.php');
$objSmarty = new Smarty();

// ディレクトリの指定
$objSmarty->template_dir = SMARTY_TEMPLATES_DIR;
$objSmarty->compile_dir = SMARTY_COMPIlE_DIR;
$objSmarty->cache_dir = SMARTY_CACHE_DIR;
//$objSmarty->debugging = TRUE;
//ここまで

//var_dump($images);

$image_thumbnail_url_list = array();//サムネイルサイズの画像の配列
$image_big_size_url_list = array();//実際に加工するときに使う大きめサイズの画像の配列
foreach ($images->data as $image) {
	array_push($image_thumbnail_url_list, $image->images->thumbnail->url);
	array_push($image_big_size_url_list, $image->images->standard_resolution->url);
}

$objSmarty->assign('urls', $image_thumbnail_url_list);
$_SESSION["user_image_urls"] = $image_big_size_url_list;
// テンプレート出力
$objSmarty->display('show_image.tpl');
