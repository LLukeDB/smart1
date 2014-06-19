<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Exports questions in the SMART Notebook format.
 *
 * @package questionbank
 * @subpackage importexport
 * @copyright 2014 Lukas Baumann
 * @author Lukas Baumann
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */
require_once ("$CFG->libdir/xmlize.php");
require_once ($CFG->dirroot . '/lib/uploadlib.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');
require_once ($CFG->dirroot . '/question/format/smart1/exporter/export.php');
require_once ("$CFG->dirroot/question/format.php");
require_once ($CFG->dirroot . '/question/format/smart1/logging.php');
require_once ($CFG->dirroot . '/question/format/smart1/wrapper/metadataxml_wrapper.php');

class qformat_smart1 extends qformat_default {
	private static $plugin_dir = "/question/format/smart1/"; 				// Folder where the plugin is installed, relative to Moodle $CFG->dirroot.
	private $settings_template    = "wrapper/templates/settings.xml";
	private $imsmanifest_template = "wrapper/templates/imsmanifest.xml";
	private $metadatardf_template = "wrapper/templates/metadata.rdf";
	private $page_template        = "wrapper/templates/page.svg";
	
	public static function get_plugin_dir() {
		return qformat_smart1::$plugin_dir;
	}
	
	/**
	 * @return bool whether this plugin provides export functionality.
	 */
	public function provide_export() {
		return true;
	}
	
	public function mime_type() {
		return 'application/smart_notebook'; // TODO
	}
	
	// IMPORT FUNCTIONS START HERE
	// Import is not supported.
	
	// EXPORT FUNCTIONS START HERE
	
	/**
	 * @return string file extension
	 */
	function export_file_extension() {
		return ".notebook";
	}
	
	/**
	 * Do any pre-processing that may be required
	 *
	 * @return     	bool success
	 */
	public function exportpreprocess() {
		return true;
	}
	
	/**
	 * Enable any processing to be done on the content
	 * just prior to the file being saved
	 * default is to do nothing
	 *
	 * @param	string output text
	 *        	
	 * @param	string processed output text
	 *        	
	 */
	protected function presave_process($content) {
		return $content;
	}
	
	/**
	 * Do the export
	 * For most types this should not need to be overrided
	 *
	 * @return stored_file
	 */
	public function exportprocess() {
		global $CFG, $OUTPUT, $DB, $USER;
		
		// get the questions (from database) in this category
		// only get q's with no parents (no cloze subquestions specifically)
		if ($this->category) {
			$questions = get_questions_category ( $this->category, true );
		} else {
			$questions = $this->questions;
		}
		
		// $count = 0;
		
		// // results are first written into string (and then to a file)
		// // so create/initialize the string here
		// $expout = "";
		
		// // track which category questions are in
		// // if it changes we will record the category change in the output
		// // file if selected. 0 means that it will get printed before the 1st question
		// $trackcategory = 0;
		
		// // iterate through questions
		// foreach ( $questions as $question ) {
		// // used by file api
		// $contextid = $DB->get_field ( 'question_categories', 'contextid', array (
		// 'id' => $question->category
		// ) );
		// $question->contextid = $contextid;
		
		// // do not export hidden questions
		// if (! empty ( $question->hidden )) {
		// continue;
		// }
		
		// // do not export random questions
		// if ($question->qtype == 'random') {	
		// continue;
		// }
		
		// // check if we need to record category change
		// if ($this->cattofile) {
		// if ($question->category != $trackcategory) {
		// $trackcategory = $question->category;
		// $categoryname = $this->get_category_path ( $trackcategory, $this->contexttofile );
		
		// // create 'dummy' question for category export
		// $dummyquestion = new stdClass ();
		// $dummyquestion->qtype = 'category';
		// $dummyquestion->category = $categoryname;
		// $dummyquestion->name = 'Switch category to ' . $categoryname;
		// $dummyquestion->id = 0;
		// $dummyquestion->questiontextformat = '';
		// $dummyquestion->contextid = 0;
		// $expout .= $this->writequestion ( $dummyquestion ) . "\n";
		// }
		// }
		
		// // export the question displaying message
		// $count ++;
		
		// if (question_has_capability_on ( $question, 'view', $question->category )) {
		// $expout .= $this->writequestion ( $question, $contextid ) . "\n";
		// }
		
		// }
		
		// // continue path for following error checks
		// $course = $this->course;
		// $continuepath = "$CFG->wwwroot/question/export.php?courseid=$course->id";
		
		// // did we actually process anything
		// if ($count == 0) {
		// print_error ( 'noquestions', 'question', $continuepath );
		// }
		
		// // final pre-process on exported data
		// $expout = $this->presave_process ( $expout );
		
		
		
		$exporter_factory = new qformat_exporter_factory();
		$export_data = $this->init_export_data();
		
		// Export all questions.
		foreach ( $questions as $question ) {
			error_logger::get_instance()->log_error("exporting question \"" . $question->name . "\"");
			$exporter = $exporter_factory->get_exporter($question);
			
			if(!$exporter) {
				error_logger::get_instance()->log_error("creation of exporter for question \"" . $question->name . "\" failed!");
				// continue path for following error checks
				$course = $this->course;
				$continuepath = "$CFG->wwwroot/question/export.php?courseid=$course->id";
				// TODO Print correct error message.
				print_error('unsupported questiontype', 'question', $continuepath);
			}
			else {
				$exporter->export($export_data);				
			}			
			
		}
		
		// Export logged errors.
		error_logger::get_instance()->log_error("exporting question error_log");
		$dummy_question=new stdClass();
		$dummy_question->qtype='log';
		$exporter = $exporter_factory->get_exporter($dummy_question);
		$exporter->export($export_data);
		
		// Create zip-file from export_data.
		$zip_file = "asdf";
		$zip_file = $this->create_zip_from_export_data($export_data);
// 		$this->start_download($zip_file);
// 		unlink($zip_file);

		// Return the zip file.
		$filehandle = fopen($zip_file, "r");
		$filecontent = fread($filehandle, filesize($zip_file));
		fclose($filehandle);		
		return $filecontent;
	}
	
