<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        {literal}
        <style type="text/css">
        	.photos {
        		float: left;
        		text-align: center;
        	}
        </style>
        {/literal}
    </head>
    <body>
    <h1>your Image</h1>
    <form action="mosaic.php" >
    	{foreach from=$urls item=url key=key}
			<div class="photos">
				<img src="{$url}" /><br />
				<input type="radio" name="photo" value="{$key}" id="photo"/>
			</div>
		{/foreach}
		<div>
		<input type="submit" value="この画像をモザイクアートにする！"/>
		</div>
    </form>

    </body>
</html>