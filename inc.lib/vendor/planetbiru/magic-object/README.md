# MagicObject

# Introduction

MagicObject is a library for making applications in the PHP language very easily. MagicObject can be derived into other classes with various intended uses.

Some examples of using MagicObject are as follows:

1. create dynamic objects
2. create objects with setters and getters
3. access the database
4. serialize and deserialize objects in JSON format
5. reads INI, Yaml, and JSON files
6. Get the environment variable value
7. encrypt and decrypt application configuration
8. annotations
9. debug objects 



# Installation

To install Magic Obbject

```
composer require planetbiru/magic-object:0.0.4
```

or if composer is not installed

```
php composer.phar require planetbiru/magic-object:0.0.4
```

To remove Magic Obbject

```
composer remove planetbiru/magic-object
```

or if composer is not installed

```
php composer.phar remove planetbiru/magic-object
```

To install composer on your PC or download latest composer.phar, click https://getcomposer.org/download/ 

# Tutorial

Tutorial is provided here https://github.com/Planetbiru/MagicObject/blob/main/tutorial.md


# Example

## Simple Object

## Yaml

**Yaml File**

```yaml
result_per_page: 20
song_base_url: ${SONG_BASE_UER}
app_name: Music Production Manager
user_image:
  width: 512
  height: 512
album_image:
  width: 512
  height: 512
song_image:
  width: 512
  height: 512
database:
  time_zone_system: Asia/Jakarta
  default_charset: utf8
  driver: ${APP_DATABASE_TYPE}
  host: ${APP_DATABASE_SERVER}
  port: ${APP_DATABASE_PORT}
  username: ${APP_DATABASE_USER}
  password: ${APP_DATABASE_PASSWORD}
  database_name: ${APP_DATABASE_NAME}
  database_schema: public
  time_zone: ${APP_DATABASE_TIME_ZONE}
  salt: ${APP_DATABASE_SALT}
```

**Configuration Object**

Create class `ConfigApp` by extends `MagicObject`

```php
<?php
namespace MusicProductionManager\Config;

use MagicObject\MagicObject;

class ConfigApp extends MagicObject
{
    /**
     * Constructor
     *
     * @param mixed $data Initial data
     * @param bool $readonly Readonly flag
     */
    public function __construct($data = null, $readonly = false)
    {
        if($data != null)
        {
            parent::__construct($data);
        }
        $this->readOnly($readonly);
    }
    
}
```

```php
<?php

$cfg = new ConfigApp(null, true);
$cfg->loadYamlFile(dirname(__DIR__)."/.cfg/app.yml", true, true);

// to get database object,
// $cfg->getDatabase()
//
// to get database.host
// $cfg->getDatabase()->getHost()
// to get database.database_name
// $cfg->getDatabase()->getDatabaseName()
```

# Application

One application that uses **MagicObjects** is **Music Production Manager** https://github.com/kamshory/MusicProductionManager

