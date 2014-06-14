<?php

class export_data {
	
	public $manifest;
	public $metadataxml;
	public $metadatardf;
	public $settings;
	public $pages;
	
}

class qformat_exporter_factory {
	
	function get_exporter($question) {
		switch($question->qtype) {
			case 'category':
				return new category_exporter();
			case 'true_false':
				return new true_false_exporter($question);
			case 'log':
				return new log_exporter();
			default:
				return false; 
		}
	}
}

abstract class qformat_exporter {
	
	
	public function export($export_data) {
		$this->write_manifest($export_data);
		$this->write_metadataxml($export_data);
		$this->write_metadatardf($export_data);
		$this->write_page($export_data);
	}
	
	private function write_manifest() {
		
	}
	
	private function write_metadataxml() {
		
	}
	
	private function write_metadatardf() {
		
	}
	
	private function write_page() {
		
	}
		
}

/**
 * Class for exporting a true-false-question.
 */
class true_false_exporter extends qformat_exporter {

	private $question;
	
	function __construct($question) {
		parent::__construct();
		$this->question = $question;
	}
	
	
	
	
}

/**
 * Dummy class for categories, which does nothing.
 */
class category_exporter extends qformat_exporter {
	
	public function export($export_data) {
		return true;
	}
}

/**
 * Class for exporting errors, which have been logged during the export process
 * and some general tasks.
 */
class log_exporter extends qformat_exporter {
	
	private function write_metadataxml($export_data) {
		
		// write current date to metadata.xml
		$date = date("Y-m-d\TH:i:s");
		$export_data->metadataxml->children('lom', true)->lifeCycle->children('smartgallery', true)->creationdatetime = $date;
	}
}































?>