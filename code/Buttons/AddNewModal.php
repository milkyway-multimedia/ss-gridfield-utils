<?php namespace Milkyway\SS\GridFieldUtils;

use Milkyway\SS\GridFieldUtils\Contracts\HasModal;

/**
 * Milkyway Multimedia
 * AddNewModal.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

class AddNewModal extends GridFieldAddNewButton implements \GridField_URLHandler
{
    use HasModal;

    public function getHTMLFragments($gridField)
    {
        $this->htmlFragments($gridField);
        return parent::getHTMLFragments($gridField);
    }

    protected function getNewLink($gridField)
    {
        return $this->Link($gridField);
    }
}
