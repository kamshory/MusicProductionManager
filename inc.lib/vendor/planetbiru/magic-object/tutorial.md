# MagicObject Tutorial and Example

## Simpe Object

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

// or you can debug with
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
// get JSON string of the object
echo $someObject;

// or you can debug with
error_log($someObject);
```

## Multilevel Object

```php
<?php
use MagicObject\MagicObject;

require_once __DIR__ . "/vendor/autoload.php";

$car = new MagicObject();
$tire = new MagicObject();
$body = new MagicObject();

$tire->setDiameter(12);
$tire->setPressure(60);

$body->setLength(320);
$body->setWidth(160);
$body->Height(140);
$body->setColor("red");

$car->setTire($tire);
$car->setBody($body);

echo $car;

/*
{"tire":{"diameter":12,"pressure":60},"body":{"length":320,"width":160,"height":140,"color":"red"}}
*/

// to get color

echo $car->getBody()->getColor();

```

## Object from Yaml File

```yaml
# config.yml

tire: 
  diameter: 12
  pressure: 60
body: 
  length: 320
  width: 160
  height: 140
  color: red

```

```php
<?php
use MagicObject\MagicObject;

require_once __DIR__ . "/vendor/autoload.php";

$car = new MagicObject();
// load file config.yml
// will not replace value with environment variable
// load as object instead of associated array
$car->loadYamlFile("config.yml", false, true);

echo $car;
/*
{"tire":{"diameter":12,"pressure":60},"body":{"length":320,"width":160,"height":140,"color":"red"}}
*/

// to get color

echo $car->getBody()->getColor();

```

## Replace Value with Environment Variable

```yaml
# config.yml

tire: 
  diameter: ${TIRE_DIAMETER}
  pressure: ${TIRE_PRESSURE}
body: 
  length: ${BODY_LENGTH}
  width: ${BODY_WIDTH}
  height: ${BODY_HEIGHT}
  color: ${BODY_COLOR}

```

Before execute this script, user must set environment variable for `TIRE_DIAMETER`, `TIRE_PRESSURE`, `BODY_LENGTH`, `BODY_WIDTH`, `BODY_HEIGHT`, and `BODY_COLOR` depend on the operating system used.

```php
<?php
use MagicObject\MagicObject;

require_once __DIR__ . "/vendor/autoload.php";

$car = new MagicObject();
// load file config.yml
// will replace value with environment variable
// load as object instead of associated array
$car->loadYamlFile("config.yml", true, true);

echo $car;

// to get color

echo $car->getBody()->getColor();

```

## Secret Object

Secret Objects are very important in applications that use very sensitive and secret configurations. This configuration must be encrypted so that it cannot be seen either when someone tries to open the configuration file, environment variables, or even when the developer accidentally debugs an object related to the database so that the properties of the database object are exposed including the host name, database name, username and even password.

```php
<?php

namespace MagicObject\Database;

use MagicObject\SecretObject;

class PicoDatabaseCredentials extends SecretObject
{
	/**
	 * Database driver
	 *
	 * @var string
	 */
	protected $driver = 'mysql';

	/**
	 * Database server host
	 *
	 * @EncryptIn
	 * @DecryptOut
	 * @var string
	 */
	protected $host = 'localhost';

	/**
	 * Database server port
	 * @var integer
	 */
	protected $port = 3306;

	/**
	 * Database username
	 *
	 * @EncryptIn
	 * @DecryptOut
	 * @var string
	 */
	protected $username = "";

	/**
	 * Database user password
	 *
	 * @EncryptIn
	 * @DecryptOut
	 * @var string
	 */
	protected $password = "";

	/**
	 * Database name
	 *
	 * @EncryptIn
	 * @DecryptOut
	 * @var string
	 */
	protected $databaseName = "";
	
	/**
	 * Database schema
	 *
	 * @EncryptIn
	 * @DecryptOut
	 * @var string
	 */
	protected $databseSchema = "public";