	/**
	 * Do an post-processing that may be required
	 *
	 * @return bool success
	 */
	protected function exportpostprocess() {
		return true;
	}
	
	/**
	 * convert a single question object into text output in the given
	 * format.
	 * This must be overriden
	 *
	 * @param
	 *        	object question question object
	 * @return mixed question export text or null if not implemented
	 */
	protected function writequestion($question) {
		global $OUTPUT, $CFG;
		// if not overidden, then this is an error.
		// $formatnotimplemented = get_string ( 'formatnotimplemented', 'question' );
		// echo "<p>$formatnotimplemented</p>";
		// echo get_string('test_output', 'qformat_smart1');
		echo "dataroot: " . $CFG->dataroot;
		echo $OUTPUT->notification ( get_string ( 'test_output', 'qformat_smart1' ) );
		echo $OUTPUT->notification ( "test output notification" );
		// debugging("DEBUG-> " . __FILE__ . " : " . __FUNCTION__ . " : " . __LINE__, DEBUG_DEVELOPER);
		return null;
	}
	
	private function init_export_data() {
		global $CFG;
		$export_data = new export_data();
		
		// Load settings.xml-template.
		$filename = $CFG->dirroot . qformat_smart1::get_plugin_dir() . $this->settings_template;
		$export_data->settings = load_simplexml($filename);
		
		// Init metadataxml_wrapper.
		$export_data->metadataxml_wrapper = new metadataxml_wrapper();
		
		// Load metadata.rdf-template.
		$filename = $CFG->dirroot . qformat_smart1::get_plugin_dir() . $this->metadatardf_template;
		$export_data->metadatardf = load_simplexml($filename);
		
		// Load imsmanifest.xml-template.
		$filename = $CFG->dirroot . qformat_smart1::get_plugin_dir() . $this->imsmanifest_template;
		$export_data->imsmanifest = load_simplexml($filename);
		
		// Load page.svg-template.
		$filename = $CFG->dirroot . qformat_smart1::get_plugin_dir() . $this->page_template;
		$export_data->page_template = load_simplexml($filename);
		
		return $export_data;
	}
	
	private function create_zip_from_export_data($export_data) {
		global $CFG;
		
		// Create temporary directory for data.
		$moodletmpdir = $CFG->dataroot . "/temp/";
		$tmpdir = tempdir($moodletmpdir, "smart_");
		createDirStructure($tmpdir);
		
		// Write settings.xml to temporary directory.
		$filename = $tmpdir . "settings.xml";
		$xml_doc = $export_data->settings;
		save_simplexml($xml_doc, $filename);
		
		// Write metadata.xml to temporary directory.
		$export_data->metadataxml_wrapper->save($tmpdir);

		// Write metadata.rdf to temporary directory.
		$filename = $tmpdir . "metadata.rdf";
		$xml_doc = $export_data->metadatardf;
		save_simplexml($xml_doc, $filename);
		
		// Write imsmanifest.xml to temporary directory.
		$filename = $tmpdir . "imsmanifest.xml";
		$xml_doc = $export_data->imsmanifest;
		save_simplexml($xml_doc, $filename);
		
		// Write pages to temporary directory.
		$pages = $export_data->pages;
		for ($i = 0; $i < count($pages); $i++) {
			$filename = $tmpdir . "page" . $i . ".svg";
			$xml_doc = $pages[$i];
			save_simplexml($xml_doc, $filename);
		}
		
		// Create zip file from temporary directory.		
		$tmpfile = tempnam($moodletmpdir, 'smart_');
		create_zip($tmpdir, $tmpfile);
		//recurseRmdir($tmpdir);	// Commented out for development.
		
		return $tmpfile;
	}
	
// 	private function start_download($zipfile) {
// 		$filehandle = fopen($zipfile, "r");
// 		$filecontent = fread($filehandle, filesize($zipfile));
// 		fclose($filehandle);
		
// 		$name = "xyz 123.zip";
// 		$type = "APPLICATION";
// 		$subtype = "ZIP";
// 		$encoding = "BASE64";
		
// 		header('Content-Description: File Transfer');
// 		header('Content-Type: '. $type .'/'. $subtype);
// 		header('Content-Disposition: attachment; filename='.$name);
// 		header('Content-Transfer-Encoding: '.$encoding);
// 		header('Expires: 0');
// 		header('Cache-Control: must-revalidate');
// 		header('Pragma: public');
// 		ob_clean();
// 		flush();
// 		echo $filecontent;
// 	}
}
?>

