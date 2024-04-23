## Extend Magic Object

User can extend `MagicObject` to many classes.

```php
<?php

use MagicObject\MagicObject;

/**
 * Example to extends MagicObject
 * 
 * @JSON(property-naming-strategy=SNAKE_CASE, prettify=true)
 */
class MyObject extends MagicObject
{
    
}
```

### Class Parameters

**@JSON**

`@JSON` is parameter to inform how the object will be serialized.

Attributes:
1. `property-naming-strategy`

Allowed value:

- `SNAKE_CASE` all column will be snace case when `__toString()` or `dumpYaml()` method called.
- `CAMEL_CASE` all column will be camel case when `__toString()` or `dumpYaml()` method called.

Default value: `CAMEL_CASE`

1. `prettify`

Allowed value:

- `true` JSON string will be prettified
- `false` JSON string will not be prettified

Default value: `false`