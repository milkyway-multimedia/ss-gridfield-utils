<?php
/**
 * Milkyway Multimedia
 * GridFieldAddNewButton.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\GridFieldUtils;

use ArrayData;
use Controller;

class GridFieldAddNewButton extends \GridFieldAddNewButton {
	public $gridFieldDetailForm;

	public function __construct($targetFragment = 'before', $buttonName = '', $gridFieldDetailForm = null) {
		$this->targetFragment = $targetFragment;
		$this->buttonName = $buttonName;
		$this->gridFieldDetailForm = $gridFieldDetailForm;
	}

	public function getHTMLFragments($gridField) {
		$singleton = singleton($gridField->getModelClass());

		if(!$singleton->canCreate()) {
			return array();
		}

		if(!$this->buttonName) {
			// provide a default button name, can be changed by calling {@link setButtonName()} on this component
			$objectName = $singleton->i18n_singular_name();
			$this->buttonName = _t('GridField.Add', 'Add {name}', array('name' => $objectName));
		}

		if($this->gridFieldDetailForm instanceof GridFieldDetailForm)
			$link = $gridField->Link($this->gridFieldDetailForm->getUriSegment());
		elseif($this->gridFieldDetailForm)
			$link = $gridField->Link($this->gridFieldDetailForm);
		else
			$link = ($df = $gridField->Config->getComponentByType('GridFieldDetailForm')) && isset($df->uriSegment) ? $gridField->Link($df->getUriSegment()) : $gridField->Link('item');

		$data = ArrayData::create(array(
			'NewLink' => Controller::join_links($link, 'new'),
			'ButtonName' => $this->buttonName,
		));

		return array(
			$this->targetFragment => $data->renderWith('GridFieldAddNewButton'),
		);
	}
} 