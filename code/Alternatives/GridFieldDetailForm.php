<?php namespace Milkyway\SS\GridFieldUtils;

/**
 * Milkyway Multimedia
 * GridFieldDetailForm.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use GridFieldDetailForm as Original;

class GridFieldDetailForm extends Original
{
    public $uriSegment = 'item';

    public function __construct($name = 'DetailForm', $uriSegment = 'item')
    {
        $this->uriSegment = $uriSegment;
        parent::__construct($name);
    }

    public function getURLHandlers($gridField)
    {
        return [
            $this->uriSegment . '/$ID' => 'handleItem',
            'autocomplete'             => 'handleAutocomplete',
        ];
    }

    public function setUriSegment($urlSegment)
    {
        $this->uriSegment = $urlSegment;
        return $this;
    }

    public function getUriSegment()
    {
        return $this->uriSegment;
    }
}
