<?php
/**
 * Created by IntelliJ IDEA.
 * User: mwm-15R
 * Date: 27/11/2015
 * Time: 1:59 PM
 */

namespace Milkyway\SS\GridFieldUtils;


use Milkyway\SS\GridFieldUtils\Contracts\HasModal;
use ArrayData;
use Controller;

class EditInModal extends \GridFieldEditButton
{
    use HasModal;

    /**
     * @param \GridField $gridField
     * @param \DataObject $record
     * @param string $columnName
     *
     * @return string - the HTML for the column
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        $this->htmlFragments($gridField);
        return ArrayData::create([
            'Link' => Controller::join_links($this->Link($gridField), $record->ID, 'edit'),
        ])->renderWith(['GridField_EditInModal', 'GridFieldEditButton',]);
    }
}