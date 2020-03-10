<?php

// Import TEXT and convert to HTML


date_default_timezone_set('Europe/London');
mb_internal_encoding("UTF-8");


$filename = '';
if ($argc < 2)
{
	echo "Usage: textToHtml.php <input file>\n";
	exit(1);
}
else
{
	$filename = $argv[1];
}

$html = file_get_contents($filename);

$basename = str_replace('.txt', '', $filename);

$text = file_get_contents($filename);

$text = str_replace("\n", '<br />', $text);

$pages = explode("\f", $text);

$html = '<p>' . join('</p><p>', $pages) . '</p>';

// http://stackoverflow.com/a/2671410/9684
$html = mb_convert_encoding($html, 'utf-8', mb_detect_encoding($html));

// if you have not escaped entities use
$html = mb_convert_encoding($html, 'html-entities', 'utf-8'); 

$dom = new DOMDocument('1.0', 'UTF-8');

// http://stackoverflow.com/questions/6090667/php-domdocument-errors-warnings-on-html5-tags
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

$dom->formatOutput = true;
$dom->encoding = 'UTF-8';

//file_put_contents($basename . '.html', $dom->saveHTML());

echo $dom->saveHTML();





?>