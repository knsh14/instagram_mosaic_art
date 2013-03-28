<?php

session_start();
$url =  $_SESSION["user_image_urls"][intval($_REQUEST["photo"])];
//まずは受け取った画像URLから画像を取得する
//$handle = fopen('http://distilleryimage1.s3.amazonaws.com/83465e2e828411e29b2022000a9f1561_7.jpg', 'rb');
$handle = fopen($url, 'rb');

//ここら辺は画像を加工するための準備
$image = new Imagick();
$image->readImageFile($handle);
$image->resizeImage(612, 612, 0, 0); 
$draw = new ImagickDraw();
$nimage = new Imagick();
$nimage->newImage( 612,612, new ImagickPixel( 'lightgray' ) );
$size = 12;//612ｐｘの画像を51分割するとこれくらいになる計算量，画像のサイズのバランスを考えるとこれくらいが妥当
$sq_size = $size*$size;

$mosaic_array_row = array();//RGB値の縦を保存しておく配列
$mosaic_array_col = array();//縦に保存されたやつを保存しておく
/*
 * 1 2 3 4 5
 * 6 7 8.....
 * こんな順番で保存されていく
 */
//モザイク加工して，それぞれのエリアのRGB値の配列を作る
for($y = 0; $y < 612;$y+=$size){

    for($x = 0; $x < 612; $x+=$size){
        
        $blue = 0;
        $green = 0;
        $red = 0;
               
		//分割した範囲の色情報の平均を求める
        for($i = 0;$i < $size;$i++){
            for($j = 0; $j < $size; $j++){
                $pixel = $image->getImagePixelColor($x+$i, $y+$j);  
                $colors = $pixel->getColor();
                $blue += $colors["b"];
                $green += $colors["g"];
                $red += $colors["r"];
            }
        }
        
        $average = array("r" => round($red/$sq_size), "g" => round($green/$sq_size), "b" => round($blue/$sq_size));
		
		array_push($mosaic_array_row,$average);
    }
	array_push($mosaic_array_col, $mosaic_array_row);
	$mosaic_array_row = array();
}



//そのRGB値ごとに適切な画像を取得して，それにあう画像を取得する

//SQL文はこんな感じ
/*
 * 
SELECT * 
FROM  popular_image 
WHERE color_r BETWEEN color_r-5 AND color_r+5 AND color_g BETWEEN color_g-5 AND color_g+5 AND color_b BETWEEN color_b-5 AND color_b+5
ORDER BY POWER( 10, 129 -  `color_r` ) + POWER( 10, 100 -  `color_g` ) + POWER( 10, 35 -  `color_b` ) , likes
 */
//まずはデータベースに接続できるようにする 
$db = new PDO('mysql:host=localhost;dbname=database name', 'user name', 'user password', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

//出力用のURLを格納しておくための2次元配列を準備しておく
$assign_image_array_col = array();
$assign_image_array_row = array();

foreach ($mosaic_array_col as $col) {
	foreach ($col as $rgb_info) {
		
		$color_r = intval($rgb_info["r"]);
		$color_g = intval($rgb_info["g"]);
		$color_b = intval($rgb_info["b"]);
		$stmt = $db -> query("SELECT * 
							  FROM  popular_image 
							  WHERE ".$color_r." BETWEEN ".$color_r."-5 AND ".$color_r."+5 AND ".$color_g." BETWEEN ".$color_g."-5 AND ".$color_g."+5 AND ".$color_b." BETWEEN ".$color_b."-5 AND ".$color_b."+5
							  ORDER BY POWER( 10, ABS(".$color_r." -  color_r )) + POWER( 10, ABS(".$color_g." -  color_g )) + POWER( 10, ABS(".$color_b." - color_b )) , likes");
		//確認のための出力echo "SELECT * FROM  popular_image WHERE ".$color_r." BETWEEN ".$color_r."-5 AND ".$color_r."+5 AND ".$color_g." BETWEEN ".$color_g."-5 AND ".$color_g."+5 AND ".$color_b." BETWEEN ".$color_b."-5 AND ".$color_b."+5 ORDER BY POWER( 10, ".$color_r." -  color_r ) + POWER( 10, ".$color_g." -  color_g ) + POWER( 10, ".$color_b." - color_b ) , likes";
		$image_chosen = $stmt -> fetch(PDO::FETCH_ASSOC);
		array_push($assign_image_array_row,$image_chosen["url"]);
	}
	array_push($assign_image_array_col, $assign_image_array_row);
	$assign_image_array_row = array();
}



//それを並べる(多分実質はテンプレートに流して，そっちで描画すると思う)


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
//
$objSmarty->assign("images", $assign_image_array_col);
$objSmarty->assign("raw_image", $url);

$objSmarty->display('mosaic.tpl');

?>
