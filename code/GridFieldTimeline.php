<?php
/**
 * Milkyway Multimedia
 * GridFieldWithTemplate.php
 *
 * @package reggardocolaianni.com
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\GridFieldUtils;


use GridField;

class GridFieldTimeline implements \GridField_ColumnProvider {
	/** @var string */
	protected $iconField;

	public function __construct($iconField = '') {
		$this->iconField = $iconField = '';
	}

	/**
	 * Modify the list of columns displayed in the table.
	 *
	 * @see {@link GridFieldDataColumns->getDisplayFields()}
	 * @see {@link GridFieldDataColumns}.
	 *
	 * @param GridField $gridField
	 * @param           array - List reference of all column names.
	 */
	public function augmentColumns($gridField, &$columns)
	{
		if(!in_array('Timeline-Icon', $columns))
			array_unshift($columns, 'Timeline-Icon');
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
		return ['Timeline-Icon'];
	}


} 