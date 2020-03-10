<?php

date_default_timezone_set('Europe/London');
mb_internal_encoding("UTF-8");


require_once(dirname(__FILE__) . '/geocode.php');
require_once(dirname(__FILE__) . '/specimen.php');

//--------------------------------------------------------------------------------------------------
// Clean up text so that we have single spaces between text, 
// see https://github.com/readmill/API/wiki/Highlight-locators
function clean_text($text)
{
	define ('WHITESPACE_CHARS', ' \f\n\r\t\x{00a0}\x{0020}\x{1680}\x{180e}\x{2028}\x{2029}\x{2000}\x{2001}\x{2002}\x{2003}\x{2004}\x{2005}\x{2006}\x{2007}\x{2008}\x{2009}\x{200a}\x{202f}\x{205f}\x{3000}');
	
	$text = preg_replace('/[' . WHITESPACE_CHARS . ']+/u', ' ', $text);
	
	return $text;
}


//--------------------------------------------------------------------------------------------------
// Add an annotation 
function new_annotation(&$document, $type, $store = true)
{
	if (!isset($document->node_type_counter[$type]))
	{
		$document->node_type_counter[$type] = 0;
	}
	$document->node_type_counter[$type]++;
	$id = $type . $document->node_type_counter[$type];
	$document->nodes->{$id} = new stdclass;
	$document->nodes->{$id}->type = $type;
	$document->nodes->{$id}->id = $id;
	
	$document->nodes->{$id}->range = array();
	$document->nodes->{$id}->range[0] = $document->counter;
	
	$document->nodes->{$id}->path = array();
	$document->nodes->{$id}->path[0] = $document->current_paragraph_node->id;
	$document->nodes->{$id}->path[1] = 'content';
	
	if ($store)
	{
		$document->current_node[] = $document->nodes->{$id};
	}
	
	return $document->nodes->{$id};
}

//--------------------------------------------------------------------------------------------------
// Store text span that annotation applies to
function add_annotation(&$document, $annotation)
{
	if (!isset($document->current_paragraph_node->open_annotation[$annotation->range[0]]))
	{
		$document->current_paragraph_node->open_annotation[$annotation->range[0]] = array();
	}
	
	if (!isset($document->current_paragraph_node->open_annotation[$annotation->range[0]][$annotation->range[1]]))
	{
		$document->current_paragraph_node->open_annotation[$annotation->range[0]][$annotation->range[1]] = array();
	}
	$document->current_paragraph_node->open_annotation[$annotation->range[0]][$annotation->range[1]][] = $annotation->id;

	krsort($document->current_paragraph_node->open_annotation[$annotation->range[0]], SORT_NUMERIC);
	
	asort($document->current_paragraph_node->open_annotation[$annotation->range[0]][$annotation->range[1]]);
	
	if (!isset($document->current_paragraph_node->close_annotation[$annotation->range[1]]))
	{
		$document->current_paragraph_node->close_annotation[$annotation->range[1]] = array();
	}
	
	if (!isset($document->current_paragraph_node->close_annotation[$annotation->range[1]][$annotation->range[0]]))
	{
		$document->current_paragraph_node->close_annotation[$annotation->range[1]][$annotation->range[0]] = array();
	}	
	
	$document->current_paragraph_node->close_annotation[$annotation->range[1]][$annotation->range[0]][] = $annotation->id;
	
	ksort($document->current_paragraph_node->close_annotation[$annotation->range[1]], SORT_NUMERIC);
	
	arsort($document->current_paragraph_node->close_annotation[$annotation->range[1]][$annotation->range[0]]);	
}

//--------------------------------------------------------------------------------------------------
// Find specimen codes in current document node
function find_specimen_codes(&$document)
{
	$results = find_codes($document->current_paragraph_node->content);
	
	foreach ($results as $specimen)
	{
		$annotation = new_annotation($document, 'occurrence', false);

		$annotation->range = $specimen->range;
		$annotation->pre = $specimen->pre;
		$annotation->mid = $specimen->mid;
		$annotation->post = $specimen->post;
				
		add_annotation($document, $annotation);	
	}	
}

//--------------------------------------------------------------------------------------------------
// Find GenBank acession codes in current document node
function find_genbank_accession_numbers(&$document)
{
	$results = find_accession_numbers($document->current_paragraph_node->content);
	
	foreach ($results as $specimen)
	{
		$annotation = new_annotation($document, 'genbank', false);

		$annotation->range = $specimen->range;
		$annotation->pre = $specimen->pre;
		$annotation->mid = $specimen->mid;
		$annotation->post = $specimen->post;
				
		add_annotation($document, $annotation);	
	}	
}



