<?php

// Extract herbarium specimen collector codes from text where part or all of the
// code is in italics

require_once(dirname(__FILE__) . '/collector.php');

function italics_collector_code(&$document)
{
	// Get each text string that is in italics
	
	foreach ($document->nodes as $node)
	{
		if ($node->type == 'emphasis')
		{
			$flanking_length = 32;
		
			// must set this for annotation to be added 
			
			$document->current_paragraph_node = $document->nodes->{$node->path[0]};
		
			// text string the emphasis refers to
			$text = $document->nodes->{$node->path[0]}->{$node->path[1]};
		
			// text in italics
			$italic_string = mb_substr(
				$text,
				$node->range[0],
				$node->range[1] - $node->range[0],
				mb_detect_encoding($text)); 
		
			$substring = mb_substr(
				$text,
				$node->range[0],
				null,
				mb_detect_encoding($text)); 
			
			$pattern = $italic_string;
		
			$pattern = preg_replace('/[\(|\|\)|\/]/u', '\\$1', $pattern);
				
			$COLLECTOR_NUMBER = '\s+\d+';	
			$COLLECTION		  = '(\s+\([^\)]+\))';	
		
			if (preg_match('/' . $COLLECTOR_NUMBER . '$/u', $pattern))
			{
				$pattern .= $COLLECTION	. '?';
			}
			else
			{
				$pattern .= $COLLECTOR_NUMBER . $COLLECTION	. '?';
			}
		
			//echo '<b>' . $pattern . '|</b><br />';
			
			if (preg_match('/' . $pattern . '/u', $substring, $match))
			{
				// try and screen out spurious matches
				
				$ok = false;
			
				// do we have an auhtor's name with initials?
				if (preg_match('/[A-Z]\./u', $match[0]))
				{
					$ok = true;
				}
			
				if ($ok)
				{
					//echo $match['0'] . '<br />';
								
					// hit
				
					$hit = new stdclass;
			
					// verbatim text we have matched
					$hit->mid = $match[0];
			
					$start = $node->range[0];
					$end = $start + mb_strlen($hit->mid, mb_detect_encoding($hit->mid)) - 1;			
			
					$hit->range = array($start, $end);
			
					$pre_length = min($start, $flanking_length);
					$pre_start = $start - $pre_length;
			
					$hit->pre = mb_substr($text, $pre_start, $pre_length, mb_detect_encoding($text)); 
			
					$post_length = 	min(mb_strlen($text, mb_detect_encoding($text)) - $end, $flanking_length);		
			
					$hit->post = mb_substr($text, $end + 1, $post_length, mb_detect_encoding($text)); 
			
			
					$annotation = new_annotation($document, 'occurrence', false);
					$annotation->range = $hit->range;
					$annotation->pre = $hit->pre;
					$annotation->mid = $hit->mid;
					$annotation->post = $hit->post;
				
					add_annotation($document, $annotation);	
				
				
				
				
				}	
			
			}
		}

	}
}

?>
