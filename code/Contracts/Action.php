<?php namespace Milkyway\SS\GridFieldUtils\Contracts;

use GridField_ColumnProvider;
use GridField_ActionProvider;

/**
 * Milkyway Multimedia
 * Action.php
 *
 * @package rugwash.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */
abstract class Action implements GridField_ColumnProvider, GridField_ActionProvider {
	protected $actionColumn = 'Actions';

	/**
	 * Add a column 'Delete'
	 *
	 * @param \GridField $gridField
	 * @param array $columns
	 */
	public function augmentColumns($gridField, &$columns) {
		if(!in_array($this->actionColumn, $columns)) {
			$columns[] = $this->actionColumn;
		}
	}

	/**
	 * Return any special attributes that will be used for FormField::create_tag()
	 *
	 * @param \GridField $gridField
	 * @param \DataObjectInterface $record
	 * @param string $columnName
	 * @return array
	 */
	public function getColumnAttributes($gridField, $record, $columnName) {
		return ['class' => 'col-buttons'];
	}

	/**
	 * Add the title
	 *
	 * @param \GridField $gridField
	 * @param string $columnName
	 * @return array
	 */
	public function getColumnMetadata($gridField, $columnName) {
		if($columnName == $this->actionColumn) {
			return ['title' => ''];
		}
	}

	/**
	 * Which columns are handled by this component
	 *
	 * @param \GridField $gridField
	 * @return array
	 */
	public function getColumnsHandled($gridField) {
		return [$this->actionColumn];
	}
}