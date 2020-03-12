<?php

// Extract possible specimen codes, GenBank accessions, herbarium collector codes, etc.

//------------------------------------------------------------------------------
// Series of regular expressions to extract possibe specimen codes from text
// Note that we don't use PREG_OFFSET_CAPTURE as it gives incorrect values
// for some strings (depending on encoding), so we compute positions ourselves.
function find_codes($text)
{
	$INSTITUTION_CODE 					= '([A-Z]{3,10}(-[A-Z]{1,2})?|QM|BM|BM\(NH\))';
	$CATALOGUE_NUMBER_PREFIX			= '([A-Z][\.|\-]?)?';
	$CATALOGUE_NUMBER					= $CATALOGUE_NUMBER_PREFIX . '[0-9]{3,}';
	$CATALOGUE_NUMBER_SUFFIX_DELIMITER	= '(\-|–|­|—|\.)';
	$CATALOGUE_NUMBER_SUFFIX			= '[0-9]{1,}((\.\d+)+)?';
	
	$BARCODE							= '[A-Z]+[\.|-]?\d+';
	
	$flanking_length = 50;
	
	$results = array();	
	
	$patterns = array(
		"/
		(?<code>
		$INSTITUTION_CODE
		\s*
		(?<catalogue>$CATALOGUE_NUMBER)
		(
			$CATALOGUE_NUMBER_SUFFIX_DELIMITER
			(?<extension>$CATALOGUE_NUMBER_SUFFIX)			
		)?
		)
		/x", 
		
		"/\[$BARCODE(,\s+$BARCODE)*\]/",
		
		"/PBI_OON \d+/",
		
		"/AM KS\.\d+/"
	
	);
	
	foreach ($patterns as $pattern)
	{
		if (preg_match_all(
			$pattern, $text, $matches, PREG_SET_ORDER))
		{
			//print_r($matches);
	
		
			$last_pos = 0;
		
			foreach ($matches as $match)
			{
		
				// filter out obvious false hits
				$ok = true;
			
				if (preg_match('/^ISSN/', $match[0]))
				{
					$ok = false;
				}

				if (preg_match('/^FIGURE/', $match[0]))
				{
					$ok = false;
				}
			
				if ($ok)
				{
		
					$hit = new stdclass;
				
					// verbatim text we have matched
					$hit->mid = $match[0];
				
					$start = mb_strpos($text, $hit->mid, $last_pos, mb_detect_encoding($text));
					$end = $start + mb_strlen($hit->mid, mb_detect_encoding($hit->mid)) - 1;
				
					// update position so we don't find this point again
					$last_pos = $end;
				
					$hit->range = array($start, $end);
				
					$pre_length = min($start, $flanking_length);
					$pre_start = $start - $pre_length;
				
					$hit->pre = mb_substr($text, $pre_start, $pre_length, mb_detect_encoding($text)); 
				
	
					$post_length = 	min(mb_strlen($text, mb_detect_encoding($text)) - $end, $flanking_length);		
				
					$hit->post = mb_substr($text, $end + 1, $post_length, mb_detect_encoding($text)); 
				
				
				
					$results[] = $hit;
				}
			}
		}
	}
	
	return $results;
}


//----------------------------------------------------------------------------------------
// Find integers which may be specimen codes
function find_integers($text)
{
	$flanking_length = 50;
	
	$results = array();	
	
	if (preg_match_all(
		"/
		[0-9]{4,6}
		(?<extension>[\-|–][0-9]{1,})?
		/ux",  	
		$text, $matches, PREG_SET_ORDER))
	{
		
		$last_pos = 0;
		
		foreach ($matches as $match)
		{
		
			// filter out obvious false hits
			$ok = true;
			
			if ($ok)
			{
		
				$hit = new stdclass;
				
				// verbatim text we have matched
				$hit->mid = $match[0];
				
				$start = mb_strpos($text, $hit->mid, $last_pos, mb_detect_encoding($text));
				$end = $start + mb_strlen($hit->mid, mb_detect_encoding($hit->mid)) - 1;
				
				// update position so we don't find this point again
				$last_pos = $end;
				
				$hit->range = array($start, $end);
				
				$pre_length = min($start, $flanking_length);
				$pre_start = $start - $pre_length;
				
				$hit->pre = mb_substr($text, $pre_start, $pre_length, mb_detect_encoding($text)); 
				
	
				$post_length = 	min(mb_strlen($text, mb_detect_encoding($text)) - $end, $flanking_length);		
				
				$hit->post = mb_substr($text, $end + 1, $post_length, mb_detect_encoding($text)); 
				
				
				
				$results[] = $hit;
			}
		}
	}
	
	return $results;
}

// Extract possible GenBank codes


//----------------------------------------------------------------------------------------
// Series of regular expressions to extract possibe accession number from text
// Note that we don't use PREG_OFFSET_CAPTURE as it gives incorrect values
// for some strings (depending on encoding), so we compute positions ourselves.
function find_accession_numbers($text)
{
	
	$flanking_length = 50;
	
	$results = array();	
	
	if (preg_match_all(
		"/
		[A-Z]
		[A-Z]?
		[0-9]{5,6}
		(?<extension>[\-|–][0-9]{1,})?
		/ux",  	
		$text, $matches, PREG_SET_ORDER))
	{
		
		$last_pos = 0;
		
		foreach ($matches as $match)
		{
		
			// filter out obvious false hits
			$ok = true;
			
			if (preg_match('/^[R|W]/', $match[0]))
			{
				$ok = false;
			}
			
			
			if ($ok)
			{
		
				$hit = new stdclass;
				
				// verbatim text we have matched
				$hit->mid = $match[0];
				
				$start = mb_strpos($text, $hit->mid, $last_pos, mb_detect_encoding($text));
				$end = $start + mb_strlen($hit->mid, mb_detect_encoding($hit->mid)) - 1;
				
				// update position so we don't find this point again
				$last_pos = $end;
				
				$hit->range = array($start, $end);
				
				$pre_length = min($start, $flanking_length);
				$pre_start = $start - $pre_length;
				
				$hit->pre = mb_substr($text, $pre_start, $pre_length, mb_detect_encoding($text)); 
				
	
				$post_length = 	min(mb_strlen($text, mb_detect_encoding($text)) - $end, $flanking_length);		
				
				$hit->post = mb_substr($text, $end + 1, $post_length, mb_detect_encoding($text)); 
				
				
				
				$results[] = $hit;
			}
		}
	}
	
	return $results;
}



if (0)
{
	$text = 'Fianar­antsoa Province, 17-29 November 1993, N. Rabibisoa, C. J. Raxworthy, and A. Razafimanantsoa; UMMZ 2149273­4, Andohahela National Park (as UADBA 4124-35);';
	
	$text = 'Diastema fimbratiloba differs from all other known congeners by the presence
of fimbriations on the lower corolla lobe. – Type: Peru, Ucayali Region, Coronel
Portillo Province, c.500 m beyond Margariti on path from Divisora pass, 1630 m, 09°09′
54′′S, 75°47′59′′W, 7 ii 2016, P.W. Moonlight & A. Daza 197 (holo MOL; iso
E [E00885503], MO, USM). Figs 2, 3.';
	$results = find_codes($text);
	print_r($results);
}

?>