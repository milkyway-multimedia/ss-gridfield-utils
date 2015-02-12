GridField Utilities
======
**GridField Utilities** are a collection of GridField components that you can use with any GridField.

Includes the following:
- Milkyway\SS\GridFieldUtils\AddNewInlineExtended: A more complex version of GridFieldAddNewInlineButton, allowing you to set custom fields, rather than copying GridFieldEditableColumns (defaults to this behaviour)
- Milkyway\SS\GridFieldUtils\EditableRow: A more complex version of GridFieldEditableColumns, allowing you to set custom fields. This makes the GridField act like a Tree, with nested GridFields working as expected
- Milkyway\SS\GridFieldUtils\HelpButton: Add a help button to your GridField that you can supply content for (will open a modal dialog)
- Milkyway\SS\GridFieldUtils\SaveAllButton: Will execute all components on the GridField that implement the GridField_SaveHandler (for use in ModelAdmin where there is no save button)
- Milkyway\SS\GridFieldUtils\DisplayAsTimeline: Will change the display of your GridField to a timeline (probably not be compatible with custom GridField Components, but compatible with framework GridField Components)
- Milkyway\SS\GridFieldUtils\GridFieldDetailForm: Works exactly the same as the standard GridFieldDetailForm, with ability to change the url segment (hence having multiple GridFieldDetailForms on the one GridField)
- Milkyway\SS\GridFieldUtils\GridFieldAddNewButton: An add button for the above GridFieldDetailForm

### Caveats
- The DisplayAsTimeline component is very hacky at this stage, due to the lack of support for templates in GridField. It has only been tested in Google Chrome
- The SaveAllButton will be VERY slow when your objects are versioned and there are many of them
- A deep nested EditableRow will be very slow, since it has many request handlers to access, but not much I can do about this behaviour...

## Install
Add the following to your composer.json file

```

    "require"          : {
		"milkyway-multimedia/ss-gridfield-utils": "dev-master"
	}

```

## TODO
* Get DisplayAsTimeline to work with sorting (just in case)
* Test EditableRow with tabs...

## License
* MIT

## Version
* Version 0.1 - Alpha

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")