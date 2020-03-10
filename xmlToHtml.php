<?php

// PDFTOXML XML to simple HTML (no coordinates)

$filename = '';
if ($argc < 2)
{
	echo "Usage: xmlToHtml.php <input file>\n";
	exit(1);
}
else
{
	$filename = $argv[1];
}

$xml = file_get_contents($filename);
				
$dom= new DOMDocument;
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
				
$pages = $xpath->query ('//PAGE');
foreach($pages as $page)
{

	$blocks = $xpath->query ('BLOCK', $page);
	foreach($blocks as $block)
	{

		echo '<p>';

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
						
				$token->bold 		= $attributes['bold'] == 'yes' ? true : false;
				$token->italic 		= $attributes['italic'] == 'yes' ? true : false;
				$token->font_size 	= $attributes['font-size'];
				$token->font_name	= $attributes['font-name'];			
				$token->text 		= $text_token->firstChild->nodeValue;
			
				if ($token->italic && !$italic)
				{
					echo '<i>';
					$italic = true;
				}
				if (!$token->italic && $italic)
				{
					echo '</i>';
					$italic = false;
				}
				
				// 
			
		
				echo $token->text . "\n";
		
			}
		
			if ($italic)
			{
				echo '</i>';
			}
		
		
			echo '<br />';
	
		}
	
		echo '</p>';
	}	
}

