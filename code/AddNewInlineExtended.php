<?php
/**
 * Milkyway Multimedia
 * AddNewInlineExtended.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\GridFieldUtils;

class AddNewInlineExtended extends \RequestHandler implements \GridField_HTMLProvider, \GridField_SaveHandler, \GridField_URLHandler, \Flushable
{
	public $urlSegment = 'extendedInline';

	public $loadViaAjax = true;

	public $cacheAjaxLoading = true;

	public $hideUnlessOpenedWithEditableColumns = true;

	public $openToggleByDefault = false;

	public $rowTemplate;

	protected $fragment;

	protected $title;

	protected $fields;

	protected $template;

	protected $validator;

	private $workingGrid;

	private $cache;

	private static $allowed_actions = [
		'loadItem',
		'handleForm',
	];

	public static function flush() {
		singleton(__CLASS__)->cleanCache();
	}

	public function cleanCache() {
		$this->cache->clean();
	}

	/**
	 * @param string $fragment the fragment to render the button in
	 * @param string $title the text to display on the button
	 * @param \FieldList|Callable|array $fields the fields to display in inline form
	 */
	public function __construct($fragment = 'buttons-before-left', $title = '', $fields = null)
	{
		parent::__construct();
		$this->fragment = $fragment;
		$this->title = $title ? : _t('GridFieldExtensions.ADD', 'Add');
		$this->fields = $fields;

		$this->cache = \SS_Cache::factory($this->getCacheKey(['holder' => __CLASS__]), 'Output', ['lifetime' => 6 * 60 * 60]);
	}

	public function getURLHandlers($gridField)
	{
		return [
			$this->urlSegment .'/load' => 'loadItem',
			$this->urlSegment . '/$Form!' => 'handleForm',
		];
	}

	/**
	 * Gets the fragment name this button is rendered into.
	 *
	 * @return string
	 */
	public function getFragment()
	{
		return $this->fragment;
	}

	/**
	 * Sets the fragment name this button is rendered into.
	 *
	 * @param string $fragment
	 * @return static $this
	 */
	public function setFragment($fragment)
	{
		$this->fragment = $fragment;
		return $this;
	}

	/**
	 * Gets the button title text.
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Sets the button title text.
	 *
	 * @param string $title
	 * @return static $this
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Gets the fields for this class
	 *
	 * @return \FieldList|Callable|array
	 */
	public function getFields()
	{
		return $this->fields;
	}

	/**
	 * Sets the fields that will be displayed in this component
	 *
	 * @param \FieldList|Callable|array $fields
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
	 * @return \Validator|Callable|array
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

	public function getHTMLFragments($grid)
	{
		if ($grid->getList() && !singleton($grid->getModelClass())->canCreate()) {
			return [];
		}

		$this->workingGrid = $grid;

		$grid->addExtraClass('ss-gridfield-add-inline-extended--table');

		$fragments = [
			$this->getFragment() => \ArrayData::create([
				'Title' => $this->getTitle(),
				'Ajax' => $this->loadViaAjax,
			    'Link' => $this->loadViaAjax ? $this->Link('load') : '',
			])->renderWith('GridField_AddNewInlineExtended_Button'),
		];

		if(!$this->loadViaAjax) {
			\Requirements::javascript(THIRDPARTY_DIR . '/javascript-templates/tmpl.js');
			$fragments['after'] = $this->getRowTemplate($grid);
		}

		Utilities::include_requirements();

		return $fragments;
	}

	protected function getRowTemplate($grid)
	{
		return \ArrayData::create($this->getRowTemplateVariables($grid))
			->renderWith(array_merge((array)$this->template, ['GridField_AddNewInlineExtended']));
	}

	protected function getRowTemplateVariables($grid, $placeholder = '{%=o.num%}')
	{
		$class = str_replace('\\', '_', __CLASS__);
		$form = $this->getForm($grid, '-'.$placeholder)->setHTMLID('Form-' . $class . '-'.$placeholder);
		$fields = $form->Fields()->dataFields();
		$editableColumnsTemplate = null;
		$countUntilThisColumn = 0;

		foreach ($fields as $field) {
			$field->setName(str_replace(
				['$gridfield', '$class', '$placeholder', '$field'],
				[$grid->getName(), $class, $placeholder, $field->getName()],
				'$gridfield[$class][$placeholder][$field]'
			));
		}

		if ($this->canEditWithEditableColumns($grid) && ($editableColumns = $grid->Config->getComponentByType('GridFieldEditableColumns')) && ($editableRow = $grid->Config->getComponentByType('Milkyway\SS\GridFieldUtils\EditableRow'))) {
			$ecFragments = (new \GridFieldAddNewInlineButton())->getHTMLFragments($grid);
			$toggleClasses = $this->openToggleByDefault ? ' ss-gridfield-add-inline-extended--toggle_open' : '';

			$editableColumnsTemplate = str_replace([
				'GridFieldAddNewInlineButton',
				'GridFieldEditableColumns',
				'{%=o.num%}',
				'ss-gridfield-editable-row--icon-holder">',
				'ss-gridfield-inline-new"',
				'ss-gridfield-delete-inline'
			],
				[
					str_replace('\\', '_', __CLASS__),
					str_replace('\\', '_', __CLASS__),
					$placeholder,
					sprintf('ss-gridfield-editable-row--icon-holder"><i class="ss-gridfield-add-inline-extended--toggle%s"></i>', $toggleClasses),
					'ss-gridfield-inline-new-extended" data-inline-new-extended-row="' . $placeholder . '"',
					'ss-gridfield-inline-new-extended--row-delete'
				], str_replace([
					'<script type="text/x-tmpl" class="ss-gridfield-add-inline-template">', '</script>'
				], '', $ecFragments['after']));



			foreach ($grid->getColumns() as $column) {
				$countUntilThisColumn++;

				if (in_array($column, $editableRow->getColumnsHandled($grid)))
					break;
			}

			if ($countUntilThisColumn == count($grid->getColumns()))
				$countUntilThisColumn = 0;
		}

		return [
				'EditableColumns' => $editableColumnsTemplate,
				'OpenByDefault' => $this->openToggleByDefault,
				'Form' => $form,
				'AllColumnsCount' => count($grid->getColumns()),
				'ColumnCount' => count($grid->getColumns()) - $countUntilThisColumn,
				'ColumnCountWithoutActions' => count($grid->getColumns()) - $countUntilThisColumn - 1,
				'PrevColumnsCount' => $countUntilThisColumn,
				'Model' => (($record = $this->getRecordFromGrid($grid)) && $record->hasMethod('i18n_singular_name')) ? $record->i18n_singular_name() : _t('GridFieldUtils.ITEM', 'Item'),
			];
	}

	public function handleSave(\GridField $grid, \DataObjectInterface $record)
	{
		$list = $grid->getList();
		$value = $grid->Value();
		$className = str_replace('\\', '_', __CLASS__);

		if (!isset($value[$className]) || !is_array($value[$className])) {
			return;
		}

		$class = $grid->getModelClass();

		if (!singleton($class)->canCreate()) {
			return;
		}

		$form = $this->getForm($grid, '', false);

		foreach ($value[$className] as $fields) {
			$item = \Object::create($class);

			$form->loadDataFrom($fields);
			$form->saveInto($item);
			$extra = method_exists($list, 'getExtraFields') ? array_intersect_key($form->Data, (array)$list->getExtraFields()) : [];

			$item->write();
			$list->add($item, $extra);
		}
	}

	protected function getForm($grid, $append = '', $removeEditableColumnFields = true)
	{
		$this->workingGrid = $grid;
		return \Form::create($this, 'Form-' . $grid->getModelClass() . $append, $this->getFieldList($grid, $removeEditableColumnFields), \FieldList::create(), $this->getValidatorForForm($grid))->loadDataFrom($this->getRecordFromGrid($grid));
	}

	protected function getFieldList($grid = null, $removeEditableColumnFields = true)
	{
		$fields = null;

		if ($this->fields) {
			if ($this->fields instanceof \FieldList)
				$fields = $this->fields;
			elseif (is_callable($this->fields))
				$fields = call_user_func_array($this->fields, [$this->getRecordFromGrid($grid), $grid, $this]);
			else
				$fields = \FieldList::create($this->fields);
		}

		if (!$fields && $grid) {
			if ($editable = $grid->getConfig()->getComponentByType('Milkyway\SS\GridFieldUtils\EditableRow'))
				$fields = $editable->getForm($grid, $this->getRecordFromGrid($grid), $removeEditableColumnFields)->Fields();
			elseif ($editable = $grid->getConfig()->getComponentByType('GridFieldEditableColumns'))
				$fields = $editable->getFields($grid, $this->getRecordFromGrid($grid));
		}

		if (!$fields && $record = $this->getRecordFromGrid($grid))
			$fields = $record->hasMethod('getEditableRowFields') ? $record->getEditableRowFields($grid) : $record->getCMSFields();

		if(!$fields)
			throw new \Exception(sprintf('Please setFields on your %s component', __CLASS__));

		if($removeEditableColumnFields && $grid && $this->canEditWithEditableColumns($grid) && $editable = $grid->getConfig()->getComponentByType('GridFieldEditableColumns')) {
			if(!isset($record))
				$record = $this->getRecordFromGrid($grid);

			$editableColumns = $editable->getFields($grid, $record);

			foreach($editableColumns as $column)
				$fields->removeByName($column->Name);
		}

		return $fields;
	}

	protected function getValidatorForForm($grid = null)
	{
		if ($this->validator) {
			if ($this->validator instanceof \Validator)
				return $this->validator;
			elseif (is_callable($this->validator))
				return call_user_func_array($this->validator, [$this->getRecordFromGrid($grid), $grid, $this]);
			else
				return \Validator::create($this->validator);
		}

		return null;
	}

	protected function getRecordFromGrid($grid)
	{
		if ($grid->getList()) {
			$record = \Object::create($grid->getModelClass());

			if($grid->List && ($grid->List instanceof \HasManyList) && $grid->Form && $grid->Form->Record) {
				$record->{$grid->Name} = $grid->Form->Record;
				$record->{$grid->Name.'ID'} = $grid->Form->Record->ID;
			}
			else {
				$workingParent = $this->setWorkingParentOnRecordTo;
				if (!$workingParent && $grid->Config && $editableRow = $grid->Config->getComponentByType('Milkyway\SS\GridFieldUtils\EditableRow')) {
					$workingParent = $editableRow->setWorkingParentOnRecordTo;
				}

				if ($workingParent && $grid->List && $grid->Form && $grid->Form->Record) {
					$record->{$workingParent} = $grid->Form->Record;
				}
			}

			return $record;
		}

		return null;
	}

	public function handleForm($grid, $request)
	{
		$remaining = $request->remaining();
		$form = $this->getForm($grid);
		$class = str_replace('\\', '_', __CLASS__);

		if (preg_match(sprintf('/\/%s\[%s\]\[([0-9]+)\]/', preg_quote($grid->Name), $class), $remaining, $matches) && isset($matches[1])) {
			foreach ($form->Fields()->dataFields() as $field) {
				$field->setName(sprintf(
					'%s[%s][%s][%s]', $grid->getName(), $class, $matches[1], $field->getName()
				));
			}
		}

		return $form;
	}

	public function loadItem($grid, $request) {
		$cacheKey = $this->getCacheKey([
			'class' => get_class($this->getRecordFromGrid($grid)),
			'id' => spl_object_hash($this),
		    'open' => $this->openToggleByDefault,
		]);

		if(!$this->cacheAjaxLoading || !($template = unserialize($this->cache->load($cacheKey)))) {
			$template = \ArrayData::create(array_merge(
				$this->getRowTemplateVariables($grid, '{{ placeholder }}'), [
					'Ajax' => true,
					'placeholder' => '{{ placeholder }}',
				]
			))->renderWith(array_merge((array)$this->template, ['GridField_AddNewInlineExtended_Row']));

			$this->cache->save(serialize($template), $cacheKey);
		}

		$template = str_replace('{{ placeholder }}', $request->getVar('_datanum'), $template);

		return $template;
	}

	public function Link($action = '')
	{
		return $this->workingGrid ? \Controller::join_links($this->workingGrid->Link($this->urlSegment), $action) : null;
	}

	public function canEditWithEditableColumns($gridField)
	{
		return $this->hideUnlessOpenedWithEditableColumns && $gridField->Config->getComponentByType('GridFieldEditableColumns');
	}

	protected function getCacheKey(array $vars = []) {
		return preg_replace('/[^a-zA-Z0-9_]/', '', __CLASS__ . '_' . urldecode(http_build_query($vars, '', '_')));
	}
}