EditableRow
===================

The `Milkyway\SS\GridFieldUtils\EditableRow` component adds an expandable form to each row in the GridField, allowing you to edit records directly from the GridField. This makes the GridField act like a Tree, with nested GridFields working as expected.

On page/dataobject save and GridField reload, the rows should stay open, and tab states will be kept.

If no fields are set, it will pull fields from either the GridFieldDetailForm component on the current grid, or default to the getEditableRowFields($record) or getCMSFields methods on the record respectively.

```php
    $grid->getConfig()->addComponent($component = new Milkyway\SS\GridFieldUtils\EditableRow($fields = null));
```

## Public properties
* **$component->setWorkingParentOnRecordTo** Set the working parent on the currently edited record (defaults to Parent). This is usually useful for ManyManyLists, so you can access the parent record in the getCMSFields() method by using $record->Parent. If you already have a Parent field in your record, you should change this value.
* **$component->disableToggleStateSave** If set to true, will disable toggle state saving (by default, toggle states will be reloaded when a page/dataobject is saved, or the gridfield is reloaded.
* **$component->cacheToggleStateSave** If set to true, toggle states will not be loaded in via AJAX (makes GridFields respond faster since there is less requests to make). If your records are not changing much during save (no additional processing done in background), I recommend setting this to true.
* **$component->column** Column Name (defaults to _OpenRowForEditing). If you need more than one EditableRow component, you should to change this value.
* **$component->urlSegment** URL of component (defaults to editableRow). If you need more than one EditableRow component, you should change this value.

## Public methods
* **$component->setFields($fields = null)** Set the fields to use (this can be a callable function, field list or array)
* **$component->setValidator($validator = null)** Set the validator to use (this can be a callable function or Validator)

## Caveats
* A very deep nested EditableRow will be very slow, since it has many request handlers to access, but not much I can do about this behaviour...