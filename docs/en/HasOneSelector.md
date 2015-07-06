HasOneSelector
===================

The `Milkyway\SS\GridFieldUtils\HasOneSelector` component allows you to select a has one from the current GridField

```php
    $grid->getConfig()->addComponent($component = new Milkyway\SS\GridFieldUtils\HasOneSelector($relation = 'FeaturedItem', $columnTitle = ''));
```

## Public properties
* **$component->columnTitle** Column Title (defaults to {{relation}})
* **$component->resetButtonTitle** Reset Button Title (defaults to Reset {{relation}})
* **$component->targetFragment** Where the reset button is placed (defaults to before)

## Use Cases
* Select a featured item from a HasManyList/ManyManyList