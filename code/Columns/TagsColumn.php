<?php namespace Milkyway\SS\GridFieldUtils;

/**
 * Milkyway Multimedia
 * TagsColumn.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use DataList;
use DataObjectInterface;
use GridField;
use GridField_ColumnProvider;
use GridField_DataManipulator;
use GridField_URLHandler;
use GridField_SaveHandler;
use FormField;
use SS_List;
use Form;
use FieldList;
use Controller;
use Object;

use SS_HTTPResponse_Exception;

class TagsColumn implements GridField_ColumnProvider, GridField_DataManipulator, GridField_URLHandler, GridField_SaveHandler
{
    private static $allowed_actions = [
        'form',
        'new',
        'filter',
    ];

    public $columnTitle = 'Select';

    public $referenceField = 'Title';
    public $valueField = 'ID';

    public $sourceList;

    public $allowNewItems = true;
    public $addHandler;

    protected $relation;

    public function __construct($relation, $columnTitle = '')
    {
        $this->relation = $relation;
        $this->columnTitle = $columnTitle ?: FormField::name_to_label($relation);
    }

    public function getManipulatedData(GridField $grid, SS_List $list)
    {
        return $list;
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
        if (!in_array($this->relation, $columns)) {
            $columns[] = $this->relation;
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
        return [$this->relation];
    }

    /**
     * HTML for the column, content of the <td> element.
     *
     * @param  \GridField $grid
     * @param  \DataObject $record - Record displayed in this row
     * @param  string $columnName
     *
     * @return string - HTML for the column. Return NULL to skip.
     */
    public function getColumnContent($grid, $record, $columnName)
    {
        $form = $this->getForm($grid, $record);

        if(!$form) {
            return '';
        }

        $field = $form->Fields()->first();

        $field->setName($this->getFieldName($field->getName(), $grid, $record));

        return $field->Field();
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
        return [
            'class'         => 'ss-gridfield-tagsColumn-holder ss-gridfield-tagsColumn-col_' . $columnName,
            'data-relation' => $this->relation,
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
        return ['title' => $this->columnTitle];
    }

    public function getURLHandlers($grid) {
        return array(
            'tag/' . $this->relation . '/form/$ID' => 'handleForm',
            'tag/' . $this->relation . '/new/$ID/$Tag' => 'handleAdd',
            'tag/' .  $this->relation . '/filter/$Tag' => 'filterByTag',
        );
    }

    public function handleAdd(GridField $grid, $request) {
        if($this->addHandler) {
            return call_user_func($this->addHandler, $request, $grid, $this->grabParamsCallback($grid, $request));
        }

        $params = call_user_func($this->grabParamsCallback($grid, $request));

        if(!$params) {
            return;
        }

        $model = $this->sourceList ? $this->sourceList->dataClass() : $params['record']->{$this->relation}()->dataClass();

        if($item = singleton($model)->get()->filter($this->referenceField, $params['tag'])->first()) {
            $params['record']->{$this->relation}()->add($item);
        }
        else {
            $item = Object::create($model);
            $item->{$this->referenceField} = $params['tag'];
            $item->write();
            $params['record']->{$this->relation}()->add($item);
        }
    }

    public function handleForm(GridField $grid, $request) {
        $params = call_user_func($this->grabParamsCallback($grid, $request));

        $form = $this->getForm($grid, $params['record']);

        if(!$form) {
            throw new SS_HTTPResponse_Exception(null, 400);
        }

        foreach($form->Fields() as $field) {
            $field->Name = $this->getFieldName($field->Name, $grid, $params['record']);
        }

        return $form;
    }

    /**
     * Gets the form instance for a record.
     *
     * @param GridField $grid
     * @param DataObjectInterface $record
     * @return \Form
     */
    public function getForm(GridField $grid, DataObjectInterface $record) {
        $dropdown = $this->getSelectField($record, $grid);

        if(!$dropdown) {
            return false;
        }

        $form = new Form($this, null, new FieldList($dropdown), new FieldList());
        $form->loadDataFrom($record);

        $form->setFormAction(Controller::join_links(
            $grid->Link(), 'tag', $this->relation, 'form', $record->ID
        ));

        return $form;
    }

    public function handleSave(GridField $grid, DataObjectInterface $record)
    {
        // TODO: Implement handleSave() method.
    }

    protected function getFieldName($name,  GridField $grid, DataObjectInterface $record) {
        return sprintf(
            '%s[%s][%s][%s]', $grid->getName(), str_replace('\\', '_', __CLASS__), $record->ID, $name
        );
    }

    protected $_saveSourceList;

    protected function getSelectField($record, $grid) {
        $list = $record->{$this->relation}();

        if (!$list || !singleton($list->dataClass())->canView() || !$record->exists()) {
            return '';
        }

        Utilities::include_requirements();

        if(!$this->_saveSourceList) {
            $this->_saveSourceList = DataList::create($list->dataClass())->map()->toArray();
        }


        return Object::create(
            'Select2Field',
            $this->relation,
            $list,
            $this->sourceList ?: DataList::create($list->dataClass()),
            null,
            $this->referenceField,
            $this->valueField
        )
            ->addExtraClass('ss-gridfield--tags-column--selector')
            ->setAttribute('multiple', true)
            ->setAttribute('data-tags', true)
            ->setAttribute('data-gf-filter-link', Controller::join_links($grid->Link(), 'tag', $this->relation, 'filter', '{{ tag }}'))
            ->setAttribute('data-gf-new-link', Controller::join_links($grid->Link(), 'tag', $this->relation, 'new', $record->ID, '{{ tag }}'))
            ->setPrefetch(50)
            ->requireSelection(!$this->allowNewItems);
    }

    protected function grabParamsCallback($grid, $request) {
        return function() use($grid, $request) {
            $id = $request->param('ID');

            if(!ctype_digit($id)) {
                throw new SS_HTTPResponse_Exception(null, 400);
            }

            if(!$record = $grid->List->byID($id)) {
                throw new SS_HTTPResponse_Exception(null, 404);
            }

            return [
                'record' => $record,
                'tag' => $request->param('Tag'),
            ];
        };
    }
}