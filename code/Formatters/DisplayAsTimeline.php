<?php namespace Milkyway\SS\GridFieldUtils;

/**
 * Milkyway Multimedia
 * DisplayAsTimeline.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use DataObject;
use GridField;

class DisplayAsTimeline implements \GridField_ColumnProvider
{
    /** @var string */
    protected $iconField;

    /** @var array */
    protected $iconClasses;

    /** @var string */
    protected $addAttributesFromMethodOnRecord;

    public function __construct($iconField = '', $iconClasses = [], $addAttributesFromMethodOnRecord = null)
    {
        $this->iconField = $iconField;
        $this->iconClasses = $iconClasses;
        $this->addAttributesFromMethodOnRecord = $addAttributesFromMethodOnRecord;
    }

    /**
     * Modify the list of columns displayed in the table.
     *
     * @see {@link GridFieldDataColumns->getDisplayFields()}
     * @see {@link GridFieldDataColumns}.
     *
     * @param GridField $gridField
     * @param array $columns - List reference of all column names.
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('TimelineIcon', $columns)) {
            array_unshift($columns, 'TimelineIcon');
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
        return ['TimelineIcon'];
    }

    /**
     * HTML for the column, content of the <td> element.
     *
     * @param  GridField $gridField
     * @param  DataObject $record - Record displayed in this row
     * @param  string $columnName
     *
     * @return string - HTML for the column. Return NULL to skip.
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        $iconField = $this->getColumnForIcon($columnName);
        return '<div class="icon-timeline">' . $record->$iconField . '</div>';
    }

    /**
     * Attributes for the element containing the content returned by {@link getColumnContent()}.
     *
     * @param  GridField $gridField
     * @param  DataObject $record displayed in this row
     * @param  string $columnName
     *
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        Utilities::include_requirements();
        $gridField->addExtraClass('ss-gridfield-timeline');

        $iconClasses = array_merge([$this->getColumnForIcon($columnName), 'TimelineIcon'], $this->iconClasses);

        if ($this->addAttributesFromMethodOnRecord) {
            return array_merge($iconClasses, $record->{$this->addAttributesFromMethodOnRecord});
        }

        return [
            'class' => implode(' ', array_unique($iconClasses)),
        ];
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
        if ($columnName == 'TimelineIcon') {
            return [
                'title' => '',
            ];
        }
    }


    /**
     * @param $columnName
     *
     * @return mixed
     */
    protected function getColumnForIcon($columnName)
    {
        $iconField = $this->iconField ?: $columnName;

        return $iconField;
    }
}
