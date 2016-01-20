<?php namespace Milkyway\SS\GridFieldUtils;

/**
 * Milkyway Multimedia
 * TogglePublishedState.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\GridFieldUtils\Contracts\Action;
use GridField_FormAction;
use LogicException;
use ValidationException;

class ToggleAction extends Action
{
    public $field;

    public $titles = [
        'true'  => [
            'title' => 'Show',
            'icon'  => 'eye',
        ],
        'false' => [
            'title' => 'Hide',
            'icon'  => 'eye-slash',
        ],
    ];

    protected $id;

    public function __construct($field = null, $id = null)
    {
        $this->field = $field;
        $this->id = $id;
    }

    public function getActions($gridField)
    {
        $action = 'toggle_0';

        if ($this->id) {
            $action = 'action_' . $this->id;
        } else {
            $toggles = $gridField->Config->getComponentsByType(__CLASS__);
            foreach ($toggles as $index => $toggle) {
                if ($toggle === $this) {
                    $action = 'toggle_' . $index;
                    break;
                }
            }
        }

        return [$action];
    }


    public function getColumnContent($gridField, $record, $columnName)
    {
        $flag = $this->getFlag($record, $gridField);

        if (!$this->checkPermissions($flag, $record, $gridField)) {
            return '';
        }

        $titles = $this->titles;
        $title = '';
        $icon = '';

        if (is_array($this->field) && isset($this->field['title'])) {
            if (is_callable($this->field['title'])) {
                $titles = call_user_func_array($this->replaceStringWithRecordInCallable($this->field['title'], $record),
                    [$flag, $record, $gridField]);
            } else {
                if (is_array($this->field['title'])) {
                    $titles = $this->field['title'];
                } else {
                    $title = $this->field['title'];
                }
            }
        }

        if (!$title && count($titles)) {
            if ($flag) {
                $title = isset($titles['true']) && isset($titles['true']['title']) ? $titles['true']['title'] : '';
                $icon = isset($titles['true']) && isset($titles['true']['icon']) ? $titles['true']['icon'] : '';
            } else {
                $title = isset($titles['false']) && isset($titles['false']['title']) ? $titles['false']['title'] : '';
                $icon = isset($titles['false']) && isset($titles['false']['icon']) ? $titles['false']['icon'] : '';
            }
        }

        $action = $this->getActions($gridField);

        return GridField_FormAction::create($gridField, $action[0] . $record->ID, $title,
            $action[0], ['RecordID' => $record->ID])
            ->addExtraClass('gridfield-button-toggle-action')
            ->setAttribute('title', $title)
            ->setAttribute('data-icon', $icon)
            ->Field();
    }

    public function handleAction(\GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == $this->getActions($gridField)[0]) {
            $record = $gridField->getList()->byID($arguments['RecordID']);

            if (!$record) {
                return;
            }

            $flag = $this->getFlag($record, $gridField);

            if (!$this->checkPermissions($flag, $record, $gridField)) {
                throw new ValidationException(
                    _t('GridFieldAction_Delete.DeletePermissionsFailure',
                        'You do not have permission for this action'), 0);
            }

            if (!is_array($this->field)) {
                $record->{$this->field} = !($record->{$this->field});
            } else {
                if (isset($this->field['action'])) {
                    if (is_callable($this->field['action'])) {
                        $callable = $this->replaceStringWithRecordInCallable($this->field['action'], $record);

                        call_user_func_array($callable, [$flag, $record, $gridField]);
                    } else {
                        $record->{$this->field['action']} = !($record->{$this->field['action']});
                    }
                } else {
                    if (isset($this->field['field']) && !is_callable($this->field['field'])) {
                        $record->{$this->field['field']} = !($record->{$this->field['field']});
                    }
                }
            }
        }
    }

    protected function getFlag($record, $gridField)
    {
        $check = $this->field;
        $flag = null;

        if (is_array($this->field)) {
            if (isset($this->field['field'])) {
                if (is_callable($this->field['field'])) {
                    $callable = $this->replaceStringWithRecordInCallable($this->field['field'], $record);

                    $flag = call_user_func_array($callable, [$record, $gridField]);
                    $check = null;
                } else {
                    $check = $this->field['field'];
                }
            } else {
                throw new LogicException(__CLASS__ . ' requires a field to check, please make sure the key "field" is set');
            }
        }

        if ($check !== null) {
            $flag = $record->{$check};
        }

        return $flag;
    }

    protected function checkPermissions($flag, $record, $gridField)
    {
        if (is_array($this->field) && isset($this->field['permission'])) {
            if (is_callable($this->field['permission'])) {
                $callable = $this->replaceStringWithRecordInCallable($this->field['permission'], $record);

                if (!call_user_func_array($callable, [$flag, $record, $gridField])) {
                    return false;
                }
            } else {
                if (!$record->{$this->field['permission']}()) {
                    return false;
                }
            }
        } else {
            if (!$record->canEdit()) {
                return false;
            }
        }

        return true;
    }

    protected function replaceStringWithRecordInCallable($callable, $record)
    {
        if (!is_array($callable)) {
            return $callable;
        }

        if (is_array($callable) && isset($callable[0]) && $callable[0] == '$record') {
            $callable[0] = $record;
        }

        return $callable;
    }
}