	/**
	 * Application time zone
	 *
	 * @var string
	 */
	protected $timeZone = "Asia/Jakarta";
}
```

The `@EncryptIn` annotation will encrypt the value before it is assigned to the associated property with the `set` method. The `@DecryptOut` annotation will decrypt the property value after it is retrieved by the `get` method. Once all values are encrypted, the user does not need to annotate `@EncryptIn` again.

```php
<?php

namespace MagicObject\Database;

use MagicObject\SecretObject;

class PicoDatabaseCredentials extends SecretObject
{
	/**
	 * Database driver
	 *
	 * @var string
	 */
	protected $driver = 'mysql';

	/**
	 * Database server host
	 *
	 * @DecryptOut
	 * @var string
	 */
	protected $host = 'localhost';

	/**
	 * Database server port
	 * @var integer
	 */
	protected $port = 3306;

	/**
	 * Database username
	 *
	 * @DecryptOut
	 * @var string
	 */
	protected $username = "";

	/**
	 * Database user password
	 *
	 * @DecryptOut
	 * @var string
	 */
	protected $password = "";

	/**
	 * Database name
	 *
	 * @DecryptOut
	 * @var string
	 */
	protected $databaseName = "";
	
	/**
	 * Database schema
	 *
	 * @DecryptOut
	 * @var string
	 */
	protected $databseSchema = "public";

	/**
	 * Application time zone
	 *
	 * @var string
	 */
	protected $timeZone = "Asia/Jakarta";
}
```

## Input GET/POST/REQUEST/COOKIE

### Input GET

```php

use MagicObject\Request\InputGet;

require_once __DIR__ . "/vendor/autoload.php";

$inputGet = new InputGet();

$name = $inputGet->getRealName();
// equivalen to 
$name = $_GET['real_name'];

```

### Input Post

```php

use MagicObject\Request\InputPost;

require_once __DIR__ . "/vendor/autoload.php";

$inputPost = new InputPost();

$name = $inputPost->getRealName();
// equivalen to 
$name = $_POST['real_name'];

```

### Input Request

```php

use MagicObject\Request\InputRequest;

require_once __DIR__ . "/vendor/autoload.php";

$inputRequest = new InputRequest();

$name = $inputRequest->getRealName();
// equivalen to 
$name = $_REQUEST['real_name'];

```

### Input Cookie

```php

use MagicObject\Request\InputCookie;

require_once __DIR__ . "/vendor/autoload.php";

$inputCookie = new InputCookie();

$name = $inputCookie->getRealName();
// equivalen to 
$name = $_COOKIE['real_name'];

```

### Filter Input

Filter input from InputGet, InputPost, InputRequest and InputCookie

```php

use MagicObject\Request\InputGet;
use MagicObject\Request\PicoFilterConstant;

require_once __DIR__ . "/vendor/autoload.php";

$inputGet = new InputGet();

$name = $inputGet->getRealName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
// equivalen to 
$name =  filter_input(
    INPUT_GET,
    'real_name',
    FILTER_SANITIZE_SPECIAL_CHARS
);


// another way to filter input
$inputGet->filterRealName(PicoFilterConstant::FILTER_SANITIZE_SPECIAL_CHARS);
$name = $inputGet->getRealName();

