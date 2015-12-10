<?php namespace Milkyway\SS\GridFieldUtils;

/**
 * Milkyway Multimedia
 * GridFieldAddNewOrExistingInlineButton.php
 *
 * This component must be added before GridFieldEditableColumns to work properly
 *
 * @package milkyway-multimedia/ss-mwm-autocomplete
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

if (!class_exists('GridFieldAddNewInlineButton')) {
    return;
}

use Select2Field;

use Convert;
use Object;
use ManyManyList;
use Form;
use DataList;
use DataObjectInterface;
use GridField;
use GridField_ColumnProvider;
use GridFieldDataColumns;

use Exception;

class AddNewOrExistingInlineButton extends \GridFieldAddNewInlineButton implements
    GridField_ColumnProvider
{

    /** @var string The reference field that will be shown in dropdown */
    public $refField = 'Title';

    /** @var string The value field that will be used when saving record to database */
    public $valField = 'ID';

    /** @var string The value field that this field will transform to when saving to database if an ID is not used (also field used for existing records) */
    public $valFieldAfterSave = 'Title';

    /** @var string|callable|\Closure A callback for the value field (otherwise scaffolded from record) */
    public $valFieldCallback;

    /** @var \DataList Use a different DataList to fill out the dropdown menu */
    public $list;

    /** @var boolean Allow new items to be added (otherwise must choose from dropdown) */
    public $allowNewItems = true;

    /** @var boolean No item is selected at first by default */
    public $hasEmptyDefault = true;

    /** @var boolean Additional data to save with new record */
    public $additionalData = [];

    public function __construct(
        $fragment = 'buttons-before-left',
        $valFieldAfterSave = 'Title',
        $refField = 'Title',
        $valField = 'ID'
    ) {
        parent::__construct($fragment);

        $this->valFieldAfterSave = $valFieldAfterSave;
        $this->refField = $refField;
        $this->valField = $valField;
    }

    /**
     * If the record exists, will use the valFieldAfterSave callback, otherwise
     * it will try to find an object by ID, and if it can't it will save to valFieldAfterSave
     *
     * @param \GridField $grid
     * @param \DataObjectInterface $record
     *
     * @throws \ValidationException
     */
    public function handleSave(GridField $grid, DataObjectInterface $record)
    {
        $list = $grid->getList();
        $value = $grid->Value();

        $editable = $grid->getConfig()->getComponentByType('GridFieldEditableColumns');

        if ($editable) {
            $this->addFallbackValueToDisplayFields($grid, $editable);
        }

        if (!isset($value['GridFieldAddNewInlineButton']) || !is_array($value['GridFieldAddNewInlineButton'])) {
            return;
        }

        $class = $grid->getModelClass();

        if (!singleton($class)->canCreate()) {
            return;
        }

        $form = $editable->getForm($grid, $record);

        foreach ($value['GridFieldAddNewInlineButton'] as $fields) {
            $item = null;

            if (isset($fields['_AddOrExistingID']) && !$list->byID($fields['_AddOrExistingID'])) {
                if ($item = DataList::create($class)->byID($fields['_AddOrExistingID'])) {
                    unset($fields['_AddOrExistingID']);
                } elseif (!isset($fields[$this->valFieldAfterSave])) {
                    $fields[$this->valFieldAfterSave] = $fields['_AddOrExistingID'];
                }
            }

            $fields = array_merge($this->additionalData, $fields);

            if (!$item) {
                $item = $class::create();
            }

            $extra = [];

            if ($item->exists()) {
                $form->loadDataFrom($item)->loadDataFrom($fields);
            } else {
                $form->loadDataFrom($fields, Form::MERGE_CLEAR_MISSING);
            }

            $form->saveInto($item);

            if ($list instanceof ManyManyList) {
                $extra = array_intersect_key(array_merge($this->additionalData, $form->getData()),
                    (array)$list->getExtraFields());
            }

            $item->write();
            $list->add($item, $extra);
        }
    }

    /**
     * Modify the list of columns displayed in the table.
     *
     * @see {@link GridFieldDataColumns->getDisplayFields()}
     * @see {@link GridFieldDataColumns}.
     *
     * @param GridField $gridField
     * @param array - List reference of all column names.
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array($this->valFieldAfterSave, $columns)) {
            $columns[] = $this->valFieldAfterSave;
        }
    }

    /**
     * Attributes for the element containing the content returned by {@link getColumnContent()}.
     *
     * @param  GridField $gridField
     * @param  \DataObject $record displayed in this row
     * @param  string $columnName
     *
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return [
            'class' => 'col-addOrExistingId',
        ];
    }

    /**
     * @param GridField $gridField
     * @param \DataObject $record
     * @param string $columnName
     *
     * @return string
     * @throws \Exception
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if (!$editable = $gridField->getConfig()->getComponentByType('GridFieldEditableColumns')) {
            throw new Exception('Inline adding requires the editable columns component');
        }

        $field = $this->getColumnField($gridField, $record, $columnName)->setForm($editable->getForm($gridField,
            $record));

        $field->setValue($record->{$field->Name})->setName(sprintf(
            '%s[%s][%s][%s]', $gridField->getName(), get_class($editable), $record->ID, $field->Name
        ));

        return $field->Field();
    }

    public function getColumnField($gridField, $record, $columnName)
    {
        if ($record->ID) {
            $field = $this->getValFieldAfterSaveFormField($record);
        } else {
            $list = $this->list ? $this->list : DataList::create($gridField->List->dataClass())->subtract($gridField->List);
            $first = $list->first();
            $field = Select2Field::create('_AddOrExistingID', $columnName, '', $list, '', $this->refField,
                $this->valField)->setEmptyString(_t('GridFieldAddNewOrExistingInlineButton.AddOrSelectExisting',
                'Add or select existing'))->setMinimumSearchLength(0)->requireSelection(!$this->allowNewItems)->setHasEmptyDefault($this->hasEmptyDefault);
            if ($first && !$this->hasEmptyDefault) {
                $field->setValue($first->ID);
            }
        }

        return $field;
    }

    /**
     * Additional metadata about the column which can be used by other components,
     * e.g. to set a title for a search column header.
     *
     * @param GridField $gridField
     * @param string $columnName
     *
     * @return array - Map of arbitrary metadata identifiers to their values.
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName == $this->valFieldAfterSave) {
            return ['title' => $this->valFieldAfterSave];
        }
    }

    /**
     * Names of all columns which are affected by this component.
     *
     * @param GridField $gridField
     *
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return [$this->valFieldAfterSave];
    }

    public function getHTMLFragments($grid)
    {
        $return = parent::getHTMLFragments($grid);

        if (isset($return['after'])) {
            $return['after'] = $this->getRowTemplate($grid, $return['after']);
        }

        return $return;
    }

    protected function getValFieldAfterSaveFormField($record)
    {
        if ($this->valFieldCallback) {
            if (is_callable($this->valFieldCallback)) {
                return call_user_func($this->valFieldCallback, $record);
            } else {
                return Object::create($this->valFieldCallback, $this->valFieldAfterSave, '');
            }
        }

        return $record->scaffoldFormFields(['restrictFields' => [$this->valFieldAfterSave]])->pop();
    }

    private function getRowTemplate(GridField $grid, $after)
    {
        $attrs = '';

        if ($grid->getList()) {
            $record = Object::create($grid->getModelClass());
        } else {
            $record = null;
        }

        foreach ($grid->getColumnAttributes($record, $this->valFieldAfterSave) as $attr => $val) {
            $attrs .= sprintf(' %s="%s"', $attr, Convert::raw2att($val));
        }

        $field = $this->getColumnField($grid, $record, $this->valFieldAfterSave);
        $field->setName(sprintf(
            '%s[%s][{%%=o.num%%}][%s]', $grid->getName(), 'GridFieldAddNewInlineButton', $field->getName()
        ));

        return str_replace('<td class="col-addOrExistingId">', '<td class="col-addOrExistingId">' . $field->Field(),
            $after);
    }

    /**
     * This will add the fallback value (val field after save) to the display fields component,
     * so it can be saved by @GridFieldEditableColumns
     *
     * @param GridField $grid
     * @param \GridFieldDataColumns $editable
     */
    private function addFallbackValueToDisplayFields(GridField $grid, GridFieldDataColumns $editable)
    {
        $fields = $editable->getDisplayFields($grid);

        if (!isset($fields[$this->valFieldAfterSave])) {
            $editable->setDisplayFields($fields + [$this->valFieldAfterSave => $this->valFieldAfterSave]);
        }
    }
}