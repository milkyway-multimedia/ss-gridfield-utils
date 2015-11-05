<?php namespace Milkyway\SS\GridFieldUtils\Controllers;
/**
 * Milkyway Multimedia
 * AddExistingPicker.php
 *
 * @package dispoze.com.au
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
    ];

    public function index() {
        return $this->renderWith([
            'GridField_Controllers_AddExistingPicker',
            'GridFieldAddExistingSearchHandler',
        ]);
    }

    public function add($request) {
        if($addHandler = $this->button->getAddHandler()) {
            return call_user_func($addHandler, $request, $this->grid, $this->button);
        }

        $ids = array_unique((array)$request->postVar('ids'));

        if(!$ids || !count($ids)) {
            $this->httpError(400);
            return;
        }

        $items = [];
        $list = $this->grid->getList();

        foreach($ids as $id) {
            $item = DataList::create($list->dataClass())->byID($id);

            if(!$item) {
                $this->httpError(400);
                return;
            }

            $items[] = $item;
        }

        foreach($items as $item) {
            $list->add($item);
        }
    }
}