```

List of filter

```
FILTER_SANITIZE_NO_DOUBLE_SPACE = 512;
FILTER_SANITIZE_PASSWORD = 511;
FILTER_SANITIZE_ALPHA = 510;
FILTER_SANITIZE_ALPHANUMERIC = 509;
FILTER_SANITIZE_ALPHANUMERICPUNC = 506;
FILTER_SANITIZE_NUMBER_UINT = 508;
FILTER_SANITIZE_NUMBER_INT = 519;
FILTER_SANITIZE_URL = 518;
FILTER_SANITIZE_NUMBER_FLOAT = 520;
FILTER_SANITIZE_STRING_NEW = 513;
FILTER_SANITIZE_ENCODED = 514;
FILTER_SANITIZE_STRING_INLINE = 507;
FILTER_SANITIZE_STRING_BASE64 = 505;
FILTER_SANITIZE_IP = 504;
FILTER_SANITIZE_NUMBER_OCTAL = 503;
FILTER_SANITIZE_NUMBER_HEXADECIMAL = 502;
FILTER_SANITIZE_COLOR = 501;
FILTER_SANITIZE_POINT = 500;
FILTER_SANITIZE_BOOL = 600;
FILTER_VALIDATE_URL = 273;
FILTER_VALIDATE_EMAIL = 274;
FILTER_SANITIZE_EMAIL = 517;
FILTER_SANITIZE_SPECIAL_CHARS = 515;
FILTER_SANITIZE_ASCII = 601;
```

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

Strategy to generate auto value:

**1. GenerationType.UUID**

Generate 20 bytes unique ID
 - 14 byte hexadecimal of uniqid https://www.php.net/manual/en/function.uniqid.php
 - 6 byte hexadecimal or random number

**2. GenerationType.IDENTITY**

Autoincrement using database feature

MagicObject will not update `time_create`, `admin_create`, and `ip_create` because `updatable=false`. So, even if the application wants to update this value, this column will be ignored when performing an update query to the database.

### Usage

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

## Pagination

Example parameters:

`genre_id=0648d4e176da4df4472d&album_id=&artist_vocal_id=&name=&vocal=&lyric_complete=&active=&page=2&orderby=title&ordertype=asc`

Create entity according to database

```php
<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="song")
 */
class EntitySong extends MagicObject
{
	/**
	 * Song ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="song_id", type="varchar(50)", length=50, nullable=false)
	 * @var string
	 */
	protected $songId;

	/**
	 * Random Song ID
	 * 
	 * @Column(name="random_song_id", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $randomSongId;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $name;

	/**
	 * Title
	 * 
	 * @Column(name="title", type="text", nullable=true)
	 * @var string
	 */
	protected $title;

	/**
	 * Album ID
	 * 
	 * @Column(name="album_id", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $albumId;

	/**
	 * @JoinColumn(name="album_id")
	 * @var Album
	 */
	protected $album;

	/**
	 * Track Number
	 * 
	 * @Column(name="track_number", type="int(11)", length=11, nullable=true)
	 * @var integer
	 */
	protected $trackNumber;
	
	/**
	 * Producer ID
	 * 
	 * @Column(name="producer_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $producerId;
	
	/**
	 * Producer
	 * 
	 * @JoinColumn(name="producer_id")
	 * @var Producer
	 */
	protected $producer;

	/**
	 * Artist Vocal
	 * 
	 * @Column(name="artist_vocalist", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $artistVocalist;
	
	/**
	 * Artist Vocal
	 * 
	 * @JoinColumn(name="artist_vocalist")
	 * @var Artist
	 */
	protected $vocalist;

	/**
	 * Artist Composer
	 * 
	 * @Column(name="artist_composer", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $artistComposer;
	
	/**
	 * Artist Composer
	 * 
	 * @JoinColumn(name="artist_composer")
	 * @var Artist
	 */
	protected $composer;

	/**
	 * Artist Arranger
	 * 
	 * @Column(name="artist_arranger", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $artistArranger;
	
	/**
	 * Artist Arranger
	 * 
	 * @JoinColumn(name="artist_arranger")
	 * @var Artist
	 */
	protected $arranger;

	/**
	 * File Path
	 * 
	 * @Column(name="file_path", type="text", nullable=true)
	 * @var string
	 */
	protected $filePath;

	/**
	 * File Name
	 * 
	 * @Column(name="file_name", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $fileName;

	/**
	 * File Type
	 * 
	 * @Column(name="file_type", type="varchar(100)", length=100, nullable=true)
	 * @var string
	 */
	protected $fileType;

	/**
	 * File Extension
	 * 
	 * @Column(name="file_extension", type="varchar(20)", length=20, nullable=true)
	 * @var string
	 */
	protected $fileExtension;

