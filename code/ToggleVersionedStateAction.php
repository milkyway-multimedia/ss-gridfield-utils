<?php
/**
 * Milkyway Multimedia
 * TogglePublishedState.php
 *
 * @package rugwash.com.au
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

namespace Milkyway\SS\GridFieldUtils;

use DB;
use ValidationException;

class ToggleVersionedStateAction extends ToggleAction {
	public function __construct($field = null, $id = null) {
		if(!$field) {
			$field = [
				'field' => function($record, $gridField) {
					if(!$record->hasExtension('Versioned'))
						return false;

					return
						$record->hasExtension('Versioned') &&
						$record->ID &&
						DB::query(
						'SELECT "ID" FROM "' . $record->baseTable() . '_' . $record->get_live_stage() .
						'" WHERE "ID" = ' . $record->ID
					)->value();
				},
				'action' => function($flag, $record, $gridField) {
					if(!$record->hasExtension('Versioned'))
						throw new ValidationException('The record is not a versioned record');

					if($flag) {
						$record->deleteFromStage('Live');
					}
					else {
						$record->writeToStage('Stage');
						$record->publish('Stage', 'Live');
					}
				},
				'permission' => function($flag, $record, $gridField) {
					if(!$record->hasExtension('Versioned'))
						return false;

					if($flag && $record->hasMethod('canDeleteFromLive'))
						return $record->canDeleteFromLive();
					else if(!$flag && $record->hasMethod('canPublish'))
						return $record->canPublish();
					else
						return $record->canEdit();
				},
				'title' => [
					'true' => [
						'title' => 'Publish',
						'icon' => 'eye',
					],
					'false' => [
						'title' => 'Unpublish',
						'icon' => 'eye-slash',
					]
				],
			];
		}

		parent::__construct($field, $id);
	}
}