//--------------------------------------------------------------------------------------------------
// Find taxon names in current document node
function find_latlong(&$document)
{
	$results = find_points($document->current_paragraph_node->content);
	//$results = array();
	
	foreach ($results as $point)
	{
		$annotation = new_annotation($document, 'point', false);

		$annotation->range = $point->range;
		$annotation->pre = $point->pre;
		$annotation->mid = $point->mid;
		$annotation->post = $point->post;
		
		$annotation->feature = $point->feature;
		
		add_annotation($document, $annotation);	
	}	
}


//--------------------------------------------------------------------------------------------------
// Find taxon names in current document node
function find_taxon_names(&$document)
{
/*
	if (1)
	{
		//echo $document->current_paragraph_node->content . "\n";	
		$response = get_names_from_text($document->current_paragraph_node->content);

		// Result
		//echo "GNRD response\n";
		//print_r($response);
	}
	else
	{
		// canned example
	
		$json = '{"token_url":"http://gnrd.globalnames.org/name_finder.json?token=e4xi1MjGTFug9fjyb4ssDA","input_url":null,"file":"","status":200,"engines":["TaxonFinder","NetiNeti"],"unique":false,"verbatim":true,"english":true,"execution_time":{"find_names_duration":0.080698861,"total_duration":0.571075043},"agent":"","created":"2014-05-29T17:19:04Z","total":4,"names":[{"verbatim":"Potamotrygon boesemani","scientificName":"Potamotrygon boesemani","offsetStart":1,"offsetEnd":22,"identifiedName":"Potamotrygon boesemani"},{"verbatim":"(Chondrichthyes:","scientificName":"Chondrichthyes","offsetStart":24,"offsetEnd":39,"identifiedName":"Chondrichthyes"},{"verbatim":"Myliobatiformes:","scientificName":"Myliobatiformes","offsetStart":41,"offsetEnd":56,"identifiedName":"Myliobatiformes"},{"verbatim":"Potamotrygonidae),","scientificName":"Potamotrygonidae","offsetStart":58,"offsetEnd":75,"identifiedName":"Potamotrygonidae"}]}';
		$response = json_decode($json);
	}
	
	if (isset($response->names))
	{
		foreach ($response->names as $name)
		{
			$annotation = new_annotation($document, 'name', false);
			
			// text with name may have leading and/or trailing cruff to trim off
			
			$found_name = $name->identifiedName;
			
			$name_length =  mb_strlen($found_name, mb_detect_encoding($found_name));
			
			// trim start
			$offsetStart = $name->offsetStart;
			
			$name_start = mb_substr($found_name, 0, 1);			
			while(mb_substr($document->current_paragraph_node->content, $offsetStart, 1) != $name_start)
			{
				$offsetStart++;
			}
			// trim end
			$offsetEnd = $name->offsetEnd;
			
			$name_end = mb_substr($found_name, $name_length - 1, 1);			
			while(mb_substr($document->current_paragraph_node->content, $offsetEnd, 1) != $name_end)
			{
				$offsetEnd--;
			}
			
			$annotation->range = array($offsetStart, $offsetEnd);
			add_annotation($document, $annotation);	
		}
	}
	
	//print_r($document);
	//exit();
*/
}


//--------------------------------------------------------------------------------------------------
// A page, which may contain the entire article, or a single page
function create_page_node(&$document)
{
	if (!isset($document->node_type_counter['page']))
	{
		$document->node_type_counter['page'] = 0;
	}
	$document->node_type_counter['page']++;
	
	$id = 'page_' . $document->node_type_counter['page'];
	$document->nodes->{$id} = new stdclass;
	$document->nodes->{$id}->type = 'page';
	$document->nodes->{$id}->id = $id;	
	$document->nodes->{$id}->children = array();
		
	$document->current_page_node = $document->nodes->{$id};
}