	/**
	 * File Size
	 * 
	 * @Column(name="file_size", type="bigint(20)", length=20, nullable=true)
	 * @var integer
	 */
	protected $fileSize;

	/**
	 * File Md5
	 * 
	 * @Column(name="file_md5", type="varchar(32)", length=32, nullable=true)
	 * @var string
	 */
	protected $fileMd5;

	/**
	 * File Upload Time
	 * 
	 * @Column(name="file_upload_time", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $fileUploadTime;

	/**
	 * First Upload Time
	 * 
	 * @Column(name="first_upload_time", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $firstUploadTime;

	/**
	 * Last Upload Time
	 * 
	 * @Column(name="last_upload_time", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $lastUploadTime;

	/**
	 * File Path Midi
	 * 
	 * @Column(name="file_path_midi", type="text", nullable=true)
	 * @var string
	 */
	protected $filePathMidi;

	/**
	 * Last Upload Time Midi
	 * 
	 * @Column(name="last_upload_time_midi", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $lastUploadTimeMidi;

	/**
	 * File Path Xml
	 * 
	 * @Column(name="file_path_xml", type="text", nullable=true)
	 * @var string
	 */
	protected $filePathXml;

	/**
	 * Last Upload Time Xml
	 * 
	 * @Column(name="last_upload_time_xml", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $lastUploadTimeXml;

	/**
	 * File Path Pdf
	 * 
	 * @Column(name="file_path_pdf", type="text", nullable=true)
	 * @var string
	 */
	protected $filePathPdf;

	/**
	 * Last Upload Time Pdf
	 * 
	 * @Column(name="last_upload_time_pdf", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $lastUploadTimePdf;

	/**
	 * Duration
	 * 
	 * @Column(name="duration", type="float", nullable=true)
	 * @var double
	 */
	protected $duration;

	/**
	 * Genre ID
	 * 
	 * @Column(name="genre_id", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $genreId;
	
	/**
	 * Genre ID
	 * 
	 * @JoinColumn(name="genre_id")
	 * @var Genre
	 */
	protected $genre;

	/**
	 * Bpm
	 * 
	 * @Column(name="bpm", type="float", nullable=true)
	 * @var double
	 */
	protected $bpm;

	/**
	 * Time Signature
	 * 
	 * @Column(name="time_signature", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $timeSignature;

	/**
	 * Subtitle
	 * 
	 * @Column(name="subtitle", type="longtext", nullable=true)
	 * @var string
	 */
	protected $subtitle;

	/**
	 * Subtitle Complete
	 * 
	 * @Column(name="subtitle_complete", type="tinyint(1)", length=1, nullable=true)
	 * @var bool
	 */
	protected $subtitleComplete;

	/**
	 * Lyric Midi
	 * 
	 * @Column(name="lyric_midi", type="longtext", nullable=true)
	 * @var string
	 */
	protected $lyricMidi;

	/**
	 * Lyric Midi Raw
	 * 
	 * @Column(name="lyric_midi_raw", type="longtext", nullable=true)
	 * @var string
	 */
	protected $lyricMidiRaw;

	/**
	 * Vocal
	 * 
	 * @Column(name="vocal", type="tinyint(1)", length=1, nullable=true)
	 * @var bool
	 */
	protected $vocal;

	/**
	 * Instrument
	 * 
	 * @Column(name="instrument", type="longtext", nullable=true)
	 * @var string
	 */
	protected $instrument;

	/**
	 * Midi Vocal Channel
	 * 
	 * @Column(name="midi_vocal_channel", type="int(11)", length=11, nullable=true)
	 * @var integer
	 */
	protected $midiVocalChannel;

	/**
	 * Rating
	 * 
	 * @Column(name="rating", type="float", nullable=true)
	 * @var double
	 */
	protected $rating;

	/**
	 * Comment
	 * 
	 * @Column(name="comment", type="longtext", nullable=true)
	 * @var string
	 */
	protected $comment;

