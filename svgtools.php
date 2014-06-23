<?php

function init_svg($page_id) {
	$svg = new SimpleXMLElement("<svg></svg>");
	$svg->addAttribute("width", "800");
	$svg->addAttribute("height", "600");
	$svg->addAttribute("xml:id", $page_id);
}

/**
 * Clean HTML content
 *
 * A string containing clean XHTML is returned
 *
 * @return string
 */
function clean_html_text($text_content_string) {
	$tidy_type = "strip_tags";

	// Check if Tidy extension loaded, and use it to clean the CDATA section if present
	if (extension_loaded('tidy')) {
		// cf. http://tidy.sourceforge.net/docs/quickref.html
		$tidy_type = "tidy";
		$tidy_config = array(
				'bare' => true, // Strip Microsoft Word 2000-specific markup
				'clean' => true, // Replace presentational with structural tags
				'word-2000' => true, // Strip out other Microsoft Word gunk
				'drop-font-tags' => true, // Discard font
				'drop-proprietary-attributes' => true, // Discard font
				'output-xhtml' => true, // Output XML, to format empty elements properly
				'show-body-only'   => true,
		);
		$clean_html = tidy_repair_string($text_content_string, $tidy_config, 'utf8');
	} else {
		// Tidy not available, so just strip most HTML tags except character-level markup and table tags
		$clean_html = strip_tags($text_content_string, "<b><br><em><i><img><strong><sub><sup><u><table><tbody><td><th><thead>");

		// The strip_tags function treats empty elements like HTML, not XHTML, so fix <br> and <img src=""> manually (i.e. <br/>, <img/>)
		$clean_html = preg_replace('~<img([^>]*?)/?>~si', '<img$1/>', $clean_html, PREG_SET_ORDER);
		$clean_html = preg_replace('~<br([^>]*?)/?>~si', '<br/>', $clean_html, PREG_SET_ORDER);
	}

	// Fix up filenames after @@PLUGINFILE@@ to replace URL-encoded characters with ordinary characters
	$found_pluginfilenames = preg_match_all('~(.*?)<img src="@@PLUGINFILE@@/([^"]*)(.*)~s', $clean_html, $pluginfile_matches, PREG_SET_ORDER);
	$n_matches = count($pluginfile_matches);
	if ($found_pluginfilenames !== FALSE and $found_pluginfilenames != 0) {
		$urldecoded_string = "";
		// Process the possibly-URL-escaped filename so that it matches the name in the file element
		for ($i = 0; $i < $n_matches; $i++) {
			// Decode the filename and add the surrounding text
			$decoded_filename = urldecode($pluginfile_matches[$i][2]);
			$urldecoded_string .= $pluginfile_matches[$i][1] . '<img src="@@PLUGINFILE@@/' . $decoded_filename . $pluginfile_matches[$i][3];
		}
		$clean_html = $urldecoded_string;
	}

	// Strip soft hyphens (0xAD, or decimal 173)
	$clean_html = preg_replace('/\xad/u', '', $clean_html);

	debugging(__FUNCTION__ . "() [using " . $tidy_type . "] -> |" . $clean_html . "|", DEBUG_DEVELOPER);
	return $clean_html;
}