//--------------------------------------------------------------------------------------------------
// Recursively traverse DOM and process tags
function dive($node, &$document )
{
	switch ($node->nodeName)
	{
		case 'p':
			if (!isset($document->node_type_counter['p']))
			{
				$document->node_type_counter['p'] = 0;
			}
			$document->node_type_counter['p']++;
			
			$document->counter = 0;
			
			$id = 'paragraph_' . $document->node_type_counter['p'];
			$document->nodes->{$id} = new stdclass;
			$document->nodes->{$id}->type = 'paragraph';
			$document->nodes->{$id}->id = $id;
			$document->nodes->{$id}->children=array();
			$document->nodes->{$id}->content = '';
						
			// HTML attributes
			if ($node->hasAttributes()) 
			{ 
				$attributes = $node->attributes; 
				
				foreach ($attributes as $attribute)
				{
					switch ($attribute->name)
					{
						case 'style':
							$document->nodes->{$id}->style = $attribute->value;
							break;
							
						default:
							break;
					}
				}
			}			
			
			// support for annotations
			$document->nodes->{$id}->open_annotation = array();
			$document->nodes->{$id}->close_annotation = array();			
			
			$document->current_node[] = $document->nodes->{$id};
			$document->current_paragraph_node = $document->nodes->{$id};
			
			// add paragraph to current page
			if (!$document->current_page_node)
			{
				create_page_node($document);
			}
			
			$document->current_page_node->children[] = $id;
			break;
			
		case 'img':
			if (!isset($document->node_type_counter['figure']))
			{
				$document->node_type_counter['figure'] = 0;
			}
			$document->node_type_counter['figure']++;
			
			$id = 'figure_' . $document->node_type_counter['figure'];
			$document->nodes->{$id} = new stdclass;
			$document->nodes->{$id}->type = 'figure';
			$document->nodes->{$id}->id = $id;
			
			// HTML attributes
			if ($node->hasAttributes()) 
			{ 
				$attributes = $node->attributes; 
				
				foreach ($attributes as $attribute)
				{
					switch ($attribute->name)
					{
						case 'src':
							$document->nodes->{$id}->url = $attribute->value;
							break;
							
						default:
							break;
					}
				}
			}
			break;			
		
		case 'i':
			new_annotation($document, 'emphasis');
			break;
			
		case 'b':
			new_annotation($document, 'strong');
			break;

		case 'br':
			new_annotation($document, 'linebreak');
			break;

		case 'sup':
			new_annotation($document, 'superscript');
			break;
			
		case 'wbr':
			new_annotation($document, 'softhyphen');
			break;			
						
		case '#text':
			// Grab text and clean it up
			if (!isset($document->node_type_counter['text']))
			{
				$document->node_type_counter['text'] = 0;
			}
			$document->node_type_counter['text']++;
			
			$id = 'text_' . $document->node_type_counter['text'];
			$document->nodes->{$id} = new stdclass;
			$document->nodes->{$id}->type = 'text';
			$document->nodes->{$id}->id = $id;		
		
			$content = $node->nodeValue;
			
			// clean text 
			$content = clean_text($content);
			
			// very important!
			$content_length =  mb_strlen($content, mb_detect_encoding($content));
		
			$document->current_paragraph_node->content .= $content;
			$document->counter += $content_length;
			
			// text node
			$document->nodes->{$id}->content = $content;
			$document->current_node[] = $document->nodes->{$id};
			
			$document->current_paragraph_node->children[] = $id;
			break;	
						
		default:
			// a tag we don't handle, just record for now
			if (!isset($document->node_type_counter['unknown']))
			{
				$document->node_type_counter['unknown'] = 0;
			}
			$document->node_type_counter['unknown']++;
			$id = 'unknown' . $document->node_type_counter['unknown'];
			$document->nodes->{$id} = new stdclass;
			$document->nodes->{$id}->type = 'unknown';
			$document->nodes->{$id}->id = $id;
			$document->nodes->{$id}->name = $node->nodeName;
			
			$document->current_node[] = $document->nodes->{$id};
		
			break;
	}
	
	// Visit any children of this node
	if ($node->hasChildNodes())
	{
		foreach ($node->childNodes as $children) {
			dive($children, $document);
		}
	}
	
	// Leaving this node, any annotations that cover a span of text get closed here
	// This is also the point at which we have all the text for a paragraph node, so
	// do any entity recognistion here
	$n = array_pop($document->current_node);
	
	switch ($n->type)
	{
		// handle formatting annotations that span a range of text
		case 'emphasis':
		case 'strong':
		case 'superscript':
			$n->range[1] = max(0, $document->counter - 1);
			$n->path[0] = $document->current_paragraph_node->id;
			
			// These annotations are spans that have open and closing tags
			add_annotation($document, $n);			
			break;
			
		// formatting that is a closed tag with no text content , e.g. <wbr/>
		case 'linebreak':
		case 'softhyphen':
			$n->range[1] = $document->counter;
			$n->path[0] = $document->current_paragraph_node->id;
			add_annotation($document, $n);			
			break;

		case 'paragraph':
			// leaving paragraph node, do any entity recognition here

			// names
			find_taxon_names($document);
						
			// georeferenced points
			find_latlong($document);
			
			// specimen codes
			find_specimen_codes($document);
			
			// accessions
			// can clash with herbarium barcodes, so need to rethink this
			//find_genbank_accession_numbers($document);
			
			// identifiers
			
			// citations
			
			// other entities
			
			break;
			
		default:
			break;
	}
}