	/**
	 * Image Path
	 * 
	 * @Column(name="image_path", type="text", nullable=true)
	 * @var string
	 */
	protected $imagePath;

	/**
	 * Last Upload Time Image
	 * 
	 * @Column(name="last_upload_time_image", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $lastUploadTimeImage;

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
	 * Admin Create
	 * 
	 * @Column(name="admin_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @var string
	 */
	protected $adminCreate;

	/**
	 * Admin Edit
	 * 
	 * @Column(name="admin_edit", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $adminEdit;

	/**
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var bool
	 */
	protected $active;
}
```

Filtering and pagination

```php
<?php

use MagicObject\Database\PicoDatabase;
use MagicObject\Database\PicoDatabaseCredentials;
use MusicProductionManager\Config\ConfigApp;

use MusicProductionManager\Config\ConfigApp;

use MagicObject\Database\PicoPagable;
use MagicObject\Database\PicoPage;
use MagicObject\Database\PicoSort;
use MagicObject\Database\PicoSortable;
use MagicObject\Pagination\PicoPagination;
use MagicObject\Request\PicoFilterConstant;
use MagicObject\Request\InputGet;
use MagicObject\Response\Generated\PicoSelectOption;
use MusicProductionManager\Constants\ParamConstant;
use MusicProductionManager\Data\Entity\Album;
use MusicProductionManager\Data\Entity\Artist;
use MusicProductionManager\Data\Entity\EntitySong;
use MusicProductionManager\Data\Entity\EntitySongComment;
use MusicProductionManager\Data\Entity\Genre;

use MusicProductionManager\Utility\SpecificationUtil;
use MusicProductionManager\Utility\UserUtil;

require_once dirname(__DIR__)."/vendor/autoload.php";

$cfg = new ConfigApp(null, true);
$cfg->loadYamlFile(dirname(__DIR__)."/.cfg/app.yml", true, true);

