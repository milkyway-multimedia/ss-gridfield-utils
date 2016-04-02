<?php namespace Milkyway\SS\GridFieldUtils;

/**
 * Milkyway Multimedia
 * EditableRow.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use RequestHandler;
use GridField_HTMLProvider;
use GridField_SaveHandler;
use GridField_URLHandler;
use GridField_ColumnProvider;
use Validator;
use FieldList;
use Session;
use Requirements;

class EditableRow extends RequestHandler implements GridField_HTMLProvider, GridField_SaveHandler, GridField_URLHandler, GridField_ColumnProvider
{
    public $column = '_OpenRowForEditing';
    public $urlSegment = 'editableRow';
    public $setWorkingParentOnRecordTo = 'Parent';
    public $disableToggleStateSave = false;
    public $cacheToggleStateSave = false;
    public $openNewTogglesOnCreate = true;

    protected $permissionCallback;

    protected $itemEditFormCallback;

    protected $fields;

    protected $template;

    protected $validator;

    private $workingGrid;

    private static $allowed_actions = [
        'loadItem',
        'handleForm',
    ];

    /**
     * @param FieldList|callable|array $fields the fields to display in inline form
     */
    public function __construct($fields = null)
    {
        $this->fields = $fields;
        parent::__construct();
    }

    /**
     * Gets the fields for this class
     *
     * @return FieldList|callable|array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Sets the fields that will be displayed in this component
     *
     * @param FieldList|callable|array $fields
     * @return static $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Gets the validator
     *
     * @return Validator|callable|array
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Sets the validator that will be displayed in this component
     *
     * @param \Validator|Callable|array $validator
     * @return static $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Get the permission callback for this column
     *
     * @return callable
     */
    public function getPermissionCallback()
    {
        return $this->permissionCallback;
    }

    /**
     * Sets the permission callback for this column
     *
     * @param callable $permissionCallback
     * @return static $this
     */
    public function setPermissionCallback($permissionCallback)
    {
        $this->permissionCallback = $permissionCallback;

        return $this;
    }

    /**
     * Get the callback for changes on the edit form after constructing it
     *
     * @return callable
     */
    public function getItemEditFormCallback()
    {
        return $this->itemEditFormCallback;
    }

    /**
     * Make changes on the edit form after constructing it.
     *
     * @param callable $itemEditFormCallback
     * @return static $this
     */
    public function setItemEditFormCallback($itemEditFormCallback)
    {
        $this->itemEditFormCallback = $itemEditFormCallback;

        return $this;
    }

    public function getURLHandlers($gridField)
    {
        return [
            $this->urlSegment . '/load/$ID' => 'loadItem',
            $this->urlSegment . '/form/$ID' => 'handleForm',
        ];
    }

    /**
     * Modify the list of columns displayed in the table.
     *
     * @see {@link GridFieldDataColumns->getDisplayFields()}
     * @see {@link GridFieldDataColumns}.
     *
     * @param \GridField $gridField
     * @param           array - List reference of all column names.
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array($this->column, $columns)) {
            array_unshift($columns, $this->column);
        }
    }

    /**
     * Names of all columns which are affected by this component.
     *
     * @param \GridField $gridField
     *
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return [$this->column];
    }

    /**
     * HTML for the column, content of the <td> element.
     *
     * @param  \GridField $gridField
     * @param  \DataObject $record - Record displayed in this row
     * @param  string $columnName
     *
     * @return string - HTML for the column. Return NULL to skip.
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if (!$this->checkPermission($gridField, $record, $columnName)) {
            return '';
        }

        $classes = 'ss-gridfield-editable-row--icon';

        if ($record) {
            $classes .= ' ss-gridfield-editable-row--toggle';
        }

        $openToggleId = 'EditableRowToggles.' . $gridField->ID() . '.' . get_class($record) . '_' . $record->ID;

        if ($this->openNewTogglesOnCreate && Session::get($openToggleId)) {
            $classes .= ' ss-gridfield-editable-row--toggle_start';
        }

        Session::clear($openToggleId);

        return sprintf('<i class="%s"></i>', $classes);
    }

    /**
     * Attributes for the element containing the content returned by {@link getColumnContent()}.
     *
     * @param  \GridField $gridField
     * @param  \DataObject $record displayed in this row
     * @param  string $columnName
     *
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        $this->workingGrid = $gridField;

        if (!$this->checkPermission($gridField, $record, $columnName)) {
            return [];
        }

        return [
            'data-link' => $this->Link('load', $record->ID),
            'class'     => 'ss-gridfield-editable-row--icon-holder',
        ];
    }

    /**
     * Additional metadata about the column which can be used by other components,
     * e.g. to set a title for a search column header.
     *
     * @param \GridField $gridField
     * @param string $columnName
     *
     * @return array - Map of arbitrary metadata identifiers to their values.
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName == $this->column) {
            return [
                'title' => '',
            ];
        }
    }

    public function getHTMLFragments($grid)
    {
        Requirements::css(singleton('env')->get('CDN.font-awesome',
            'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'
        ));
        $grid->addExtraClass('ss-gridfield-editable-rows');
        Utilities::include_requirements();

        if ($this->disableToggleStateSave) {
            $grid->addExtraClass('ss-gridfield-editable-rows_disableToggleState');
        }

        if ($this->cacheToggleStateSave) {
            $grid->addExtraClass('ss-gridfield-editable-rows_allowCachedToggles');
        }

        $this->workingGrid = $grid;
    }

    public function handleSave(\GridField $grid, \DataObjectInterface $record)
    {
        $list = $grid->getList();
        $value = $grid->Value();
        $className = $this->getComponentName();

        if (!isset($value[$className]) || !is_array($value[$className])) {
            return;
        }

        foreach ($value[$className] as $id => $fields) {
            if (!is_numeric($id) || !is_array($fields)) {
                continue;
            }

            $item = $list->byID($id);

            if (!$item || !$item->canEdit()) {
                continue;
            }

            $form = $this->getForm($grid, $item, false);
            $form->loadDataFrom($fields);
            $form->saveInto($item);
            $extra = method_exists($list, 'getExtraFields') ? array_intersect_key($form->Data,
                (array)$list->getExtraFields()) : [];

            $item->write();
            $list->add($item, $extra);
        }
    }

    public function getForm($grid, $record, $removeEditableColumnFields = true)
    {
        $this->workingGrid = $grid;
        $form = \Form::create($this, $grid->ID() . '-EditableRow-' . $record->ID,
            $this->getFieldList($record, $grid, $removeEditableColumnFields), \FieldList::create(),
            $this->getValidatorForForm($record, $grid))->loadDataFrom($record)->setFormAction($this->Link('form',
            $record->ID))->disableSecurityToken();

        if ($form->Fields()->hasTabSet() && ($root = $form->Fields()->findOrMakeTab('Root')) && $root->Template == 'CMSTabSet') {
            $root->setTemplate('');
            $form->removeExtraClass('cms-tabset');
        }

        $callback = $this->getItemEditFormCallback();

        if ($callback) {
            call_user_func($callback, $form, $this, $grid, $record, $removeEditableColumnFields);
        }

        return $form;
    }

    protected function getFieldList($record, $grid = null, $removeEditableColumnFields = true)
    {
        $fields = null;

        if ($this->fields) {
            if ($this->fields instanceof \FieldList) {
                $fields = $this->fields;
            } elseif (is_callable($this->fields)) {
                $fields = call_user_func_array($this->fields, [$record, $grid, $this]);
            } else {
                $fields = \FieldList::create($this->fields);
            }
        }

        if (!$fields && $grid) {
            if ($editable = $grid->getConfig()->getComponentByType('GridFieldDetailForm')) {
                if ($editable->getFields()) {
                    $fields = $editable->getFields();
                } else {
                    $fields = \Object::create($editable->getItemRequestClass(), $grid, $editable, $record,
                        $grid->getForm()->getController(), $editable->getName())->ItemEditForm()->Fields();
                }
            }
        }

        if (!$fields) {
            $fields = $record->hasMethod('getEditableRowFields') ? $record->getEditableRowFields($grid) : $record->getCMSFields();
        }

        if ($removeEditableColumnFields && $grid && $editable = $grid->getConfig()->getComponentByType('GridFieldEditableColumns')) {
            $editableColumns = $editable->getFields($grid, $record);

            foreach ($editableColumns as $column) {
                $fields->removeByName($column->Name);
            }
        }

        return $fields;
    }

    protected function getValidatorForForm($record, $grid = null)
    {
        if ($this->validator) {
            if ($this->validator instanceof \Validator) {
                return $this->validator;
            } elseif (is_callable($this->validator)) {
                return call_user_func_array($this->validator, [$record, $grid, $this]);
            } else {
                return \Validator::create($this->validator);
            }
        }

        if ($grid) {
            if ($editable = $grid->getConfig()->getComponentByType('GridFieldDetailForm')) {
                return $editable->getValidator();
            }
        }

        if ($record->hasMethod('getEditableRowValidator')) {
            return $record->getEditableRowValidator($grid);
        }

        return $record->hasMethod('getCMSValidator') ? $record->getCMSValidator() : null;
    }

    public function handleForm($grid, $request)
    {
        $id = $request->param('ID');
        $record = $this->getRecordFromRequest($grid, $request);
        $form = $this->getForm($grid, $record);
        $class = $this->getComponentName();

        foreach ($form->Fields()->dataFields() as $field) {
            $field->setName(sprintf(
                '%s[%s][%s][%s]', $grid->getName(), $class, $id, $field->getName()
            ));
        }

        $form->setController($grid->getForm()->getController());

//		if(!$request->isGET() && $request->remaining() && ($newGrid = $form->handleRequest($request, \DataModel::inst())) && ($newGrid instanceof $grid) && ($row = $newGrid->getConfig()->getComponentByType(__CLASS__))) {
//			$form = $row->handleForm($newGrid, $request);
//		}

        return $form;
    }

    public function loadItem($grid, $request)
    {
        $record = $this->getRecordFromRequest($grid, $request);
        $form = $this->getForm($grid, $record);
        $this->renameFieldsInCompositeField($form->Fields(), $grid, $record);

        $canEdit = $record->canEdit();
        $canView = $record->canView();

        if (!$canEdit && !$canView) {
            throw new \LogicException('You do not have permission to view this record');
        }

        if (!$canEdit) {
            $form->makeReadonly();
        }

        $countUntilThisColumn = 0;
        foreach ($grid->getColumns() as $column) {
            $countUntilThisColumn++;

            if ($column == $this->column) {
                break;
            }
        }

        if ($countUntilThisColumn == count($grid->getColumns())) {
            $countUntilThisColumn = 0;
        }

        return $record->customise([
            'Form'              => $form,
            'ColumnCount'       => count($grid->getColumns()),
            'PrevColumnsCount'  => $countUntilThisColumn,
            'OtherColumnsCount' => count($grid->getColumns()) - $countUntilThisColumn,
        ])->renderWith(array_merge((array)$this->template, ['GridField_EditableRow']));
    }

    protected function getRecordFromRequest($grid, $request)
    {
        $id = $request->param('ID');
        $list = $grid->getList();

        if (!ctype_digit($id)) {
            throw new \SS_HTTPResponse_Exception(null, 400);
        }

        if (!$record = $list->byID($id)) {
            throw new \SS_HTTPResponse_Exception(null, 404);
        }

        if ($this->setWorkingParentOnRecordTo) {
            if ($grid->List && ($grid->List instanceof \ManyManyList) && $grid->Form && $grid->Form->Record) {
                $record->{$this->setWorkingParentOnRecordTo} = $grid->Form->Record;
            }
        }

        return $record;
    }

    public function Link($action = null, $id = null)
    {
        return $this->workingGrid ? \Controller::join_links($this->workingGrid->Link($this->urlSegment), $action,
            $id) : null;
    }

    protected function renameFieldsInCompositeField($fields, $grid, $record)
    {
        $class = $this->getComponentName();

        foreach ($fields as $field) {
            $field->setName(sprintf(
                '%s[%s][%s][%s]', $grid->getName(), $class, $record->ID, $field->getName()
            ));

            if ($field->isComposite()) {
                $this->renameFieldsInCompositeField($field->FieldList(), $grid, $record);
            }
        }
    }

    private $canView = [];

    protected function checkPermission($gridField, $record, $columnName)
    {
        if (isset($this->canView[$record->ID])) {
            return $this->canView[$record->ID];
        }

        $this->canView[$record->ID] =
            ($this->permissionCallback && call_user_func($this->permissionCallback, $gridField, $record,
                    $columnName)) ||
            (!$this->permissionCallback && ($record->canView() || $record->canEdit()));

        return $this->canView[$record->ID];
    }

    protected function getComponentName()
    {
        return str_replace(['\\', '-'], '_', __CLASS__ . '_' . $this->urlSegment);
    }
}
