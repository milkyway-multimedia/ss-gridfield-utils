<?php namespace Milkyway\SS\GridFieldUtils;
/**
 * Milkyway Multimedia
 * GridFieldDetailForm.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Controller;

class GridFieldDetailForm extends \GridFieldDetailForm {
	public $uriSegment = 'item';

	public function __construct($name = 'DetailForm', $uriSegment = 'item') {
		$this->uriSegment = $uriSegment;
		parent::__construct($name);
	}

	public function getURLHandlers($gridField) {
		return [
			$this->uriSegment . '/$ID' => 'handleItem',
			'autocomplete' => 'handleAutocomplete',
		];
	}

	public function setUriSegment($urlSegment) {
		$this->uriSegment = $urlSegment;
		return $this;
	}

	public function getUriSegment() {
		return $this->uriSegment;
	}
}

class GridFieldDetailForm_ItemRequest extends \GridFieldDetailForm_ItemRequest {
	public function Link($action = null) {
		return Controller::join_links($this->gridField->Link($this->component->getUriSegment()),
			$this->record->ID ? $this->record->ID : 'new', $action);
	}
}