$databaseCredentials = new PicoDatabaseCredentials($cfg->getDatabase());
$database = new PicoDatabase($databaseCredentials);
try
{
    $database->connect();
    
    $inputGet = new InputGet();
    
    $orderMap = array(
        'name'=>'name', 
        'title'=>'title', 
        'rating'=>'rating',
        'albumId'=>'albumId', 
        'album'=>'albumId', 
        'trackNumber'=>'trackNumber',
        'genreId'=>'genreId', 
        'genre'=>'genreId',
        'producerId'=>'producerId',
        'artistVocalist'=>'artistVocalist',
        'artistComposer'=>'artistComposer',
        'artistAranger'=>'artistAranger',
        'duration'=>'duration',
        'subtitleComplete'=>'subtitleComplete',
        'vocal'=>'vocal',
        'active'=>'active'
    );
    $defaultOrderBy = 'albumId';
    $defaultOrderType = 'desc';
    $pagination = new PicoPagination($cfg->getResultPerPage());

    $spesification = SpecificationUtil::createSongSpecification($inputGet);

    if($pagination->getOrderBy() == '')
    {
    $sortable = new PicoSortable();
    $sort1 = new PicoSort('albumId', PicoSortable::ORDER_TYPE_DESC);
    $sortable->addSortable($sort1);
    $sort2 = new PicoSort('trackNumber', PicoSortable::ORDER_TYPE_ASC);
    $sortable->addSortable($sort2);
    }
    else
    {
    $sortable = new PicoSortable($pagination->getOrderBy($orderMap, $defaultOrderBy), $pagination->getOrderType($defaultOrderType));
    }

    $pagable = new PicoPagable(new PicoPage($pagination->getCurrentPage(), $pagination->getPageSize()), $sortable);

    $songEntity = new EntitySong(null, $database);
    $rowData = $songEntity->findAll($spesification, $pagable, $sortable, true);

    $result = $rowData->getResult();
    
    if(!empty($result))
    {
    ?>
    <div class="pagination">
        <div class="pagination-number">
        <?php
        foreach($rowData->getPagination() as $pg)
        {
            ?><span class="page-selector<?php echo $pg['selected'] ? ' page-selected':'';?>" data-page-number="<?php echo $pg['page'];?>"><a href="#"><?php echo $pg['page'];?></a></span><?php
        }
        ?>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
            <th scope="col" width="20"><i class="ti ti-edit"></i></th>
            <th scope="col" width="20"><i class="ti ti-trash"></i></th>
            <th scope="col" width="20"><i class="ti ti-player-play"></i></th>
            <th scope="col" width="20"><i class="ti ti-download"></i></th>
            <th scope="col" width="20">#</th>
            <th scope="col" class="col-sort" data-name="name">Name</th>
            <th scope="col" class="col-sort" data-name="title">Title</th>
            <th scope="col" class="col-sort" data-name="rating">Rating</th>
            <th scope="col" class="col-sort" data-name="album_id">Album</th>
            <th scope="col" class="col-sort" data-name="track_number">Track</th>
            <th scope="col" class="col-sort" data-name="genre_id">Genre</th>
            <th scope="col" class="col-sort" data-name="artist_vocalist">Vocalist</th>
            <th scope="col" class="col-sort" data-name="artist_composer">Composer</th>
            <th scope="col" class="col-sort" data-name="artist_arranger">Arranger</th>
            <th scope="col" class="col-sort" data-name="duration">Duration</th>
            <th scope="col" class="col-sort" data-name="vocal">Vocal</th>
            <th scope="col" class="col-sort" data-name="lyric_complete">subtitle</th>
            <th scope="col" class="col-sort" data-name="active">Active</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = $pagination->getOffset();
            foreach($result as $song)
            {
            $no++;
            $songId = $song->getSongId();
            $linkEdit = basename($_SERVER['PHP_SELF'])."?action=edit&song_id=".$songId;
            $linkDetail = basename($_SERVER['PHP_SELF'])."?action=detail&song_id=".$songId;
            $linkDelete = basename($_SERVER['PHP_SELF'])."?action=delete&song_id=".$songId;
            $linkDownload = "read-file.php?type=all&song_id=".$songId;
            ?>
            <tr data-id="<?php echo $songId;?>">
            <th scope="row"><a href="<?php echo $linkEdit;?>" class="edit-data"><i class="ti ti-edit"></i></a></th>
            <th scope="row"><a href="<?php echo $linkDelete;?>" class="delete-data"><i class="ti ti-trash"></i></a></th>
            <th scope="row"><a href="#" class="play-data" data-url="<?php echo $cfg->getSongBaseUrl()."/".$song->getFileName();?>?hash=<?php echo str_replace(array(' ', '-', ':'), '', $song->getLastUploadTime());?>"><i class="ti ti-player-play"></i></a></th>
            <th scope="row"><a href="<?php echo $linkDownload;?>"><i class="ti ti-download"></i></a></th>
            <th class="text-right" scope="row"><?php echo $no;?></th>
            <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-name"><?php echo $song->getName();?></a></td>
            <td><a href="<?php echo $linkDetail;?>" class="text-data text-data-title"><?php echo $song->getTitle();?></a></td>
            <td class="text-data text-data-rating"><?php echo $song->hasValueRating() ? $song->getRating() : "";?></td>
            <td class="text-data text-data-album-name"><?php echo $song->hasValueAlbum() ? $song->getAlbum()->getName() : "";?></td>
            <td class="text-data text-data-track-number"><?php echo $song->hasValueTrackNumber() ? $song->getTrackNumber() : "";?></td>
            <td class="text-data text-data-genre-name"><?php echo $song->hasValueGenre() ? $song->getGenre()->getName() : "";?></td>
            <td class="text-data text-data-artist-vocal-name"><?php echo $song->hasValueVocalist() ? $song->getVocalist()->getName() : "";?></td>
            <td class="text-data text-data-artist-composer-name"><?php echo $song->hasValueComposer() ? $song->getComposer()->getName() : "";?></td>
            <td class="text-data text-data-artist-arranger-name"><?php echo $song->hasValueArranger() ? $song->getArranger()->getName() : "";?></td>
            <td class="text-data text-data-duration"><?php echo $song->getDuration();?></td>
            <td class="text-data text-data-vocal"><?php echo $song->isVocal() ? 'Yes' : 'No';?></td>
            <td class="text-data text-data-subtitle-complete"><?php echo $song->issubtitleComplete() ? 'Yes' : 'No';?></td>
            <td class="text-data text-data-active"><?php echo $song->isActive() ? 'Yes' : 'No';?></td>
            </tr>
            <?php
            }
            ?>
            
        </tbody>
        </table>


        <div class="pagination">
        <div class="pagination-number">
        <?php
        foreach($rowData->getPagination() as $pg)
        {
            ?><span class="page-selector<?php echo $pg['selected'] ? ' page-selected':'';?>" data-page-number="<?php echo $pg['page'];?>"><a href="#"><?php echo $pg['page'];?></a></span><?php
        }
        ?>
        </div>
    </div>

    <?php
    }
}
catch(Exception $e)
{
    
}

