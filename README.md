GridField Utilities
======
**GridField Utilities** are a collection of GridField components that you can use with any GridField.

Includes the following (note they all live in the namespace Milkyway\SS\GridFieldUtils):
* [AddNewInlineExtended](docs/en/AddNewInlineExtended.md): A more complex version of GridFieldAddNewInlineButton, allowing you to set custom fields, rather than copying GridFieldEditableColumns (defaults to this behaviour)
* [EditableRow](docs/en/EditableRow.md): adds an expandable form to each row in the GridField, allowing you to edit records directly from the GridField.
* [HasOneSelector](docs/en/HasOneSelector.md): Allow you to select a has one relation from the current GridField
* [AddExistingPicker](docs/en/AddExistingPicker.md): Works exactly like the one in gridfieldextensions, except it allows you to add more before closing the window - allowing for a faster workflow (requires silverstripe-australia/gridfieldextensions)
* [MinorActionsHolder](docs/en/MinorActionsHolder.md): Defines a new fragment that will holds SS UI buttons as a dropdown (not touch friendly)
* [AddNewModal](docs/en/AddNewModal.md): Opens up the detail form in a modal window 
* RangeSlider: Filter your GridField using a slider, for a more user-friendly option for viewing lots of records
* HelpButton: Add a help button to your GridField that you can supply content for (will open a modal dialog)
* SaveAllButton: Will execute all components on the GridField that implement the GridField_SaveHandler (for use in ModelAdmin where there is no save button)
* DisplayAsTimeline: Will change the display of your GridField to a timeline (probably not be compatible with custom GridField Components, but compatible with framework GridField Components)
* GridFieldDetailForm: Works exactly the same as the standard GridFieldDetailForm, with ability to change the url segment (hence having multiple GridFieldDetailForms on the one GridField)
* GridFieldAddNewButton: An add button for the above GridFieldDetailForm

### Caveats
* The DisplayAsTimeline component is very hacky at this stage, due to the lack of support for templates in GridField. It has only been tested in Google Chrome
* The SaveAllButton will be VERY slow when your objects are versioned and there are many of them
* A deep nested EditableRow will be very slow, since it has many request handlers to access, but not much I can do about this behaviour...

### Additional notes
You will need to install the [milkyway-multimedia/ss-mwm-formfields](https://github.com/milkyway-multimedia/ss-mwm-formfields) module to use the RangeSlider component.

## Requirements
* [silverstripe/framework](https://github.com/silverstripe/framework) 
* [milkyway-multimedia/ss-mwm](https://github.com/milkyway-multimedia/ss-mwm) 

## Install
Add the following to your composer.json file

```

    "require"          : {
		"milkyway-multimedia/ss-gridfield-utils": "dev-master"
	}

```

## Suggested Packages
* [silverstripe-australia/gridfieldextensions](https://github.com/silverstripe-australia/gridfieldextensions) 

## Credits
- [ajshort](https://github.com/ajshort "ajshort on Github"): He did most of the coding of GridFieldExtensions, which I borrowed for the more complex versions in this module
- [silverstripe-australia](https://github.com/silverstripe-australia "silverstripe-australia on Github"): They now look after the GridFieldExtensions module, and have done some updates which I have probably borrowed

## TODO
* Screenshots!!
* Make MinorActionsHolder touch friendly
* Get DisplayAsTimeline to work with sorting (just in case)
* Test EditableRow with tabs...
* Make RangeSlider work with Date Range Fields / Any Range Fields
* Test RangeSlider more

## License
* MIT

## Version
* Version 0.2 - Alpha

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")