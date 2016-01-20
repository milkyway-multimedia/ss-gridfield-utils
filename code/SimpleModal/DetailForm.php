<?php namespace Milkyway\SS\GridFieldUtils\SimpleModal;

/**
 * Milkyway Multimedia
 * SimpleModal.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use DataModel;
use Milkyway\SS\GridFieldUtils\GridFieldDetailForm;

class DetailForm extends GridFieldDetailForm
{
    public $allowUndo = true;
    public $setWorkingParentOnRecordTo = '';
    public $dimensions = [
        'maxWidth' => 0.8,
        'maxHeight' => 0.8,
        'resizable' => true,
    ];
    public $uriSegment = 'simple-item';

    protected $itemHandlerFactory;
    protected $saveHandler;
    protected $undoSaveNewHandler;

    protected $fields;
    protected $validator;

    public function __construct($name = 'DetailForm', $uriSegment = 'simple-item')
    {
        parent::__construct($name, $uriSegment);
    }

    /**
     * Set a item handler factory, which can create a custom RequestHandler
     * to be used for viewing the modal
     *
     * @param callable $itemHandlerFactory
     * @return self $this
     */
    public function setItemHandlerFactory($itemHandlerFactory)
    {
        $this->itemHandlerFactory = $itemHandlerFactory;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getItemHandlerFactory()
    {
        return $this->itemHandlerFactory;
    }

    /**
     * Sets a custom handler for when the add action is called
     *
     * @param callable $saveHandler
     * @return self $this
     */
    public function setSaveHandler($saveHandler)
    {
        $this->saveHandler = $saveHandler;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getSaveHandler()
    {
        return $this->saveHandler;
    }

    /**
     * Sets a custom handler for undoing the save action
     * (only works for new records)
     *
     * @param callable $undoSaveNewHandler
     * @return self $this
     */
    public function setUndoSaveNewHandler($undoSaveNewHandler)
    {
        $this->undoSaveNewHandler = $undoSaveNewHandler;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getUndoSaveNewHandler()
    {
        return $this->undoSaveNewHandler;
    }

    /**
     * Enable undo of adding an item
     *
     * @param bool $allowUndo
     * @return self $this
     */
    public function allowUndo($allowUndo = true)
    {
        $this->allowUndo = $allowUndo;
        return $this;
    }

    /**
     * @return bool
     */
    public function canUndo()
    {
        return $this->allowUndo;
    }

    public function handleItem($grid, $request)
    {
        if ($this->itemHandlerFactory) {
            return call_user_func($this->itemHandlerFactory, $grid, $this, $request)->handleRequest($request, DataModel::inst());
        }

        return parent::handleItem($grid, $request);
    }
}