//--------------------------------------------------------------------------------------------------

function to_html($document, $extra = false, $show_italics=true)
{
	$html = '';

	// dump as HTML (ideally should be able to completely reproduce input...)
	foreach ($document->nodes as $doc_node)
	{
		if ($doc_node->type == 'page')
		{
			foreach ($doc_node->children as $child_id)
			{
				if ($document->nodes->{$child_id}->type == 'paragraph')
				{
					$node = $document->nodes->{$child_id};
				
					$html .= '<p';
								
					if (isset($node->style))
					{
						$html .= ' style="' . $node->style . '"';
						//echo $node->style;
					}
					//$html .= '"';				
				
					$html .= '>';
				
				
					// walk along text and output annotations and text
					$content_length =  mb_strlen($node->content, mb_detect_encoding($node->content));
				
					for ($i = 0; $i < $content_length; $i++)
					{
						$char = mb_substr($node->content, $i, 1);
					
						if (isset($node->open_annotation[$i]))
						{
							foreach ($node->open_annotation[$i] as $k => $v)
							{
								foreach ($v as $annotation_id)
								{
									switch ($document->nodes->{$annotation_id}->type)
									{
									
										case 'emphasis':
											if ($show_italics)
											{
												$html .= '<i>';
											}
											break;
			
										case 'strong':
											$html .= '<b>';
											break;
			
										case 'superscript':
											$html .= '<sup>';
											break;
											
										// extra annotations
		
										case 'name':
											if ($extra)
											{
												$html .= '<span style="background-color:yellow">';
											}
											break;
		
										case 'occurrence':
											if ($extra)
											{
												$html .= '<span style="background-color:#99FFFF">';
											}
											break;
		
										case 'point':
											if ($extra)
											{
												$html .= '<span style="background-color:orange">';
											}
											break;
													
										case 'genbank':
											if ($extra)
											{
												$html .= '<span style="background-color:yellow;">';
											}
											break;
																					
										case 'linebreak':
											$html .= '<br/>';
											break;
										
										
										default:
											break;
									}
								}
							}
						}
					
						//echo $char;
						$html .= htmlspecialchars($char, ENT_NOQUOTES | ENT_HTML5);
					
						if (isset($node->close_annotation[$i]))
						{
							foreach ($node->close_annotation[$i] as $k => $v)
							{
								foreach ($v as $annotation_id)
								{
									switch ($document->nodes->{$annotation_id}->type)
									{
								
										case 'softhyphen':
											$html .= '&shy;';
											break;
									
										case 'emphasis':
											if ($show_italics)
											{
												$html .= '</i>';
											}
											break;
										
										case 'strong':
											$html .= '</b>';
											break;
										
										case 'superscript':
											$html .= '</sup>';
											break;
									
										case 'name':
											if ($extra)
											{
												$html .= '</span>';
											}
											break;

										case 'occurrence':
											if ($extra)
											{
												$html .= '</span>';
											}
											break;
		
										case 'point':
											if ($extra)
											{
												$html .= '</span>';
										
												if (1)
												{
													//echo '<a href="http://maps.google.com/?q=' . $document->nodes->{$annotation_id}->feature->geometry->coordinates[1] . ',' . $document->nodes->{$annotation_id}->feature->geometry->coordinates[0] . '&z=8" target="_new">(Google)</a>';
													$html .= '<a href="http://www.openstreetmap.org/?mlat=' . $document->nodes->{$annotation_id}->feature->geometry->coordinates[1]. '&mlon=' . $document->nodes->{$annotation_id}->feature->geometry->coordinates[0] . '&zoom=8" target="_new">(OSM)</a>';
												}
											}										
											break;										

										case 'genbank':
											if ($extra)
											{
												$html .= '</span>';
											}
											break;
										
										default:
											break;
									}
								}
							}
						}
					
					
					
					
					
					}
				
					$html .= '</p>';
				}
			}
		}
	}
	
	return $html;
}


?>