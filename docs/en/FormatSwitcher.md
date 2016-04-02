FormatSwitcher
==============

The `Milkyway\SS\GridFieldUtils\FormatSwitcher` component allows you to switch between different GridField formats.

**NOTE:** Due to the way the GridField reload function works, when changing the GridField FormField, only class will be changed, and the children of the GridField. So you cannot use this switcher to change data-attributes on the GridField.

Formats included:
* Milkyway\SS\GridFieldUtils\DisplayAsTimeline: Display as Timeline

```php
    $grid->getConfig()->addComponent($component = new Milkyway\SS\GridFieldUtils\FormatSwitcher($fragment = 'buttons-before-right'));
```

## Public methods
* **$component->setDefault($default)** Set the default format (uses the button state as the unique ID)
* **$component->setFormats($formats)** Set an array of formats to choose from (by default, finds all known formats to list)
* **$component->setFormatCallback** Set a callback to be called during format (two parameters for callback: GridField and state)

## Public properties
* **$component->urlSegment** Set the url segment for setting format (in case you ever need two?)
* **$component->unformatted** Set the title and state of the unformatted GridField

## Creating new Formats
It is recommended to create new formats by implementing the `Milkyway\SS\GridFieldUtils\Contracts\Format` interface. These are automatically picked up by the FormatSwitcher when it is added to a GridField.

You can also use callbacks as formatters.

```php
    $component->setFormats([
        'cleaner' => [
            'title' => 'Clean',
            'format' => function($gridField) {

            },
            'unformat' => function($gridField) {

            }
        ],
    ]);
```
