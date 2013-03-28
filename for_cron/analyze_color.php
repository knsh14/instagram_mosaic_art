<?php

$max_id = file_get_contents("/var/www/kamata/mosaic_art/for_cron/max_id.txt");

$db = new PDO('mysql:host=localhost;dbname=klab', 'root', 'kaho1018', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

$urls = $db->query("SELECT id, url, likes FROM popular_image WHERE id > ".$max_id);
$content = $urls->fetchAll(PDO::FETCH_ASSOC);

$update = $db->prepare("UPDATE popular_image SET color_r = ?, color_g = ?, color_b = ? WHERE id = ?");

foreach($content as $data){
	$handle = fopen($data["url"], 'rb');
	if ($handle) {
		
		$image = new Imagick();
		$image->readImageFile($handle);
		$image->resizeImage(612, 612, 0, 0); 
		$draw = new ImagickDraw();
		$nimage = new Imagick();
		$nimage->newImage( 612,612, new ImagickPixel( 'lightgray' ) );
		$size = 612;
		$sq_size = $size*$size;
		for($x = 0; $x < 612;$x+=$size){
		    for($y = 0; $y < 612; $y+=$size){
		        
		        $blue = 0;
		        $green = 0;
		        $red = 0;
		               
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
		        var_dump($average);
				$update->execute(array($average["r"], $average["g"], $average["b"], $data["id"]));
				file_put_contents("/var/www/kamata/mosaic_art/for_cron/max_id.txt", intval($data["id"]));
		        /*$ip = new ImagickPixel();
		        $ip->setcolor($str_color);
		        
		        $draw->setFillColor($ip);    // Set up some colors to use for fill and outline
		        $draw->setStrokeColor( new ImagickPixel( 'black' ) );
		        $draw->setStrokeWidth(0);
		        $draw->rectangle( $x, $y, $x+$size, $y+$size );
		        echo "\n";*/
		    }
		}
		/*
		$nimage->drawImage( $draw );    // Apply the stuff from the draw class to the image canvas
		
		$nimage->setImageFormat('jpg');
		
		$nimage->writeimage("rola_mosaic.jpg");
		 * 
		 */
	} else {
		
	}
	
}

?>
