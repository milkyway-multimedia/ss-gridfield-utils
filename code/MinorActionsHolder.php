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

class MinorActionsHolder implements GridField_HTMLProvider
{
	protected $targetFragment;
	protected $title;

	public function __construct($targetFragment = 'before', $title = '')
	{
		$this->targetFragment = $targetFragment;
		$this->title = $title ? $title : _t('GridField.OTHER_ACTIONS', 'Other Actions');
	}

	public function getHTMLFragments($gridField)
	{
		return [
			$this->targetFragment => ArrayData::create([
				'TargetFragmentName' => $this->targetFragment,
				'Actions' => "\$DefineFragment(actions-{$this->targetFragment})",
			])->renderWith('GridField_MinorActionsHolder')
		];
	}
} 