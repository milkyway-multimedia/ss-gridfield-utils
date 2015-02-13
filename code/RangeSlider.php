<?php namespace Milkyway\SS\GridFieldUtils;
/**
 * Milkyway Multimedia
 * RangeSlider.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

class RangeSlider implements \GridField_ActionProvider, \GridField_DataManipulator, \GridField_HTMLProvider {
	public $filterField;

	public $fragment;

	public $template = 'GridField_RangeSlider_Row';

	public $sliderSettings = [];

	public $hideNavigation = false;

	public $label;

	public $comparisonFilter = 'LessThan';

	private $working;

	public function __construct($filterField = 'Created', $fragment = 'header', $label = '') {
		if(!\ClassInfo::exists('RangeSliderField')) {
			throw new \LogicException('Please install the milkyway-multimedia/ss-mwm-formfields module to use this feature');
		}

		$this->filterField = $filterField;
		$this->fragment = $fragment;
		$this->label = $label ?: _t('GridFieldUtils.SHOW', 'Show');
	}

	public function getActions($gridField) {
		return [
			'filterByRange',
		];
	}

	public function setSliderSetting($setting, $value = null)
	{
		if (isset($this->sliderSettings[$setting])) {
			$this->sliderSettings[$setting] = $value;
			return $this;
		}

		array_set($this->sliderSettings, $setting, $value);
		return $this;
	}

	public function getSliderSetting($setting)
	{
		return array_get($this->sliderSettings, $setting);
	}

	public function handleAction(\GridField $gridField, $actionName, $arguments, $data) {
		$state = $gridField->State->Milkyway_SS_GridFieldUtils_RangeSlider;

		if($actionName === 'filterbyrange') {
			if(isset($data['filterByRange'][$gridField->getName()][$this->filterField]['slider'])){
				foreach($data['filterByRange'][$gridField->getName()][$this->filterField]['slider'] as $key => $value){
					$state->$key = $value;
				}
			}
		}
	}

	/**
	 *
	 * @param \GridField $gridField
	 * @param \SS_List $dataList
	 * @return \SS_List
	 */
	public function getManipulatedData(\GridField $gridField, \SS_List $dataList) {
		$state = $gridField->State->Milkyway_SS_GridFieldUtils_RangeSlider;

		if(!isset($state->min) && !isset($state->value)) {
			$settings = $this->scaffoldSliderSettingsForField(singleton($gridField->getModelClass())->obj($this->filterField), $dataList);
			$this->working = [$gridField, $dataList];

			if(isset($settings['start'])) {
				if(is_array($settings['start'])) {
					$state->min = $settings['start'][0];
					$state->max = $settings['start'][1];
				}
				else
					$state->value = $settings['start'];
			}
			else
				return $dataList;
		}

		$dataListClone = clone($dataList);

		$dbField = singleton($gridField->getModelClass())->obj($this->filterField);

		if(isset($state->value)) {
			$comparisonFilter = $this->comparisonFilter ? '' : ':' . $this->comparisonFilter;
			$dataListClone = $dataListClone->filter($this->filterField.$comparisonFilter, $this->getValueForDB($dbField, $state->value));
		}
		elseif(isset($state->min) && isset($state->max)) {
			$dataListClone = $dataListClone->filter([$this->filterField.':GreaterThan' => $this->getValueForDB($dbField, $state->min), $this->filterField.':LessThan' => $this->getValueForDB($dbField, $state->max)]);
		}

		return $dataListClone;
	}

	public function getHTMLFragments($gridField) {
		Utilities::include_requirements();
		$state = $gridField->State->Milkyway_SS_GridFieldUtils_RangeSlider;

		$data = [
			'ColumnCount' =>$gridField->getColumnCount(),
		];

		$fieldName = 'filterByRange[' . $gridField->getName() . '][' . $this->filterField . '][slider]';

		$dbField = singleton($gridField->getModelClass())->obj($this->filterField);
		$settings = array_merge(
			[
				'behaviour' => 'drag-tap',
			],
			$this->scaffoldSliderSettingsForField($dbField, $gridField->getList()),
			$this->sliderSettings
		);

		if(isset($state->min) && isset($state->max))
			$settings['start'] = [$state->min, $state->max];
		elseif(isset($state->value))
			$settings['value'] = [$state->min, $state->max];

		$data['Slider'] = \RangeSliderField::create($fieldName, null, null, $settings)->addExtraClass('ss-gridfield-range-slider--field');

		$data['Button'] = \GridField_FormAction::create($gridField, 'filter', 'Filter', 'filterByRange', null)
			->addExtraClass('ss-gridfield-range-slider--button')
			->setAttribute('title', _t('GridField.Filter', 'Filter'))
			->setAttribute('id', 'action_filter_' . $gridField->getModelClass() . '_' . $this->filterField);

		$data['Slider']->inputCallback = function($fields) use($fieldName, $dbField, $settings, &$data) {
			foreach($fields as $field) {
				$this->modifyFormFieldForDBField($dbField, $field->Render, $settings);

				if($field->Render->hasClass('date'))
					$data['HasDates'] = true;
			}

			if($this->label) {
				$fields->unshift(\ArrayData::create([
					'Render' => \LabelField::create($fieldName . '[label]', $this->label)
				]));
			}
		};

		$data['Slider']->Field();

		$data['HideNavigation'] = $this->hideNavigation;

		return [
			$this->fragment => \ArrayData::create($data)->renderWith(array_merge((array)$this->template, ['GridField_RangeSlider_Row'])),
		];
	}

	protected function scaffoldSliderSettingsForField(\DBField $dbField, $dataList = null) {
		if($dbField instanceof \Boolean) {
			$this->comparisonFilter = null;

			return [
				'start' => 0,
				'step'  => 1,
				'range' => [
					'min' => 0,
					'max' => 2,
				],
			];
		}
		else if($dbField instanceof \Date) {
			$format = ($dbField instanceof \SS_Datetime) ? 'd/m/Y h:i' : 'd/m/Y';

			$response = [
				'start' => [date($format, strtotime('-1 year')), date($format)],
				'range' => [
					'min' => strtotime('-10 years'),
					'max' => time(),
				],
			    'format' => ($dbField instanceof \SS_Datetime) ? 'date::DD/MM/YYYY hh:mm' : 'date::DD/MM/YYYY',
			    'connect' => true,
			];

			if($dataList && $first = $dataList->sort($this->filterField, 'ASC')->first()) {
				$response['range']['min'] = strtotime($first->{$this->filterField});
				$response['start'][0] = date($format, $response['range']['min']);
			}

			if($this->getSliderSetting('pips')) {
				$this->setSliderSetting('pips.format', ($dbField instanceof \SS_Datetime) ? 'date::DD/MM/YYYY' : 'date::DD/MM/YYYY');
			}

			return $response;
		}
		else if(
			($dbField instanceof \Currency)
			|| ($dbField instanceof \Decimal)
			|| ($dbField instanceof \Double)
			|| ($dbField instanceof \Float)
			|| ($dbField instanceof \Percentage)
			|| ($dbField instanceof \Money)
		) {
			return [
				'start' => 50.00,
				'range' => [
					'min' => 0.00,
				    'max' => 100.00,
				],
			];
		}
		else if($dbField instanceof \Int) {
			return [
				'start' => 5,
				'range' => [
					'min' => 0,
					'max' => 10,
				],
			];
		}
		else if($dbField instanceof \Time) {
			return [
				'start' => strtotime(date('d/m/Y') . ' 12:00'),
				'range' => [
					'min' => strtotime(date('d/m/Y') . ' 00:00'),
					'max' => strtotime(date('d/m/Y') . ' 23:59'),
				],
			];
		}
		else if($dbField instanceof \Year) {
			return [
				'start' => date(strtotime('-10 years'), 'Y'),
				'range' => [
					'min' => date('Y', strtotime('-80 years')),
					'max' => date('Y'),
				],
			];
		}

		throw new \LogicException('Invalid field type ' . get_class($dbField) . ' for ' . __CLASS__);
	}

	protected function getValueForDB($dbField, $value) {
		if(($dbField instanceof \Date) || ($dbField instanceof \Time))
			$value = strtotime(str_replace('/', '-', $value));

		$dbField->setValue($value);
		$value = $dbField->RAW();
		return $value;
	}

	protected function modifyFormFieldForDBField(\DBField $dbField, $field, $settings = []) {
		$field->setAttribute('readonly', 'readonly');
	}
} 