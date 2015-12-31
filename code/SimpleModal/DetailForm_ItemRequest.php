<?php namespace Milkyway\SS\GridFieldUtils\SimpleModal;

/**
 * Milkyway Multimedia
 * SimpleModal_ItemRequest.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use LeftAndMain;
use Form;
use Controller;
use FieldList;

class DetailForm_ItemRequest extends \Milkyway\SS\GridFieldUtils\GridFieldDetailForm_ItemRequest
{
    private static $allowed_actions = [
        'undo',
    ];

    public function ItemEditForm()
    {
        $this->beforeExtending('updateItemEditForm', function ($form) {
            $form->Template = '';
            $form->removeExtraClass('cms-content cms-edit-form center');
            $form->setAttribute('data-pjax-fragment', null);

            if ($form->Fields()->hasTabset()) {
                $form->Fields()->findOrMakeTab('Root')->Template = '';
                $form->removeExtraClass('cms-tabset');
            }
        });

        return parent::ItemEditForm();
    }

    public function doSave($data, $form, $request)
    {
        if ($handler = $this->component->getSaveHandler()) {
            return call_user_func($handler, [
                'data'       => $data,
                'form'       => $form,
                'request'    => $request,
                'controller' => $this,
                'grid'       => $this->gridField,
                'record'     => $this->record,
                'component'  => $this->component,
            ]);
        }

        return parent::doSave($data, $form);
    }

    public function undo($request)
    {
        if ($handler = $this->component->getUndoHandler()) {
            return call_user_func($handler, [
                'data' => $request->requestVars(),
                'request'    => $request,
                'controller' => $this,
                'grid'       => $this->gridField,
                'record'     => $this->record,
                'component'  => $this->component,
            ]);
        }

        if (($toplevelController = $this->getToplevelController()) && $toplevelController instanceof LeftAndMain) {
            $form = $toplevelController->getEditForm();
        } else {
            $form = new Form(Controller::curr(), 'Undo-Placeholder', new FieldList(), new FieldList());
        }

        return $this->doDelete($request->requestVars(), $form);
    }
}
