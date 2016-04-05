<?php namespace Milkyway\SS\GridFieldUtils;

/**
 * Milkyway Multimedia
 * FormatSwitcher.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use GridField_HTMLProvider;
use GridField_DataManipulator;
use GridField_ActionProvider;
use GridField;
use GridField_FormAction as Button;
use Milkyway\SS\GridFieldUtils\Contracts\Format;
use SS_List;
use ArrayList;
use ArrayData;
use FormField;

class FormatSwitcher implements GridField_HTMLProvider, GridField_DataManipulator, GridField_ActionProvider
{
    protected $default = 'unformatted';

    protected $formats = [];

    protected $targetFragment;

    protected $formatCallback;

    public $urlSegment = 'format';

    public $unformatted = [
        'title' => 'List',
        'state' => 'unformatted',
    ];

    public function __construct($targetFragment = 'before', $default = '') {
        $this->targetFragment = $targetFragment;
        $this->default = $default;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param string $default
     * @return static
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }

    /**
     * @param array $formats
     * @return static
     */
    public function setFormats($formats)
    {
        $this->formats = $formats;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getFormatCallback()
    {
        return $this->formatCallback;
    }

    /**
     * Sets a custom handler for when a format action is performed
     *
     * @param callable $callback
     * @return self $this
     */
    public function setFormatCallback($callback)
    {
        $this->formatCallback = $callback;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHTMLFragments($gridField)
    {
        $this->stockFormats($gridField);

        if(empty($this->formats)) {
            return [];
        }

        Utilities::include_requirements();

        $currentFormat = $this->getFormatState($gridField)->current;
        $formats = ArrayList::create();
        $icon = '<i class="ss-gridfield--format-switcher--button-icon"></i><i class="ss-gridfield--format-switcher--button-icon"></i><i class="ss-gridfield--format-switcher--button-icon"></i>';

        $action = Button::create($gridField, 'unformatted', _t('GridField_FormatSwitcher.' . $this->unformatted['title'], $this->unformatted['title']), $this->urlSegment, $this->unformatted['state']);
        $action->ButtonContent = $icon . $action->Title();
        $action->addExtraClass('ss-gridfield--format-switcher--button ss-gridfield--format-switcher--button_' . $this->unformatted['state']);

        if($currentFormat == $this->unformatted['state']) {
            $action = $action
                ->addExtraClass('ss-gridfield--format-switcher--button_active active')
                ->performDisabledTransformation();
        }

        $formats->push(ArrayData::create([
            'Button' => $action,
        ]));

        foreach($this->formats as $formatState => $format) {
            if(is_array($format)) {
                $title = empty($format['title']) ? FormField::name_to_label($formatState) : $format['title'];
            }
            else if(is_callable($format)) {
                $title =  FormField::name_to_label($formatState);
            }
            else {
                $title = $format->getFormatTitle();
            }

            $action = Button::create($gridField, $formatState, $title, $this->urlSegment, $formatState);
            $action->ButtonContent = $icon . $action->Title();
            $action->addExtraClass('ss-gridfield--format-switcher--button ss-gridfield--format-switcher--button_' . str_replace(' ', '-', $formatState));

            if($formatState == $currentFormat) {
                $action = $action
                    ->addExtraClass('ss-gridfield--format-switcher--button_active active')
                    ->performDisabledTransformation();
            }

            $formats->push(ArrayData::create([
                'Button' => $action,
            ]));
        }

        return [
            $this->targetFragment => ArrayData::create([
                'Formats' => $formats,
            ])->renderWith('GridField_FormatSwitcher'),
        ];
    }

    /**
     * Retrieves/Sets up the state object used to store and retrieve information
     * about the current paging details of this GridField
     * @param GridField $gridField
     * @return \GridState_Data
     */
    protected function getFormatState(GridField $gridField) {
        $state = $gridField->State->Formatter;

        if(!(string)$state->current && !(string)$state->hasloaded) {
            $state->current = $this->getDefault();
            $state->hasloaded = 1;
        }

        return $state;
    }

    /**
     * Manipulate the list according to chosen format
     * @param GridField $gridField
     * @param SS_List $dataList
     * @return SS_List
     */
    public function getManipulatedData(GridField $gridField, SS_List $dataList)
    {
        $this->stockFormats($gridField);
        $state = $this->getFormatState($gridField);
        $lastState = (string)$state->last;
        $currentState = (string)$state->current;

        if(!$currentState && $lastState) {
            $currentState = $lastState;
        }

        foreach($gridField->Config->getComponents() as $component) {
            if($component instanceof Format) {
                $this->formats[$component->getFormatState()]->unformat($gridField);
            }
        }

        foreach($this->formats as $format) {
            if(is_array($format) && isset($format['unformat'])) {
                call_user_func($format['unformat'], $gridField);
            }
        }

        if($currentState && isset($this->formats[$currentState])) {
            $gridField->removeExtraClass('ss-gridfield_' . $this->unformatted['state']);

            if(is_array($this->formats[$currentState]) && isset($this->formats[$currentState]['format'])) {
                call_user_func($this->formats[$currentState]['format'], $gridField, ($currentState ?: $this->unformatted['state']));
            }
            else if(is_callable($this->formats[$currentState])) {
                call_user_func($this->formats[$currentState], $gridField, ($currentState ?: $this->unformatted['state']));
            }
            else {
                $this->formats[$currentState]->format($gridField);
            }
        }
        else {
            $gridField->addExtraClass('ss-gridfield_' . $this->unformatted['state']);
        }

        if($this->formatCallback) {
            call_user_func($this->formatCallback, $gridField, ($currentState ?: $this->unformatted['state']));
        }

        $state->last = $currentState;

        return $dataList;
    }

    public function getActions($gridField)
    {
        return [$this->urlSegment];
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if($actionName !== $this->urlSegment) {
            return;
        }

        $state = $this->getFormatState($gridField);
        $state->current = (string)$arguments;
    }

    protected function stockFormats($gridField) {
        if(empty($this->formats) && $gridField) {
            foreach($gridField->Config->getComponents() as $component) {
                if($component instanceof Format) {
                    $this->formats[$component->getFormatState()] = $component;
                }
            }
        }
    }
}
