<?php

// Read (simplified) HTML and add entity markup

require_once(dirname(__FILE__) . '/collector.php');
require_once(dirname(__FILE__) . '/utils.php');

$filename = '';
if ($argc < 2)
{
	echo "Usage: htmlMarkup.php <input file>\n";
	exit(1);
}
else
{
	$filename = $argv[1];
}

$html = file_get_contents($filename);


// http://stackoverflow.com/a/2671410/9684
$html = mb_convert_encoding($html, 'utf-8', mb_detect_encoding($html));
// if you have not escaped entities use
$html = mb_convert_encoding($html, 'html-entities', 'utf-8'); 

$dom = new DOMDocument('1.0', 'UTF-8');

// http://stackoverflow.com/questions/6090667/php-domdocument-errors-warnings-on-html5-tags
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

$document = new stdclass;
$document->nodes = new stdclass;

// house keeping
$document->counter = 0;
$document->node_type_counter = array();
$document->current_paragraph_node = null;
$document->current_text_node = null;
$document->current_page_node = null;

$document->italic_strings = array();

$counter = 0;
foreach ($dom->documentElement->childNodes as $node) {
    dive($node, $document); 
}



// external annotations
/*          [emphasis1] => stdClass Object
                (
                    [type] => emphasis
                    [id] => emphasis1
                    [range] => Array
                        (
                            [0] => 1
                            [1] => 23
                        )

                    [path] => Array
                        (
                            [0] => paragraph_1
                            [1] => content
                        )

                )
                */
/*
$annotation = new stdclass;
$annotation->type = "name";
$annotation->id = "name1";
$annotation->range = array(41,55);

$document->nodes->{$annotation->id} = $annotation;
add_annotation($document, $annotation);	
*/


if (0)
{
	echo '<pre>';
	print_r($document);
	echo '</pre>';
}


// list everything in italics

echo '<i>Italics</i><br/>';
foreach ($document->nodes as $node)
{
	if ($node->type == 'emphasis')
	{
		$text = $document->nodes->{$node->path[0]}->{$node->path[1]};
		$substring = mb_substr(
			$text,
			$node->range[0],
			$node->range[1] - $node->range[0],
			mb_detect_encoding($text)); 
			
		echo $substring . '|<br />';
	}

}

// add annotations for entities that may be flagged by italics, etc.
italics_collector_code($document);


// remove housekeeping
unset($document->counter);
unset($document->node_type_counter);
unset($document->current_paragraph_node);
unset($document->current_text_node);
unset($document->current_page_node);
unset($document->current_node);

if (0)
{
	echo '<pre>';
	print_r($document);
	echo '</pre>';
}

if (0)
{
	echo '<pre>';
	echo json_encode($document);
	echo '</pre>';
}

if (1)
{
	$html = to_html($document, true, false);
	echo $html;
}

if (1)
{
	// Dump list of annotations
	echo '<h1>Annotations</h1>';
	
	echo '<h2>Occurences</h2>';
	echo '<ul>';
	foreach ($document->nodes as $node)
	{
		// specimen
		if ($node->type == 'occurrence')
		{
			echo '<li>' . $node->mid . '</li>';
		
		}
	}
	echo '</ul>';
	
	echo '<h2>Points</h2>';
	echo '<ul>';	
	foreach ($document->nodes as $node)
	{

		// point location
		if ($node->type == 'point')
		{
			echo '<li>' . $node->mid . '</li>';
		
		}

	}
		
	echo '</ul>';
	



}



?>