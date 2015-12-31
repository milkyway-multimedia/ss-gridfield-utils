<?php namespace Milkyway\SS\GridFieldUtils;

/**
 * Milkyway Multimedia
 * MinorActionsHolder.php
 *
 * @package milkyway-multimedia/ss-gridfield-utils
 * @author  Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use GridField_HTMLProvider;
use ArrayData;
use ArrayList;
use ViewableData;

class MinorActionsHolder implements GridField_HTMLProvider
{
    protected $targetFragment;
    protected $title;
    protected $id;
    protected $showEmptyString;

    public function __construct($targetFragment = 'buttons-before-left', $title = '', $id = '')
    {
        $this->targetFragment = $targetFragment;
        $this->id = $id;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getShowEmptyString()
    {
        return $this->showEmptyString;
    }

    /**
     * @param string $showEmptyString
     */
    public function setShowEmptyString($showEmptyString = '')
    {
        $this->showEmptyString = $showEmptyString;
        return $this;
    }

    public function getHTMLFragments($gridField)
    {
        $target = $this->id ? $this->targetFragment . '-' . $this->id : $this->targetFragment;
        return [
            $this->targetFragment => ArrayData::create([
                'Title' => $this->title,
                'ShowEmptyString' => $this->showEmptyString,
                'TargetFragmentName' => $this->targetFragment,
                'TargetFragmentID' => $target,
                'Actions'            => "\$DefineFragment(actions-{$target})",
            ])->renderWith('GridField_MinorActionsHolder'),
        ];
    }
}
