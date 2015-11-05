<?php namespace Milkyway\SS\GridFieldUtils;
/**
 * Milkyway Multimedia
 * AddNewInlineExtended.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

class AddNewInlineExtended_MultiClass extends AddNewInlineExtended
{
	public $allowedClasses;

	public $showEmptyString = true;

	private $useAllowedClasses;

	public function getHTMLFragments($grid) {
		$classes = $this->getAllowedClasses($grid);

		if(count($classes)) {
			$this->useAllowedClasses = $classes;
			return parent::getHTMLFragments($grid);
		}
		else {
			return [];
		}
	}

	/**
	 * Gets the classes that can be created using this button, defaulting to the model class and
	 * its subclasses.
	 *
	 * @param \GridField $grid
	 * @return array a map of class name to title
	 */
	public function getAllowedClasses(\GridField $grid) {
		$result = array();

		if($this->useAllowedClasses) {
			$classes = $this->useAllowedClasses;
			$this->useAllowedClasses = null;
			return $classes;
		}
		else if(is_null($this->allowedClasses)) {
			$classes = array_values(\ClassInfo::subclassesFor($grid->getModelClass()));
			sort($classes);
		} else {
			$classes = $this->allowedClasses;
		}

		foreach($classes as $class => $title) {
			if(!is_string($class)) {
				$class = $title;
				$title = singleton($class)->i18n_singular_name();
			}

			if(!singleton($class)->canCreate()) {
				continue;
			}

			$result[$class] = $title;
		}

		return $result;
	}

	protected function getButtonFragment($grid) {
		$field = \DropdownField::create(
				sprintf('%s[ClassName]', str_replace('\\', '_', __CLASS__)),
				'',
				$this->getAllowedClasses($grid)
			)
			->addExtraClass('no-change-track ss-gridfield-inline-new-extended--class-selector')
			;

		if($this->showEmptyString) {
			$field->setEmptyString($this->showEmptyString !== true ? $this->showEmptyString : _t('GridFieldExtensions.SELECTTYPETOCREATE', '(Select type to create)'));
		}

		return \ArrayData::create([
			'Title' => $this->getTitle(),
			'Ajax' => true,
			'Link' => $this->Link('load'),
			'ClassField' => $field,
		])->renderWith($this->buttonTemplate);
	}
}