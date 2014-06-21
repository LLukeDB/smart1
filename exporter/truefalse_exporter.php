<?php

require_once ($CFG->dirroot . '/question/format/smart1/logging.php');
require_once ($CFG->dirroot . '/question/format/smart1/generator/page_generator.php');
require_once ($CFG->dirroot . '/question/format/smart1/filetools.php');
require_once ($CFG->dirroot . '/question/format/smart1/generator/question.php');
require_once ($CFG->dirroot . '/question/format/smart1/idgenerator.php');
require_once ($CFG->dirroot . '/question/format/smart1/exporter/export_data.php');
require_once ($CFG->dirroot . '/question/format/smart1/exporter/export.php');

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

		$page_generator = new page_generator($question);
		$export_data->add_page($page_generator);
		$export_data->metadatardf_generator->add_question($question);
	}
}