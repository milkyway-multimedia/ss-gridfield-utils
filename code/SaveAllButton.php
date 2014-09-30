<?php
/**
 * Milkyway Multimedia
 * SaveAllButton.php
 *
 * @package reggardocolaianni.com
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\GridFieldUtils;


class SaveAllButton implements \GridField_HTMLProvider, \GridField_ActionProvider {
	protected $targetFragment;
	protected $actionName = 'saveallrecords';

	public $buttonName;

	public $publish = true;

	public $completeMessage;

	public function setButtonName($name) {
		$this->buttonName = $name;
		return $this;
	}

	public function __construct($targetFragment = 'before', $publish = true, $action = 'saveallrecords') {
		$this->targetFragment = $targetFragment;
		$this->publish = $publish;
		$this->actionName = $action;
	}

	public function getHTMLFragments($gridField) {
		$singleton = singleton($gridField->getModelClass());

		if(!$singleton->canEdit() && !$singleton->canCreate()) {
			return [];
		}

		if(!$this->buttonName) {
			if($this->publish)
				$this->buttonName = _t('GridField.SAVE_ALL_AND_PUBLISH', 'Save all and publish');
			else
				$this->buttonName = _t('GridField.SAVE_ALL', 'Save all');
		}

		$button = \GridField_FormAction::create(
			$gridField,
			$this->actionName,
			$this->buttonName,
			$this->actionName,
			null
		);
		$button->setAttribute('data-icon', 'disk')->addExtraClass('new new-link ui-button-text-icon-primary');

		return [
			$this->targetFragment => $button->Field(),
		];
	}

	public function getActions($gridField) {
		return [$this->actionName];
	}

	public function handleAction(\GridField $gridField, $actionName, $arguments, $data) {
		if($actionName == $this->actionName) {
			return $this->saveAllRecords($gridField, $arguments, $data);
		}
	}

	protected function saveAllRecords(\GridField $grid, $arguments, $data) {
		if(isset($data[$grid->Name])) {
			$currValue = $grid->Value();
			$grid->setValue($data[$grid->Name]);
			$model = singleton($grid->List->dataClass());

			foreach ($grid->getConfig()->getComponents() as $component) {
				if ($component instanceof \GridField_SaveHandler)
					$component->handleSave($grid, $model);
			}

			if ($this->publish) {
				$grid->List->each(
					function ($item) {
						if ($item->hasExtension('Versioned')) {
							$item->writeToStage('Stage');
							$item->publish('Stage', 'Live');
						}
					}
				);
			}

			if ($model->exists()) {
				$model->delete();
				$model->destroy();
			}

			$grid->setValue($currValue);

			if(\Controller::curr() && $response = \Controller::curr()->Response) {
				if(!$this->completeMessage)
					$this->completeMessage = _t('GridField.DONE', 'Done.');

				$response->addHeader('X-Status', rawurlencode($this->completeMessage));
			}
		}
	}
} 