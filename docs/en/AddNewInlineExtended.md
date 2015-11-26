AddNewInlineExtended
====================

The `Milkyway\SS\GridFieldUtils\AddNewInlineExtended` component allows you to add new records inline. It automatically integrates with GridFieldEditableColumns and EditableRow, so fields do not appear more than once, and they can be toggled on/off.

If no fields are set, it will pull fields from either the [EditableRow](EditableRow.md) or GridFieldEditableColumns component on the current grid respectively, or default to the getEditableRowFields($record) or getCMSFields methods on the record respectively.

```php
    $grid->getConfig()->addComponent($component = new Milkyway\SS\GridFieldUtils\AddNewInlineExtended(fragment = 'buttons-before-left', $title = '', $fields = null));
```

## Public properties
* **$component->setWorkingParentOnRecordTo** Set the working parent on the currently edited record (defaults to Parent). This is usually useful for ManyManyLists, so you can access the parent record in the getCMSFields() method by using $record->Parent. If you already have a Parent field in your record, you should change this value.
* **$component->loadViaAjax** Set new rows to be loaded in via AJAX. It is slower, but it also makes sure any javascript files are loaded in with the form. This is enabled by default (disable it if your fields do not need custom JS).
* **$component->cacheAjaxLoading** New rows are cached via AJAX by default
* **$component->openToggleByDefault** If true, open the toggle by default (only valid when [EditableRow](EditableRow.md) is also attached)
* **$component->hideUnlessOpenedWithEditableColumns** If disabled, will override GridFieldEditableColumns for new rows
* **$component->prepend** If enabled, will attach new records to the top of the GridField

## Public methods
* **$component->setTitle($title)** Set the title of the button
* **$component->setFields($fields = null)** Set the fields to use (this can be a callable function, field list or array)
* **$component->setValidator($validator = null)** Set the validator to use (this can be a callable function or Validator)