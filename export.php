<?php

require_once ($CFG->dirroot . '/question/format/smart1/logging.php');

class export_data {
	
	public $imsmanifest;
	public $metadataxml;
	public $metadatardf;
	public $settings;
	public $pages = array();
	public $page_template;
	
}

class qformat_exporter_factory {

	function get_exporter($question) {
		switch($question->qtype) {
			case 'category':
				return new category_exporter();
			case 'truefalse':
				return new truefalse_exporter($question);
			case 'log':
				return new log_exporter();
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
	
	protected function add_page_to_imsmanifest($page_name, $export_data) {
		$imsmanifest = $export_data->imsmanifest;
	
		$page = $imsmanifest->resources->resource[0]->addChild("file");
		$page->addAttribute("href", $page_name);
	
		$page = $imsmanifest->resources->resource[1]->addChild("file");
		$page->addAttribute("href", $page_name);
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

		$page = $export_data->page_template;
		$page_num = count($export_data->pages);
		$page_name = "page" . $page_num . ".svg";
		// TODO copy template and fill it.
		array_push($export_data->pages, $page);
		$this->add_page_to_imsmanifest($page_name, $export_data);
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
	
	public function export($export_data) {
		error_logger::get_instance()->log_error("log_exporter->export() called");
		$this->write_metadataxml($export_data);
		
		$this->export_to_file();
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
		
	protected function write_metadataxml($export_data) {
		error_logger::get_instance()->log_error("log_exporter->write_metadataxml() called");
		
		// write current date to metadata.xml
		error_log('log_exporter->write_metadataxml');
		$date = date("Y-m-d\TH:i:s");
		$export_data->metadataxml->children('lom', true)->lifeCycle->children('smartgallery', true)->creationdatetime = $date;
	}
}






























?>