```

Define method `createSongSpecification`. In this example, we use predicate (`PicoPredicate`) and and specification (`PicoSpecification`)

```php
<?php

namespace MusicProductionManager\Utility;

use MagicObject\Database\PicoPredicate;
use MagicObject\Database\PicoSpecification;
use MagicObject\Request\PicoRequestTool;


class SpecificationUtil
{
    /**
     * Create Song specification
     * @param PicoRequestTool $inputGet
     * @param array $additional
     * @return PicoSpecification
     */
    public static function createSongSpecification($inputGet, $additional = null)
    {
        $spesification = new PicoSpecification();

        if($inputGet->getSongId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('songId', $inputGet->getSongId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getGenreId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('genreId', $inputGet->getGenreId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getAlbumId() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('albumId', $inputGet->getAlbumId());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getName() != "")
        {
            $spesificationTitle = new PicoSpecification();
            $predicate1 = new PicoPredicate();
            $predicate1->like('name', PicoPredicate::generateCenterLike($inputGet->getName()));
            $spesificationTitle->addOr($predicate1);
            $predicate2 = new PicoPredicate();
            $predicate2->like('title', PicoPredicate::generateCenterLike($inputGet->getName()));
            $spesificationTitle->addOr($predicate2);
            $spesification->addAnd($spesificationTitle);
        }

        if($inputGet->getSubtitle() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->like('subtitle', PicoPredicate::generateCenterLike($inputGet->getSubtitle()));
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getArtistVocalist() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('artistVocalist', $inputGet->getArtistVocalist());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getsubtitleComplete() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('subtitleComplete', $inputGet->getsubtitleComplete());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getVocal() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('vocal', $inputGet->getVocal());
            $spesification->addAnd($predicate1);
        }

        if($inputGet->getActive() != "")
        {
            $predicate1 = new PicoPredicate();
            $predicate1->equals('active', $inputGet->getActive());
            $spesification->addAnd($predicate1);
        }

        if(isset($additional) && is_array($additional))
        {
            foreach($additional as $key=>$value)
            {
                $predicate2 = new PicoPredicate();          
                $predicate2->equals($key, $value);
                $spesification->addAnd($predicate2);
            }
        }
        
        return $spesification;
    }
}
```

## Database Query Builder

```php
<?php

use MagicObject\Database\PicoDatabaseQueryBuilder;

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
    
    $queryBuilder = new PicoDatabaseQueryBuilder($database);
    
    $queryBuilder
        ->newQuery()
        ->select("u.*")
        ->from("user")
        ->alias("u")
        ->where("u.username = ? and u.password = ? and u.active = ?", $username, $password, true)
        ;
    $stmt = $database->executeQuery($queryBuilder);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $user)
    {
        var_dump($user);
    }
    
}
catch(Ecxeption $e)
{
    
}
