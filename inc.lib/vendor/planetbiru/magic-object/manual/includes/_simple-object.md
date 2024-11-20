## Simple Object

### Set and Get Properties Value

```php
<?php
use MagicObject\MagicObject;

require_once __DIR__ . "/vendor/autoload.php";

$someObject = new MagicObject();
$someObject->setId(1);
$someObject->setRealName("Someone");
$someObject->setPhone("+62811111111");
$someObject->setEmail("someone@domain.tld");

echo "ID        : " . $someObject->getId() . "\r\n";
echo "Real Name : " . $someObject->getRealName() . "\r\n";
echo "Phone     : " . $someObject->getPhone() . "\r\n";
echo "Email     : " . $someObject->getEmail() . "\r\n";

// get JSON string of the object
echo $someObject;

// or you can debug with error_log
error_log($someObject);
```

### Unset Properties

```php
<?php
use MagicObject\MagicObject;

require_once __DIR__ . "/vendor/autoload.php";

$someObject = new MagicObject();
$someObject->setId(1);
$someObject->setRealName("Someone");
$someObject->setPhone("+62811111111");
$someObject->setEmail("someone@domain.tld");

echo "ID        : " . $someObject->getId() . "\r\n";
echo "Real Name : " . $someObject->getRealName() . "\r\n";
echo "Phone     : " . $someObject->getPhone() . "\r\n";
echo "Email     : " . $someObject->getEmail() . "\r\n";

$someObject->unsetPhone();
echo "Phone     : " . $someObject->getPhone() . "\r\n";

// get JSON string of the object
echo $someObject;

// or you can debug with
error_log($someObject);
```

### Check if Properties has Value

```php
<?php
use MagicObject\MagicObject;

require_once __DIR__ . "/vendor/autoload.php";

$someObject = new MagicObject();
$someObject->setId(1);
$someObject->setRealName("Someone");
$someObject->setPhone("+62811111111");
$someObject->setEmail("someone@domain.tld");

echo "ID        : " . $someObject->getId() . "\r\n";
echo "Real Name : " . $someObject->getRealName() . "\r\n";
echo "Phone     : " . $someObject->getPhone() . "\r\n";
echo "Email     : " . $someObject->getEmail() . "\r\n";

$someObject->unsetPhone();
if($someObject->hasValuePhone())
{
    echo "Phone     : " . $someObject->getPhone() . "\r\n";
}
else
{
    echo "Phone value is not set\r\n";
}
// another way
if($someObject->issetPhone())
{
    echo "Phone     : " . $someObject->getPhone() . "\r\n";
}
else
{
    echo "Phone value is not set\r\n";
}
// get JSON string of the object
echo $someObject;

// or you can debug with
error_log($someObject);
```

### Push

Push is present in MagicObject version 1.22. Push is used to add array elements from a MagicObject property. The `push` method basically uses the `array_push` function which is a built-in PHP function.

As with the `set` method, users can use the `push` method in two ways:

1. using a subfix in the form of a property name written in camelcase style with one parameter, namely the value of the element to be added.
2. using two parameters, namely the property name written in camelcase style and the value of the element to be added.

**Warning!**

Be careful when using the `push` method. If a property has a value other than an array and the user calls the `push` method on that property, MagicObject will ignore it and nothing will change the property value..

```
<?php
use MagicObject\MagicObject;

require_once __DIR__ . "/vendor/autoload.php";

$someObject = new MagicObject();

$someObject->pushData("Text 1");
$someObject->pushData("Text 2");
$someObject->pushData(3);
$someObject->pushData(4.0);
$someObject->pushData(true);

/*
or

$someObject->push("data", "Text 1");
$someObject->push("data", "Text 2");
$someObject->push("data", 3);
$someObject->push("data", 4.1);
$someObject->push("data", true);
*/

echo $someObject;
```

Output will be

```json
{"data":["Text 1","Text 2",3,4.1,true]}
```

### Pop

Pop is present in MagicObject version 1.22. Pop is used to remove the last element of an array from a MagicObject property. The `pop` method basically uses the `array_pop` function which is a built-in PHP function.

As with the `unset` method, users can use the `pop` method in two ways:

1. using a subfix in the form of a property name written in camelcase style.
2. using one parameter, namely the property name.

**Warning!**

The `pup` method only applies if the property is a traditional array. It does not apply to scalar, object, and associated array properties.

```
<?php
use MagicObject\MagicObject;

require_once __DIR__ . "/vendor/autoload.php";

$someObject = new MagicObject();

$someObject->setData(["Text 1", "Text 2", 3, 4.1, true]);
echo $someObject."\r\n\r\n";

echo "Pop\r\n";
echo $someObject->popData()."\r\n";
echo "After Pop\r\n";
echo $someObject."\r\n\r\n";
echo $someObject->popData()."\r\n";
echo "After Pop\r\n";
echo $someObject."\r\n\r\n";
echo $someObject->popData()."\r\n";
echo "After Pop\r\n";
echo $someObject."\r\n\r\n";
```

Output will be:

```
{"data":["Text 1","Text 2",3,4.1,true]}

Pop
1
After Pop
{"data":["Text 1","Text 2",3,4.1]}

4.1
After Pop
{"data":["Text 1","Text 2",3]}

3
After Pop
{"data":["Text 1","Text 2"]}
```

`push` and `pop` example:

```php
<?php
use MagicObject\MagicObject;

require_once __DIR__ . "/vendor/autoload.php";
$someObject = new MagicObject();


$someObject->pushData("Text 1");
$someObject->pushData("Text 2");
$someObject->pushData(3);
$someObject->pushData(4.1);
$someObject->pushData(true);

/*
or

$someObject->push("data", "Text 1");
$someObject->push("data", "Text 2");
$someObject->push("data", 3);
$someObject->push("data", 4.1);
$someObject->push("data", true);
*/


echo "After Push\r\n";

echo $someObject."\r\n\r\n";

echo "Pop\r\n";
echo $someObject->popData()."\r\n";
// or echo $someObject->pop("data")."\r\n";
echo "After Pop\r\n";
echo $someObject."\r\n\r\n";
echo $someObject->popData()."\r\n";
// or echo $someObject->pop("data")."\r\n";
echo "After Pop\r\n";
echo $someObject."\r\n\r\n";
echo $someObject->popData()."\r\n";
// or echo $someObject->pop("data")."\r\n";
echo "After Pop\r\n";
echo $someObject."\r\n\r\n";
```

Output will be:

```
After Push
{"data":["Text 1","Text 2",3,4.1,true]}

Pop
1
After Pop
{"data":["Text 1","Text 2",3,4.1]}

4.1
After Pop
{"data":["Text 1","Text 2",3]}

3
After Pop
{"data":["Text 1","Text 2"]}
```

`push` and `pop` only apply to properties that are arrays.

```php
<?php
use MagicObject\MagicObject;

require_once __DIR__ . "/vendor/autoload.php";
$someObject = new MagicObject();

$someObject->setData(8); // push and pop will not change this
$someObject->pushData("Text 1");
$someObject->pushData("Text 2");
$someObject->pushData(3);
$someObject->pushData(4.1);
$someObject->pushData(true);

echo "After Push\r\n";

echo $someObject."\r\n\r\n";

echo "Pop\r\n";
echo $someObject->popData()."\r\n";
echo "After Pop\r\n";
echo $someObject."\r\n\r\n";
echo $someObject->popData()."\r\n";
echo "After Pop\r\n";
echo $someObject."\r\n\r\n";
echo $someObject->popData()."\r\n";
echo "After Pop\r\n";
echo $someObject."\r\n\r\n";
``