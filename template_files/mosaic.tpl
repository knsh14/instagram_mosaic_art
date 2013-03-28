<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        {literal}
        <style type="text/css">
        	img.mosaic {
        		width: 12px;
        		height: 12px
        	}
        	img.mosaic:hover {
        		width: 100px;
        		height: 100px
        	}
        </style>
        {/literal}
    </head>
    <body>
    <h1>モザイク イメージ</h1>
	{foreach item=contact from=$images}
  		{foreach item=image from=$contact}
    		<img class="mosaic" src="{$image}" />
  		{/foreach}
  		<br />
	{/foreach}
	<h1>元画像</h1>
	<img src="{$raw_image}">
	<a href="show_images.php">
    </body>
</html>