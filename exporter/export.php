<?php

require_once ($CFG->dirroot . '/question/format/smart1/logging.php');
require_once ($CFG->dirroot . '/question/format/smart1/wrapper/page_wrapper.php');

class export_data {
	
	public $pages = array();
	public $page_template;
	
	public $metadataxml_wrapper;
	public $settingsxml_wrapper;
	public $imsmanifest_wrapper;
	public $metadatardf_wrapper;
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

	private $question;
	
	public function __construct($question) {
		$this->question = $question;
		error_logger::get_instance()->log_error("truefalse_exporter created");
	}
	
	protected function write_page($export_data) {
		error_logger::get_instance()->log_error("truefalse_exporter->write_page() called");

		$page_num = count($export_data->pages);
		$page_name = "page" . $page_num . ".svg";
		$page_id = "ABCD";
		
		$page_wrapper = new page_wrapper($page_name, $page_id);
		// TODO Fill page.
		
		array_push($export_data->pages, $page_wrapper);
		$export_data->imsmanifest_wrapper->add_page($page_name);
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