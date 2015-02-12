<?php
/**
 * Milkyway Multimedia
 * AddNewInlineExtended.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\GridFieldUtils;

class AddNewInlineExtended extends \RequestHandler implements \GridField_HTMLProvider, \GridField_SaveHandler, \GridField_URLHandler {
	public $urlSegment = 'extendedInline';

	protected $fragment;

	protected $title;

	protected $fields;

	protected $template;

	protected $validator;

	private $workingGrid;

	private static $allowed_actions = [
		'handleForm',
	];

	public function getURLHandlers($gridField) {
		return array(
			$this->urlSegment.'/$Form!' => 'handleForm',
		);
	}

	/**
	 * @param string $fragment the fragment to render the button in
	 * @param string $title the text to display on the button
	 * @param \FieldList|Callable|array $fields the fields to display in inline form
	 */
	public function __construct($fragment = 'buttons-before-left', $title = '', $fields = null) {
		$this->fragment = $fragment;
		$this->title = $title ?: _t('GridFieldExtensions.ADD', 'Add');
		$this->fields = $fields;
	}

	/**
	 * Gets the fragment name this button is rendered into.
	 *
	 * @return string
	 */
	public function getFragment() {
		return $this->fragment;
	}

	/**
	 * Sets the fragment name this button is rendered into.
	 *
	 * @param string $fragment
	 * @return static $this
	 */
	public function setFragment($fragment) {
		$this->fragment = $fragment;
		return $this;
	}

	/**
	 * Gets the button title text.
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the button title text.
	 *
	 * @param string $title
	 * @return static $this
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 * Gets the fields for this class
	 *
	 * @return \FieldList|Callable|array
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Sets the fields that will be displayed in this component
	 *
	 * @param \FieldList|Callable|array $fields
	 * @return static $this
	 */
	public function setFields($fields) {
		$this->fields = $fields;
		return $this;
	}

	/**
	 * Gets the validator
	 *
	 * @return \Validator|Callable|array
	 */
	public function getValidator() {
		return $this->validator;
	}

	/**
	 * Sets the validator that will be displayed in this component
	 *
	 * @param \Validator|Callable|array $validator
	 * @return static $this
	 */
	public function setValidator($validator) {
		$this->validator = $validator;
		return $this;
	}

	public function getHTMLFragments($grid) {
		if($grid->getList() && !singleton($grid->getModelClass())->canCreate()) {
			return [];
		}

		$fragment = $this->getFragment();
		$grid->addExtraClass('ss-gridfield-add-inline-extended--table');

		\Requirements::javascript(THIRDPARTY_DIR . '/javascript-templates/tmpl.js');
		Utilities::include_requirements();

		$data = \ArrayData::create([
			'Title'  => $this->getTitle(),
		]);

		return array(
			$fragment => $data->renderWith('GridField_AddNewInlineExtended_Button'),
			'after'   => $this->getRowTemplate($grid)
		);
	}

	protected function getRowTemplate($grid) {
		$class = str_replace('\\', '_', __CLASS__);
		$form = $this->getForm($grid, '-{%=o.num%}')->setHTMLID('Form-'.$class.'-{%=o.num%}');
		$fields = $form->Fields()->dataFields();

		foreach($fields as $field) {
			$field->setName(sprintf(
				'%s[%s][{%%=o.num%%}][%s]', $grid->getName(), $class, $field->getName()
			));
		}

		return \ArrayData::create([
			'Form' => $form,
			'ColumnCount' => count($grid->getColumns()),
			'ColumnCountWithoutActions' => count($grid->getColumns()) - 1,
		    'Model' => (($record = $this->getRecordFromGrid($grid)) && $record->hasMethod('i18n_singular_name')) ? $record->i18n_singular_name() : _t('GridFieldUtils.ITEM', 'Item'),
			]
		)->renderWith(array_merge((array)$this->template, ['GridField_AddNewInlineExtended']));
	}

	public function handleSave(\GridField $grid, \DataObjectInterface $record) {
		$list  = $grid->getList();
		$value = $grid->Value();
		$className = str_replace('\\', '_', __CLASS__);

		if(!isset($value[$className]) || !is_array($value[$className])) {
			return;
		}

		$class    = $grid->getModelClass();

		if(!singleton($class)->canCreate()) {
			return;
		}

		$form     = $this->getForm($grid);

		foreach($value[$className] as $fields) {
			$item  = \Object::create($class);

			$form->loadDataFrom($fields);
			$form->saveInto($item);
			$extra = array_intersect_key($form->Data, (array) $list->getExtraFields());

			$item->write();
			$list->add($item, $extra);
		}
	}

	protected function getForm($grid, $append = '') {
		$this->workingGrid = $grid;
		return \Form::create($this, 'Form-'.$grid->getModelClass().$append, $this->getFieldList($grid), \FieldList::create(), $this->getValidatorForForm($grid));
	}

	protected function getFieldList($grid = null) {
		if($this->fields) {
			if($this->fields instanceof \FieldList)
				return $this->fields;
			elseif(is_callable($this->fields))
				return call_user_func_array($this->fields, [$this->getRecordFromGrid($grid), $grid, $this]);
			else
				return \FieldList::create($this->fields);
		}

		if($grid) {
			if($editable = $grid->getConfig()->getComponentByType('Milkyway\SS\GridFieldUtils\EditableRow'))
				return $editable->getForm($grid, $this->getRecordFromGrid($grid))->Fields();
			if($editable = $grid->getConfig()->getComponentByType('GridFieldEditableColumns'))
				return $editable->getFields($grid, $this->getRecordFromGrid($grid));
		}

		if($record = $this->getRecordFromGrid($grid))
			return $record->hasMethod('getEditableRowFields') ? $record->getEditableRowFields($grid) : $record->getCMSFields();

		throw new \Exception(sprintf('Please setFields on your %s component', __CLASS__));
	}

	protected function getValidatorForForm($grid = null) {
		if($this->validator) {
			if($this->validator instanceof \Validator)
				return $this->validator;
			elseif(is_callable($this->validator))
				return call_user_func_array($this->validator, [$this->getRecordFromGrid($grid), $grid, $this]);
			else
				return \Validator::create($this->validator);
		}

		return null;
	}

	protected function getRecordFromGrid($grid) {
		if($grid->getList()) {
			return \Object::create($grid->getModelClass());
		}

		return null;
	}

	public function handleForm($grid, $request) {
		$remaining = $request->remaining();
		$form = $this->getForm($grid);
		$class = str_replace('\\', '_', __CLASS__);

		if(preg_match(sprintf('/\/%s\[%s\]\[([0-9]+)\]/', preg_quote($grid->Name), $class), $remaining, $matches) && isset($matches[1])) {
			foreach($form->Fields()->dataFields() as $field) {
				$field->setName(sprintf(
					'%s[%s][%s][%s]', $grid->getName(), $class, $matches[1], $field->getName()
				));
			}
		}

		return $form;
	}

	public function Link() {
		return $this->workingGrid ? \Controller::join_links($this->workingGrid->Link($this->urlSegment)) : null;
	}
}