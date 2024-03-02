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
8. debug objects 



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


# Example

## Entity

```php
<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="album")
 */
class Album extends MagicObject
{
	/**
	 * Album ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="album_id", type="varchar(50)", length=50, nullable=false)
	 * @var string
	 */
	protected $albumId;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $name;

	/**
	 * Description
	 * 
	 * @Column(name="description", type="longtext", nullable=true)
	 * @var string
	 */
	protected $description;

	/**
	 * Release Date
	 * 
	 * @Column(name="release_date", type="date", nullable=true)
	 * @var string
	 */
	protected $releaseDate;

	/**
	 * Number Of Song
	 * 
	 * @Column(name="number_of_song", type="int(11)", length=11, nullable=true)
	 * @var integer
	 */
	protected $numberOfSong;

	/**
	 * Duration
	 * 
	 * @Column(name="duration", type="float", nullable=true)
	 * @var double
	 */
	protected $duration;

	/**
	 * Sort Order
	 * 
	 * @Column(name="sort_order", type="int(11)", length=11, nullable=true)
	 * @var integer
	 */
	protected $sortOrder;

	/**
	 * Time Create
	 * 
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * Time Edit
	 * 
	 * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * Admin Create
	 * 
	 * @Column(name="admin_create", type="varchar(40)", length=40, nullable=true, updatable=false)
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * Admin Edit
	 * 
	 * @Column(name="admin_edit", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * IP Create
	 * 
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * IP Edit
	 * 
	 * @Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var bool
	 */
	protected $active;

	/**
	 * As Draft
	 * 
	 * @Column(name="as_draft", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var bool
	 */
	protected $asDraft;

}
```

MagicObject will not update `time_create`, `admin_create`, and `ip_create` because `updatable=false`. So, even if the application wants to update this value, this column will be ignored when performing an update query to the database.

## Usage

```php
<?php

use MagicObject\Database\PicoDatabase;
use MagicObject\Database\PicoDatabaseCredentials;
use MusicProductionManager\Config\ConfigApp;

use MusicProductionManager\Config\ConfigApp;

use MusicProductionManager\Data\Entity\Album;

require_once dirname(__DIR__)."/vendor/autoload.php";

$cfg = new ConfigApp(null, true);
$cfg->loadYamlFile(dirname(__DIR__)."/.cfg/app.yml", true, true);

$databaseCredentials = new PicoDatabaseCredentials($cfg->getDatabase());
$database = new PicoDatabase($databaseCredentials);
try
{
    $database->connect();
    
    // create new 
    
    $album1 = new Album(null, $database);
    $album1->setAibumId("123456");
    $album1->setName("Album 1");
    $album1->setAdminCreate("USER1");
    $album1->setDuration(300);
    
    
    
    // other way to create object
    // create object from stdClass or other object with match property (snake case or camel case)
    $data = new stdClass;
    // snake case
    $data->album_id = "123456";
    $data->name = "Album 1";
    $data->admin_create = "USER1";
    $data->duration = 300;
    
    // or camel case
    $data->albumId = "123456";
    $data->name = "Album 1";
    $data->adminCreate = "USER1";
    $data->duration = 300;
    
    $album1 = new Album($data, $database); 
    
    // other way to create object
    // create object from associated array with match property (snake case or camel case)
    $data = array();
    // snake case
    $data["album_id"] "123456";
    $data["name"] = "Album 1";
    $data["admin_create"] = "USER1";
    $data["duration"] = 300;
    
    // or camel case
    $data["albumId"] = "123456";
    $data["name"] = "Album 1";
    $data["adminCreate"] = "USER1";
    $data["duration"] = 300;
    $album1 = new Album($data, $database);
    
    
    // get value from form
    // this way is not safe
    $album1 = new Album($_POST, $database);
    
    
    // we can use other way
    $inputPost = new InputPost();
    
    // we can apply filter
    $inputPost->filterName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
    $inputPost->filterDescription(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
    
    // if property not present in $inputPost, we can set default value
    // please note that user can modify form and add update any unwanted properties to be updated
    $inputPost->checkboxActive(false);
    $inputPost->checkboxAsDraft(true);
    
    // we can remove any property data from object $inputPost before apply it to entity
    // it will not saved to database
    $inputPost->setSortOrder(null);
    
    $album1 = new Album($inputPost, $database);
    
    // insert to database
    $album1->insert();
    
    // insert or update
    $album1->save();
    
    // update
    // NoRecordFoundException if ID not found
    $album1->update();
    
    // convert to JSON
    $json = $album1->toString();
    // or
    $json = $album1 . "";
    
    // send to buffer output
    // automaticaly converted to string
    echo $album1;
    
    // find one by ID
    $album2 = new Album(null, $database);
    $album2->findOneByAlbumId("123456");
    
    // find multiple
    $album2 = new Album(null, $database);
    $albums = $album2->findByAdminCreate("USER1");
    $rows = $albums->getResult();
    foreach($rows as $albumSaved)
    {
        // $albumSaved is instance of Album
        
        // we can update data
        $albumSaved->setAdminEdit("USER1");
        $albumSaved->setTimeEdit(date('Y-m-d H:i:s'));
        
        // this value will not be saved to database because has no column
        $albumSaved->setAnyValue("ANY VALUE");
        
        $albumSaved->update();
    }
    
    
}
catch(Exception $e)
{
    // do nothing
}



```

# Application

One application that uses **MagicObjects** is **Music Production Manager** https://github.com/kamshory/MusicProductionManager

