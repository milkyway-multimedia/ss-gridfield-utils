<?php
/**
 * Milkyway Multimedia
 * GridFieldAddNewButton.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\GridFieldUtils;

use ArrayData;
use Controller;

class GridFieldAddNewButton extends \GridFieldAddNewButton
{
    public $gridFieldDetailForm;

    protected $template;
    protected $type;

    public function __construct($targetFragment = 'before', $buttonName = '', $gridFieldDetailForm = null)
    {
        $this->targetFragment = $targetFragment;
        $this->buttonName = $buttonName;
        $this->gridFieldDetailForm = $gridFieldDetailForm;
    }

    public function getHTMLFragments($gridField)
    {
        $singleton = singleton($gridField->getModelClass());

        if (!$singleton->canCreate()) {
            return [];
        }

        if (!$this->buttonName) {
            // provide a default button name, can be changed by calling {@link setButtonName()} on this component
            $objectName = $singleton->i18n_singular_name();
            $this->buttonName = _t('GridField.Add', 'Add {name}', ['name' => $objectName]);
        }

        $class = explode('\\', get_called_class());
        $class = array_pop($class);

        return [
            $this->targetFragment => ArrayData::create([
                'NewLink'    => Controller::join_links($this->getNewLink($gridField), 'new'),
                'ButtonName' => $this->buttonName,
                'Type'       => $this->type ?: $class,
            ])->renderWith(array_filter([
                $this->template,
                'GridField_' . $class,
                'GridFieldAddNewButton',
            ])),
        ];
    }

    protected function getNewLink($gridField)
    {
        if ($this->gridFieldDetailForm instanceof GridFieldDetailForm) {
            return $gridField->Link($this->gridFieldDetailForm->getUriSegment());
        } elseif ($this->gridFieldDetailForm) {
            return $gridField->Link($this->gridFieldDetailForm);
        } else {
            return ($df = $gridField->Config->getComponentByType('Milkyway\SS\GridFieldUtils\GridFieldDetailForm')) && isset($df->uriSegment) ? $gridField->Link($df->getUriSegment()) : $gridField->Link('item');
        }
    }
}
