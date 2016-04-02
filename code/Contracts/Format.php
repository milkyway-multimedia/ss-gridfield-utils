<?php namespace Milkyway\SS\GridFieldUtils\Contracts;

/**
 * Milkyway Multimedia
 * Format.php
 *
 * @package milkyway-multimedia/milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use GridField;

interface Format
{
    /**
     * Get the title of the format for a button
     * @return string
     */
    public function getFormatTitle();

    /**
     * Get unique format state (ie. slug)
     * @return string
     */
    public function getFormatState();

    /**
     * Format the @GridField
     * @param GridField $gridField
     */
    public function format(GridField $gridField);

    /**
     * Unformat the @GridField
     * @param GridField $gridField
     */
    public function unformat(GridField $gridField);
}
