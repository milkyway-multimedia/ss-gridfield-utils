<?php
/**
 * Milkyway Multimedia
 * utilities.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\GridFieldUtils;


class Utilities {
    public static function include_requirements() {
        \Requirements::css(SS_GRIDFIELD_UTILITIES . '/css/mwm.gridfield.utils.css');
        \Requirements::javascript(SS_GRIDFIELD_UTILITIES . '/js/mwm.gridfield.utils.js');
    }
} 