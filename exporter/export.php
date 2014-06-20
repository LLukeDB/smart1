<?php

require_once ($CFG->dirroot . '/question/format/smart1/logging.php');
require_once ($CFG->dirroot . '/question/format/smart1/wrapper/page_wrapper.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');
require_once ($CFG->dirroot . '/question/format/smart1/wrapper/question.php');
require_once ($CFG->dirroot . '/question/format/smart1/idgenerator.php');

class export_data {
	
	public $pages;		//page_wrappers
	public $metadataxml_wrapper;
	public $settingsxml_wrapper;
	public $imsmanifest_wrapper;
	public $metadatardf_wrapper;
	
	public function __construct() {
		$this->pages = array();
		$this->settingsxml_wrapper = new settingsxml_wrapper();
		$this->metadataxml_wrapper = new metadataxml_wrapper();
		$this->metadatardf_wrapper = new metadatardf_wrapper();
		$this->imsmanifest_wrapper = new imsmanifest_wrapper();
	}
	
	public function add_page($page) {
		array_push($this->pages, $page);
	}
	
	public function toZIP() {
		global $CFG;
	
		// Create temporary directory for data.
		$moodletmpdir = $CFG->dataroot . "/temp/";
		$tmpdir = tempdir($moodletmpdir, "smart_");
		createDirStructure($tmpdir);
	
		// Write data to temporary directory.
		$this->settingsxml_wrapper->save($tmpdir);
		$this->metadataxml_wrapper->save($tmpdir);
		$this->imsmanifest_wrapper->save($tmpdir);
		$this->metadatardf_wrapper->save($tmpdir);
		foreach ($this->pages as $page) {
			$page->save($tmpdir);
		}
	
		// Create zip file from temporary directory.
		$tmpfile = tempnam($moodletmpdir, 'smart_');
		create_zip($tmpdir, $tmpfile);
		//recurseRmdir($tmpdir);	// Commented out for development.
	
		return $tmpfile;
	}
}

class qformat_exporter_factory {

	function get_exporter($question) {
		switch($question->qtype) {
			case 'category':
				return new category_exporter();
			case 'truefalse':
				return new truefalse_exporter($question);
			case 'log':
				return new log_exporter($question);
			default:
				return false;
		}
	}
}

abstract class qformat_exporter {
	
	public function export($export_data) {
		$this->write_imsmanifest($export_data);
		$this->write_metadataxml($export_data);
		$this->write_metadatardf($export_data);
		$this->write_page($export_data);
	}
	
	protected function write_imsmanifest($export_data) {
		return;
	}
	
	protected function write_metadataxml($export_data) {
		return;
	}
	
	protected function write_metadatardf($export_data) {
		return;
	}
	
	protected function write_page($export_data) {
		return;
	}
		
}

/**
 * Class for exporting a true-false-question.
 */
class truefalse_exporter extends qformat_exporter {

	private $mquestion;
	
	public function __construct($question) {
		$this->mquestion = $question;
	}
	
	public function export($export_data) {
		$page_num = count($export_data->pages);
		
		$question = new question($page_num);
		
		$question->format = "trueorfalse";
		$question->labelstyle = "true/false";
		$question->points = $this->mquestion->defaultmark;
		$question->correct = $this->mquestion->options->trueanswer == '292' ? 1 : 2;
		$question->questiontext = $this->mquestion->questiontext;
		$question->question_num = $page_num + 1;
		$question->questionformat = "choice";
		$question->choicelabelstyle = "true-false";
		
		$choice_true = new choice();
		$choice_true->id = "EFGH";
		$choice_true->label = "1";
		$choice_true->choicetext = "Wahr";
		$choice_true->true = $this->mquestion->options->trueanswer == '292' ? true : false;
		$question->add_choice($choice_true);
		
		$choice_false = new choice();
		$choice_false->id = "EFGH";
		$choice_false->label = "1";
		$choice_false->choicetext = "Falsch";
		$choice_false->true = $this->mquestion->options->trueanswer == '292' ? false : true;
		$question->add_choice($choice_false);
		
		$page_wrapper = new page_wrapper($question);
		$export_data->add_page($page_wrapper);
		$export_data->metadatardf_wrapper->add_question($question);
	}
}

/**
 * Dummy class for categories, which does nothing.
 */
class category_exporter extends qformat_exporter {
	
	public function export($export_data) {
		return;
	}
}

/**
 * Class for exporting errors, which have been logged during the export process
 * and some general tasks.
 */
class log_exporter extends qformat_exporter {
	
	public function __construct($question) {
		error_logger::get_instance()->log_error("log_exporter created");
	}
	
	/*
	 * Helper function, which exports the log to a file for debugging.
	 */
	private function export_to_file() {
		$path = "/opt/lampp/logs/smart1_error_log";
		
		$error_logger = error_logger::get_instance();
		$error_log = $error_logger->get_error_log();
		
		$handle = fopen($path, 'w');
		$date = date("Y-m-d\TH:i:s");
		foreach ($error_log as $error) {
			fputs($handle, "[$date] " . $error . "\n");
		}
		fclose($handle);
	}
	
}































?>