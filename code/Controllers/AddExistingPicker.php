<?php namespace Milkyway\SS\GridFieldUtils\Controllers;
/**
 * Milkyway Multimedia
 * AddExistingPicker.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

if(!class_exists('GridFieldAddExistingSearchButton')) {
    return;
}

use DataList;

class AddExistingPicker extends \GridFieldAddExistingSearchHandler
{
    private static $allowed_actions = [
        'index',
        'add',
        'undo',
    ];

    public function index() {
        return $this->renderWith([
            'GridField_Controllers_AddExistingPicker',
            'GridFieldAddExistingSearchHandler',
        ]);
    }

    public function add($request) {
        if($handler = $this->button->getAddHandler()) {
            return call_user_func($handler, $request, $this->grid, $this->button);
        }

        $items = $this->checkAccess(array_unique((array)$request->postVar('ids')));

        if($items === false) {
            return;
        }

        $list = $this->grid->getList();

        foreach($items as $item) {
            $list->add($item);
        }
    }

    public function undo($request) {
        if($handler = $this->button->getUndoHandler()) {
            return call_user_func($handler, $request, $this->grid, $this->button);
        }

        $items = $this->checkAccess(array_unique((array)$request->postVar('ids')));

        if($items === false) {
            return;
        }

        $list = $this->grid->getList();

        foreach($items as $item) {
            $list->remove($item);
        }
    }

    protected function checkAccess($ids) {
        if(!$ids || !count($ids)) {
            $this->httpError(400);
            return false;
        }

        $items = [];
        $list = $this->grid->getList();

        foreach($ids as $id) {
            $item = DataList::create($list->dataClass())->byID($id);

            if(!$item) {
                $this->httpError(400);
                return false;
            }

            $items[] = $item;
        }

        return count($items) ? $items : false;
    }

    public function isAsync() {
        return $this->button->isAsync();
    }
}