MinorActionsHolder
==================

The `Milkyway\SS\GridFieldUtils\MinorActionsHolder` component allows you to group GridField buttons in a dropdown

```php
    $grid->getConfig()->addComponent($component = new Milkyway\SS\GridFieldUtils\MinorActionsHolder($fragment = 'buttons-before-left', $title = '', $id = ''));
```

## How to use
After adding the above component, to add buttons to it simply change the fragment of the button to **actions-**actionsHolderFragment (for example, if you added MinorActionsHolder to the buttons-before-left, the fragment will be actions-buttons-before-left)

```php
    $grid->getConfig()->addComponent(new GridFieldAddNewButton($fragment = 'actions-buttons-before-left'));
```

If you would like to add more than one MinorActionsHolder to the same fragment, you will have to set up an id for it when you add it to the config (third parameter on construction)

```php
    $grid->getConfig()->addComponent(new Milkyway\SS\GridFieldUtils\MinorActionsHolder('before', $title = 'Another one!', $id = 'minor'));
```


## Public methods
* **$component->setTitle($title)** Set a title (label) for the button
* **$component->setShowEmptyString($string)** Set a string to show in dropdown when default (the placeholder for a normal dropdown)