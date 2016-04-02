<?php namespace Milkyway\SS\GridFieldUtils\Common;

use GridField;
use Milkyway\SS\GridFieldUtils\FormatSwitcher;

trait FormatsGridField
{
    protected function formatIfNoSwitcherAvailable(GridField $gridField) {
        $hasSwitcher = false;

        foreach($gridField->Config->getComponents() as $component) {
            if($component instanceof FormatSwitcher) {
                $hasSwitcher = true;
                break;
            }
        }

        if(!$hasSwitcher) {
            $this->format($gridField);
        }
    }
}
