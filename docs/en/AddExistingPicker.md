AddExistingPicker
=================

The `Milkyway\SS\GridFieldUtils\AddExistingPicker` component is an advanced version of GridFieldAddExistingSearchButton. It allows you to select more than one item to add to the list while the window is open, and also has options for overriding the add action for more complex uses.

```php
    $grid->getConfig()->addComponent($component = new Milkyway\SS\GridFieldUtils\AddExistingPicker(fragment = 'buttons-before-left'));
```

## Public methods
* **$component->async($isAsync = true)** Set whether the addition of new records is handled asynchronously, with undo UX
* **$component->setAddHandler(callable)** Set a callable that will handle the add action
* **$component->setUndoHandler(callable)** Set a callable that will ahndle the undo of the add action (for async requests)
* **$component->setSearchHandlerFactory(callable)** Set a callable factory that will create the Search RequestHandler.