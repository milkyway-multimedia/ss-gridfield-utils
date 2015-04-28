<?php
/**
 * Milkyway Multimedia
 * HasOneSelector.php
 *
 * @package milkywaymultimedia.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\GridFieldUtils;


class HasOneSelector implements \GridField_ColumnProvider, \GridField_SaveHandler, \GridField_HTMLProvider {
	public $resetButtonTitle;
	public $columnTitle = 'Select';
	public $targetFragment;

	protected $relation;

	public function __construct($relation, $columnTitle = '', $targetFragment = 'before') {
		$this->relation = $relation;
		$this->columnTitle = $columnTitle ?: \FormField::name_to_label($relation);
		$this->targetFragment = $targetFragment;
		$this->resetButtonTitle = _t('GridField_HasOneSelector.RESET', 'Reset {columnTitle}', ['columnTitle' => $this->columnTitle]);
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
		if(!in_array($this->relation, $columns))
			$columns[] = $this->relation;
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
	 * @param  \GridField  $gridField
	 * @param  \DataObject $record - Record displayed in this row
	 * @param  string     $columnName
	 *
	 * @return string - HTML for the column. Return NULL to skip.
	 */
	public function getColumnContent($gridField, $record, $columnName)
	{
		$value = $gridField && $gridField->Form && $gridField->Form->Record ? $gridField->Form->Record->{$this->relation.'ID'} : '';

		return $record->ID ? _t(
			'GridField_HasOneSelector.SELECTOR',
			'<input type="radio" name="{name}" value="{value}"{selected}/>',
			[
				'name' => sprintf('%s[%s]', $gridField->getName(), str_replace('\\', '_', __CLASS__)),
		        'value' => $record->ID,
		        'selected' => $value == $record->ID ? ' checked="checked"' : '',
			]
		) : '';
	}

	/**
	 * Attributes for the element containing the content returned by {@link getColumnContent()}.
	 *
	 * @param  \GridField  $gridField
	 * @param  \DataObject $record displayed in this row
	 * @param  string     $columnName
	 *
	 * @return array
	 */
	public function getColumnAttributes($gridField, $record, $columnName)
	{
		return [
			'class' => 'ss-gridfield-hasOneSelector-holder ss-gridfield-hasOneSelector-col_' . $columnName,
		    'data-relation' => $this->relation,
		];
	}

	/**
	 * Additional metadata about the column which can be used by other components,
	 * e.g. to set a title for a search column header.
	 *
	 * @param \GridField $gridField
	 * @param string    $columnName
	 *
	 * @return array - Map of arbitrary metadata identifiers to their values.
	 */
	public function getColumnMetadata($gridField, $columnName)
	{
		return ['title' => $this->columnTitle];
	}

	public function getHTMLFragments($grid) {
		Utilities::include_requirements();

		return [
			$this->targetFragment => \ArrayData::create([
				'Title' => $this->resetButtonTitle,
				'Relation' => $this->relation,
			])->renderWith('GridField_HasOneSelector'),
		];
	}

	public function handleSave(\GridField $grid, \DataObjectInterface $record) {
		$value = $grid->Value();

		if(!isset($value[str_replace('\\', '_', __CLASS__)])) {
			$value[str_replace('\\', '_', __CLASS__)] = 0;
		}

		if($record->hasMethod('save'.$this->relation.'FromGridField'))
			$record->{'save'.$this->relation.'FromGridField'}((int)$value[str_replace('\\', '_', __CLASS__)], $value);
		else
			$record->{$this->relation.'ID'} = (int)$value[str_replace('\\', '_', __CLASS__)];
	}
}