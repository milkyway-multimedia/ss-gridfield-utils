<?php namespace Milkyway\SS\GridFieldUtils;
/**
 * Milkyway Multimedia
 * SaveAllButton.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use GridField;
use GridField_HTMLProvider;
use GridField_ActionProvider;
use GridField_FormAction;
use GridField_SaveHandler;
use Controller;

class SaveAllButton implements GridField_HTMLProvider, GridField_ActionProvider
{
    protected $targetFragment;
    protected $actionName = 'saveallrecords';

    public $buttonName;

    public $publish = true;

    public $completeMessage;

    public $removeChangeFlagOnFormOnSave = false;

    public function setButtonName($name)
    {
        $this->buttonName = $name;
        return $this;
    }

    public function setRemoveChangeFlagOnFormOnSave($flag)
    {
        $this->removeChangeFlagOnFormOnSave = $flag;
        return $this;
    }

    public function __construct($targetFragment = 'before', $publish = true, $action = 'saveallrecords')
    {
        $this->targetFragment = $targetFragment;
        $this->publish = $publish;
        $this->actionName = $action;
    }

    public function getHTMLFragments($gridField)
    {
        $singleton = singleton($gridField->getModelClass());

        if (!$singleton->canEdit() && !$singleton->canCreate()) {
            return [];
        }

        if (!$this->buttonName) {
            if ($this->publish && $singleton->hasExtension('Versioned')) {
                $this->buttonName = _t('GridField.SAVE_ALL_AND_PUBLISH', 'Save all and publish');
            } else {
                $this->buttonName = _t('GridField.SAVE_ALL', 'Save all');
            }
        }

        $button = GridField_FormAction::create(
            $gridField,
            $this->actionName,
            $this->buttonName,
            $this->actionName,
            null
        );

        $button->setAttribute('data-icon', 'disk')->addExtraClass('new new-link ui-button-text-icon-primary');

        if($this->removeChangeFlagOnFormOnSave) {
            $button->addExtraClass('js-mwm-gridfield--saveall');
        }

        return [
            $this->targetFragment => $button->Field(),
        ];
    }

    public function getActions($gridField)
    {
        return [$this->actionName];
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == $this->actionName) {
            return $this->saveAllRecords($gridField, $arguments, $data);
        }
    }

    protected function saveAllRecords(GridField $grid, $arguments, $data)
    {
        if (isset($data[$grid->Name])) {
            $currValue = $grid->Value();
            $grid->setValue($data[$grid->Name]);
            $model = singleton($grid->List->dataClass());

            foreach ($grid->getConfig()->getComponents() as $component) {
                if ($component instanceof GridField_SaveHandler) {
                    $component->handleSave($grid, $model);
                }
            }

            if ($this->publish) {
                // Only use the viewable list items, since bulk publishing can take a toll on the system
                $list = ($paginator = $grid->getConfig()->getComponentByType('GridFieldPaginator')) ? $paginator->getManipulatedData($grid, $grid->List) : $grid->List;

                $list->each(
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

            if (Controller::curr() && $response = Controller::curr()->Response) {
                if (!$this->completeMessage) {
                    $this->completeMessage = _t('GridField.DONE', 'Done.');
                }

                $response->addHeader('X-Status', rawurlencode($this->completeMessage));
            }
        }
    }
}
