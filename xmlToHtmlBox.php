<?php

// PDFTOXML XML to simple HTML (no coordinates)


$filename = '';
if ($argc < 2)
{
	echo "Usage: xmlToHtmlB.php <input file>\n";
	exit(1);
}
else
{
	$filename = $argv[1];
}


echo '<html>
<head>
<style>
body {
	background:rgb(228,228,228);
}
.page {
	background-color:white;
}
</style>
</head>
<body>';


$xml = file_get_contents($filename);
				
$dom= new DOMDocument;
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
				
$pages = $xpath->query ('//PAGE');
foreach($pages as $page)
{

	// coordinates
	if ($page->hasAttributes()) 
	{ 
		$attributes = array();
		$attrs = $page->attributes; 
		
		foreach ($attrs as $i => $attr)
		{
			$attributes[$attr->name] = $attr->value; 
		}
	}
	
	$x = 0;
	$y = 0;
	$w =  $attributes['width'];
	$h =  $attributes['height'];
	
	echo '<div class="page" style="position:relative;margin:10px;top:0px;left:0px;width:' . $w . 'px;height:' . $h . 'px;">';
	
	// images (figures) from born native PDF
	if (1)
	{
		$images = $xpath->query ('IMAGE', $page);
		foreach($images as $image)
		{
			// coordinates
			if ($image->hasAttributes()) 
			{ 
				$attributes = array();
				$attrs = $image->attributes; 
				
				foreach ($attrs as $i => $attr)
				{
					$attributes[$attr->name] = $attr->value; 
				}
			}
			
			// ignore block x=0, y=0 as this is the whole page(?)
			if (($attributes['x'] != 0) && ($attributes['y'] != 0))
			{
		
				
				echo '<div style="position:absolute;' 
					. 'left:' . $attributes['x'] . 'px;'
					. 'top:' . $attributes['y'] . 'px;'
					. 'width:' . $attributes['width'] . 'px;'
					. 'height:' . $attributes['height'] . 'px;'
					. 'background-color:orange;">';
					
					
				echo '<img src="' . $attributes['href']	. '"'
					. 'width="' . $attributes['width'] . '"'
					. '>';				
				
					
				echo '</div>';

			}		
		}
	}
		
		

	$blocks = $xpath->query ('BLOCK', $page);
	foreach($blocks as $block)
	{

		//echo '<p>';

		$texts = $xpath->query ('TEXT', $block);
		foreach($texts as $text)
		{
			$italic = false;
	
	
			$text_tokens = $xpath->query ('TOKEN', $text);
			foreach($text_tokens as $text_token)
			{
		
				// attributes
				if ($text_token->hasAttributes()) 
				{ 
					$attributes = array();
					$attrs = $text_token->attributes; 
		
					foreach ($attrs as $i => $attr)
					{
						$attributes[$attr->name] = $attr->value; 
					}
				}
		
				$token = new stdclass;
				$token->type = 'token';
				
				$token->rotation 	= $attributes['rotation'] == '1' ? true : false;
				$token->angle 		= $attributes['angle'];
				
				$span_style = '';
				
				if ($token->rotation)
				{
					if ($token->angle == 90)
					{
						$span_style = 'writing-mode: vertical-rl;transform: rotate(-180deg);';
					}
				
				}
				
						
				$token->bold 		= $attributes['bold'] == 'yes' ? true : false;
				$token->italic 		= $attributes['italic'] == 'yes' ? true : false;
				$token->font_size 	= $attributes['font-size'];
				$token->font_name	= $attributes['font-name'];			
				$token->text 		= $text_token->firstChild->nodeValue;
			
				if ($token->italic && !$italic)
				{
					//echo '<i>';
					$italic = true;
				}
				if (!$token->italic && $italic)
				{
					//echo '</i>';
					$italic = false;
				}
	
				$x = $attributes['x'];
				$y = $attributes['y'];
				$w = $attributes['width'];
				$h = $attributes['height'];		
				
				/*
				<TOKEN sid="p8_s105233" id="p8_w125" font-name="phjhgxadvttf0d81159" symbolic="yes" fixed-width="yes" bold="no" italic="no" font-size="8.4682" font-color="#131313" rotation="1" angle="90" x="104.366" y="554.296" base="110.717" width="8.18028" height="23.3807">1240”</TOKEN>
				
				
				*/		
				
				echo '<div style="position:absolute;' 
					. 'left:' . $x . 'px;'
					. 'top:' . $y . 'px;'
					. 'width:' . $w . 'px;'
					. 'height:' . $h . 'px;'
					
					//. 'background-color:rgb(228,228,228);'					
					
					. '">';
			
				echo '<span style="font-size:' . $token->font_size . 'px;'
					. 'rgba(19,19,19,1);'
					. 'vertical-align:baseline;'
					. 'white-space:nowrap;'

					. $span_style

					. '">';
	
				echo $token->text;
				
				echo '</span>';
				
				echo '</div>';
		
			}
		
			if ($italic)
			{
				//echo '</i>';
			}
		
		
			//echo '<br />';
	
		}
	
		//echo '</p>';
	}
	
	echo '</div>';
		
}

echo '</body>
</html>';


