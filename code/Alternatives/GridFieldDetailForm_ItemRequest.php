<?php namespace Milkyway\SS\GridFieldUtils;
/**
 * Milkyway Multimedia
 * GridFieldDetailForm.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Controller;

class GridFieldDetailForm_ItemRequest extends \GridFieldDetailForm_ItemRequest {
	public function Link($action = null) {
		return Controller::join_links($this->gridField->Link($this->component->getUriSegment()),
			$this->record->ID ? $this->record->ID : 'new', $action);
	}
}