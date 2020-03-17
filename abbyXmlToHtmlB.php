<?php

// ABBY XML to simple HTML 


$filename = '';
if ($argc < 2)
{
	echo "Usage: abbyXmlToHtmlB.php <input file>\n";
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
	/ *border:1px solid black; */
	
}

.block {
	position:absolute;
	background-color:yellow;
	border:1px solid black;
	
}

.line {
	position:absolute;
	color:rgba(19,19,19,1);
	vertical-align:baseline;
	text-align: justify;
}

.line:after {
      content: "";
      display: inline-block;
      width: 100%;
    }

</style>
</head>
<body>';

/*

<document xmlns="http://www.abbyy.com/FineReader_xml/FineReader10-schema-v1.xml" version="1.0" producer="ABBYY FineReader Engine 11" languages="" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.abbyy.com/FineReader_xml/FineReader10-schema-v1.xml http://www.abbyy.com/FineReader_xml/FineReader10-schema-v1.xml">


<page width="6300" height="9000" resolution="600" originalCoords="1">
<block blockType="Text" blockName="" l="876" t="526" r="3074" b="752"><region><rect l="876" t="526" r="3074" b="752"/></region>
<text>
<par rightIndent="30400" lineSpacing="1440" style="{00000064-00DD-521B-A040-33CDE06B03B9}">
<line baseline="618" l="895" t="539" r="3065" b="641"><formatting lang="EnglishUnitedStates" ff="Default Metrics Font" fs="11" italic="1" color="16777215" style="{00000068-00DD-523E-A040-49A56A81C904}">
*/


$xml = file_get_contents($filename);

$xml = str_replace(' xmlns="http://www.abbyy.com/FineReader_xml/FineReader10-schema-v1.xml" version="1.0" producer="ABBYY FineReader Engine 11" languages="" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.abbyy.com/FineReader_xml/FineReader10-schema-v1.xml http://www.abbyy.com/FineReader_xml/FineReader10-schema-v1.xml"', '', $xml);
				
$dom= new DOMDocument;
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);


				
$pages = $xpath->query ('//page');
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
	
	$scale = 600/$w;
	$w = 600;
	$h = $scale * $h;
	
	$page_obj = new stdclass;
	$page_obj->w =$w;
	$page_obj->h =$h;
	
	
	// transform: scale(0.3);
	
	echo '<div class="page" style="position:relative;margin:10px;top:0px;left:0px;width:' . $w . 'px;height:' . $h . 'px;">';
	


// <block blockType="Text" blockName="" l="876" t="526" r="3074" b="752">

	$blocks = $xpath->query ('block', $page);
	foreach($blocks as $block)
	{
	
		// coordinates
		if ($block->hasAttributes()) 
		{ 
			$attributes = array();
			$attrs = $block->attributes; 
		
			foreach ($attrs as $i => $attr)
			{
				$attributes[$attr->name] = $attr->value; 
			}
		}
	
		$x = $attributes['l'];
		$y = $attributes['t'];
		$w = $attributes['r'] - $attributes['l'];
		$h = $attributes['b'] - $attributes['t'];
	
		$x *= $scale;
		$y *= $scale;
		$w *= $scale;
		$h *= $scale;
		
		$blockType = 'UNKNOWN';		
				
		//print_r($attributes);				
				
		switch ($attributes['blockType'])	
		{
			case 'Picture':
				$blockType = 'PICTURE';
				break;
		
			case 'Table':
				$blockType = 'TABLE';
				break;		
		
			case 'Text':
			default:
				$blockType = 'TEXT';
				break;
		}
		
		//echo '<b>' . $blockType . '</b>';
		
		$show_block = true;
		
		// ignore block that covers whole page
		if (
			(max (abs($page_obj->w - $w), 5) == 5)
			&& (max (abs($page_obj->h - $h), 5) == 5)
			)
			{
			$show_block = false;	
		}
		
		if ($show_block)
		{
	
			switch ($blockType)	
			{
				case 'PICTURE':
				case 'TABLE':
					echo '<div class="block" style="position:absolute;top:' . $y . 'px;left:' . $x . 'px;width:' . $w . 'px;height:' . $h . 'px;"></div>';
					break;		
		
				case 'TEXT':
				default:
					break;
			}
		}		
		
		
	
		// transform: scale(0.3);
	
		//echo '<div class="block" style="position:absolute;top:' . $y . 'px;left:' . $x . 'px;width:' . $w . 'px;height:' . $h . 'px;">';
		
		
		/*
<block blockType="Text" blockName="" l="876" t="526" r="3074" b="752"><region><rect l="876" t="526" r="3074" b="752"/></region>
<text>
<par rightIndent="30400" lineSpacing="1440" style="{00000064-00DD-521B-A040-33CDE06B03B9}">
<line baseline="618" l="895" t="539" r="3065" b="641"><formatting lang="EnglishUnitedStates" ff="Default Metrics Font" fs="11" italic="1" color="16777215" style="{00000068-00DD-523E-A040-49A56A81C904}">
<charParams l="895" t="541" r="969" b="619" wordFirst="1" wordLeftMost="1" wordFromDictionary="1" wordNormal="1" wordNumeric="0" wordIdentifier="0" charConfidence="100" serifProbability="50" wordPenalty="0" meanStrokeWidth="96">G</charParams>
*/		


		$lines = $xpath->query ('text/par/line', $block);
		foreach($lines as $line)
		{
	
			// coordinates
			if ($line->hasAttributes()) 
			{ 
				$attributes = array();
				$attrs = $line->attributes; 
		
				foreach ($attrs as $i => $attr)
				{
					$attributes[$attr->name] = $attr->value; 
				}
			}
	
			$x = $attributes['l'];
			$y = $attributes['t'];
			$w = $attributes['r'] - $attributes['l'];
			$h = $attributes['b'] - $attributes['t'];
	
			$x *= $scale;
			$y *= $scale;
			$w *= $scale;
			$h *= $scale;
	
			// transform: scale(0.3);
	
			echo '<div class="line" style="'

					. 'font-size:' . $h . 'px;'
					  
			
			
			. 'top:' . $y . 'px;left:' . $x . 'px;width:' . $w . 'px;height:' . $h . 'px;">' . "\n";
			
			// characters
			$formats = $xpath->query ('formatting', $line);
			foreach($formats as $format)
			{
				
				if ($format->hasAttributes()) 
				{ 
					$attributes = array();
					$attrs = $format->attributes; 
		
					foreach ($attrs as $i => $attr)
					{
						$attributes[$attr->name] = $attr->value; 
					}
				}
				
				$italic 		= $attributes['italic'] == 1 ? true : false;
				
				
				if ($italic)
				{
					echo '<i>';
				}
				
			
				$text = '';
			
				foreach ($xpath->query('charParams', $format) as $charParam)
				{
					$text .= $charParam->firstChild->nodeValue;
				}
				
				echo $text;
				
				if ($italic)
				{
					echo '</i>';
				}
				
				
				//echo '</span>';
			}

			
			
			
		
			echo '</div>' . "\n";
		} 
	
	}

	// close page	
	echo '</div>';
		
}

echo '</body>
</html>';


