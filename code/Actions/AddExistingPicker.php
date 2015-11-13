<?php namespace Milkyway\SS\GridFieldUtils;

/**
 * Milkyway Multimedia
 * AddExistingPicker.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

if (!class_exists('GridFieldAddExistingSearchButton')) {
    return;
}

use GridFieldAddExistingSearchButton as AddExistingSearchButton;
use GridFieldExtensions;
use ArrayData;
use Milkyway\SS\GridFieldUtils\Controllers\AddExistingPicker as Controller;

class AddExistingPicker extends AddExistingSearchButton
{
    protected $searchHandlerFactory;
    protected $addHandler;
    protected $undoHandler;
    protected $urlSegment;
    public $async = true;

    /**
     * Set a search handler factory, which can create a custom RequestHandler
     * to be used for searching
     *
     * @param callable $searchHandlerFactory
     * @return self $this
     */
    public function setSearchHandlerFactory($searchHandlerFactory)
    {
        $this->searchHandlerFactory = $searchHandlerFactory;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getSearchHandlerFactory()
    {
        return $this->searchHandlerFactory;
    }

    /**
     * Sets a custom handler for when the add action is called
     *
     * @param callable $addHandler
     * @return self $this
     */
    public function setAddHandler($addHandler)
    {
        $this->addHandler = $addHandler;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getAddHandler()
    {
        return $this->addHandler;
    }

    /**
     * Sets a custom handler for undoing the add action
     *
     * @param callable $undoHandler
     * @return self $this
     */
    public function setUndoHandler($undoHandler)
    {
        $this->undoHandler = $undoHandler;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getUndoHandler()
    {
        return $this->undoHandler;
    }

    /**
     * @return string
     */
    public function getUrlSegment()
    {
        return $this->urlSegment;
    }

    /**
     * @param string $urlSegment
     */
    public function setUrlSegment($urlSegment = '')
    {
        $this->urlSegment = $urlSegment;
        return $this;
    }

    /**
     * Enable the async picker, when an item is clicked in the list
     * it is automatically added to the list, with an undo option.
     *
     * @param bool $async
     * @return self $this
     */
    public function async($async = true)
    {
        $this->async = $async;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAsync()
    {
        return $this->async;
    }

    public function handleSearch($grid, $request)
    {
        if ($this->searchHandlerFactory) {
            return call_user_func($this->searchHandlerFactory, $grid, $this, $request);
        } else {
            return Controller::create(
                $grid,
                $this,
                $request
            );
        }
    }

    public function getHTMLFragments($grid)
    {
        GridFieldExtensions::include_requirements();
        Utilities::include_requirements();

        $data = ArrayData::create([
            'Title' => $this->getTitle(),
            'Link'  => $grid->Link($this->urlSegment ?: 'add-existing-search'),
        ]);

        if ($this->async) {
            $grid->addExtraClass('ss-gridfield-add-existing-picker_async');
        }

        return [
            $this->fragment => $data->renderWith([
                'GridField_AddExistingPicker',
                'GridFieldAddExistingSearchButton',
            ]),
        ];
    }

    public function getURLHandlers($grid)
    {
        return [
            $this->urlSegment ?: 'add-existing-search' => 'handleSearch',
        ];
    }
}