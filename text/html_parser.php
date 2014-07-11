<?php

require_once ('text.php');

class html_parser {

private $formattings;
private $xml_parser;
private $logger;
private $text;

private function init_parser() {
	$this->formattings = array();
	
	// Init parser.
	$this->xml_parser = xml_parser_create ();
	xml_parser_set_option ( $this->xml_parser, XML_OPTION_CASE_FOLDING, 0 );
	xml_parser_set_option ( $this->xml_parser, XML_OPTION_SKIP_WHITE, 1 );
	xml_set_default_handler ( $this->xml_parser, "parseDEFAULT" );
	xml_set_element_handler ( $this->xml_parser, "html_parser::startElement", "html_parser::endElement" );
	xml_set_character_data_handler ( $this->xml_parser, "html_parser::contents" );
	
	// Set default formattings.
// 	$default_formattings = array("fill" => "#000000",
// 								"font-size" => "28.000",
// 								"font-family" => "Arial",
// 								"char-transform" => "0.00 1.00 0.00 0.00 0.00 1.00");
// 	array_push($this->formattings, $default_formattings);
	
	// Create text-object.
	$this->text = new text();
	
	// Get logger.
	// $this->logger = ...
}

/**
 * Parses text, which is in the moodle html-format, into an text-object.
 */
public function parse_text($text) {
	$this->init_parser();
	
	// Create surrounding text-elements to get valid xml.
	$text = "<text>" . $text . "</text";
	
	// Delete linebreaks from the text.
	str_replace("\n", "", $text);
	
	if (! xml_parse ( $this->xml_parser, $text )) {
		// TODO error handling.
	}
	xml_parser_free ( $this->xml_parser );
	
	return $this->text;
}

/*
 * Callback function for the parser, which handles start-nodes.
 */
private function startElement($parser, $name, $attrs) {
	switch ($name) {
		case "text" :
			// Do nothing.
			break;
		case "p" :
			$this->text->add_paragraph();
			$this->parse_attributes($attrs);
			break;
		case "br" :
			$this->text->add_paragraph();
			break;
		case "strong" :
			$new_formattings = array("font-weight" => "bold");
			array_push($this->formattings, $new_formattings);
			break;
		case "em" :
			$new_formattings = array("font-style" => "italic");
			array_push($this->formattings, $new_formattings);
			break;
		case "span" :
			$this->parse_attributes($attrs);
			break;
		case "li" :
			$this->text->add_paragraph();
			break;
		case "tbody" :
			// Do nothing.
			break;
		case "td" :
			// Do nothing.
			break;
		case "tr" :
			$this->text->add_paragraph();
			break;
		default :
			// TODO log error: Unsupported Formatting.
			break;
	}
}

/*
 * Callback function for the parser, which handles end-nodes.
 */
private function endElement($parser, $name) {
	switch ($name) {
		case "text" :
			// Do nothing.
			break;
		case "p" :
			// Do nothing.
			break;
		case "br" :
			// Do nothing.
			break;
		case "strong" :
			array_pop($this->formattings);
			break;
		case "em" :
			array_pop($this->formattings);
			break;
		case "span" :
			array_pop($this->formattings);
			break;
		default :
			// Do nothing.
			break;
	}
}

private function parse_attributes($attrs) {
	foreach($attrs as $key => $value) {
		if($key == "style") {
			$this->parse_style_attribue($value);
		}
		else {
			array_push($this->formattings, array());
			// TODO Error logging: unknown attribute.
		}
	}
}

private function parse_style_attribue($attrval) {
	$styles = preg_split("/;\s*/", $attrval, -1, PREG_SPLIT_NO_EMPTY);
	$splitted_styles = array();
	foreach ($styles as $num => $style) {
		$splitted_style = preg_split("/:\s*/", $style);
		if(count($splitted_style) != 2) {
			// TODO Error handling.
		}
		else {
			$splitted_styles[$splitted_style[0]] = $splitted_style[1];
		}
	}
	
	$translated_styles = array();
	foreach($splitted_styles as $stylename => $stylevalue) {
		$translated_style = $this->translate_style($stylename, $stylevalue);
		$translated_styles = array_merge($translated_styles, $translated_style);
	}
	array_push($this->formattings, $translated_styles);
}

/*
 * Translates one style, which was specified by the style-attribute of a moodle-text into a smart style. 
 */
private function translate_style($stylename, $stylevalue) {
	$returnvalue = array();
	
	switch($stylename) {
		case "font-family": 
			$fonts = preg_split("/,\s*/", $stylevalue); 
			$returnvalue = array("font-family" => $fonts[0]); // Take only the first specified font.
			break;
		case "font-size":
			$font_size = "";
			switch($stylevalue) {
				case "xx-small":
					$font_size = "10";
					break;
				case "x-small":
					$font_size = "16";
					break;
				case "small":
					$font_size = "22";
					break;
				case "medium":
					$font_size = "28";
					break;
				case "large":
					$font_size = "34";
					break;
				case "x-large":
					$font_size = "40";
					break;
				case "xx-large":
					$font_size = "46";
					break;
				default:
					$font_size = "28";
					break;
			}
			$returnvalue = array("font-size" => $font_size);
			break;
		case "text-decoration":
			switch($stylevalue) {
				case "underline":
					$returnvalue = array("text-decoration" => "underline");
					break;
				case "line-through":
					$returnvalue = array("text-strikeout" => "strikeout");
					break;
				default:
					// TODO log error: Unsupported formatting.
					break;
			}
			break;
		case "text-align":
			if($stylevalue != "left") {
				// TODO log error: Unsupported formatting.
			}
			break;
		case "color":
			$returnvalue = array("fill" => $stylevalue);
			break;
		default:
			// TODO log error: Unsupported formatting.
			break;
	}
	
	return $returnvalue;
}


/*
 * Callback function for the parser, which handles text-nodes.
 */
private function contents($parser, $data) {
	$formattings = $this->get_formattings();
	$this->text->add_textfragment($data, $formattings);
}

/*
 * Returns the formattings for the current text-node as a single associative array.
 */
private function get_formattings() {
	$merged_formattings = array();
	foreach ($this->formattings as $formatting) {
		$merged_formattings = array_merge($merged_formattings, $formatting);
	}
	
	return $merged_formattings;
}

// function parseDEFAULT($parser, $data) {
// 	$data = preg_replace ( "/</", "&lt;", $data );
// 	$data = preg_replace ( "/>/", "&gt;", $data );
// 	echo "parseDEFAULT " . $data;
// }

}

?>