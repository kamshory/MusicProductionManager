## Secret Object

### Definition

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

### Class Parameters

**@JSON**

`@JSON` is parameter to inform how the object will be serialized.

Attributes:
`property-naming-strategy`

Available value:

- `SNAKE_CASE` all column will be snace case when `__toString()` or `dumpYaml()` method called.
- `CAMEL_CASE` all column will be camel case when `__toString()` or `dumpYaml()` method called.

### Property Parameters

1. `@EncryptIn` annotation will encrypt the value before it is assigned to the associated property with the `set` method. 
2. `@DecryptIn` annotation will decrypt the value before it is assigned to the associated property with the `set` method. 
3. `@EncryptOut` annotation will encrypt the property when application call `get` method. 
4. `@DecryptOut` annotation will decrypt the property when application call `get` method. 

### Create Secure Config

When creating a secure application configuration, users can simply use the `@EncryptOut` annotation. MagicObject will load the configuration as entered but will encrypt it when dumped to a file. For configurations that will not be encrypted, do not use `@EncryptIn`, `@DecryptIn`, `@EncryptOut`, or `@DecryptOut`. 

```php
<?php

namespace MagicObject\Database;

use MagicObject\SecretObject;

class SecretGenerator extends SecretObject
{
	/**
	 * Database driver
	 *
	 * @var string
	 */
	protected $driver;

	/**
	 * Database server host
	 *
	 * @EncryptOut
	 * @var string
	 */
	protected $host;

	/**
	 * Database server port
	 * @var integer
	 */
	protected $port;

	/**
	 * Database username
	 *
	 * @EncryptOut
	 * @var string
	 */
	protected $username;

	/**
	 * Database user password
	 *
	 * @EncryptOut
	 * @var string
	 */
	protected $password;

	/**
	 * Database name
	 *
	 * @EncryptOut
	 * @var string
	 */
	protected $databaseName;

	/**
	 * Database schema
	 *
	 * @EncryptOut
	 * @var string
	 */
	protected $databseSchema;

	/**
	 * Application time zone
	 *
	 * @var string
	 */
	protected $timeZone;
}
```

```php

$yaml = "  
time_zone_system: Asia/Jakarta
default_charset: utf8
driver: mysql
host: localhost
port: 3306
username: root
password: password
database_name: music
database_schema: public
time_zone: Asia/Jakarta
salt: GaramDapur
";

$config = new MagicObject();
$config->loadYamlString($yaml);
$generator = new SecretGenerator($config);

echo $generator; // will print JSON

$secretYaml = $generator->dumpYaml(null, 4, 0); // will print secret yaml

file_put_content("secret.yaml", $secretYaml); // will dump to file secret.yaml
```

Do not use standard encryption keys when creating or using SecretObjects. Always use your own lock. The encryption key must be generated using a callback function. Do not enter it as an object property or constant.

```php

$yaml = "  
time_zone_system: Asia/Jakarta
default_charset: utf8
driver: mysql
host: localhost
port: 3306
username: root
password: password
database_name: music
database_schema: public
time_zone: Asia/Jakarta
salt: GaramDapur
";

$config = new MagicObject();
$config->loadYamlString($yaml, false, true, true);
$generator = new SecretGenerator($config, function(){
	// define your own key here
	return "6619f3e7a1a9f0e75838d41ff368f72868e656b251e67e8358bef8483ab0d51c";
});

echo $generator; // will print JSON

$secretYaml = $generator->dumpYaml(null, 4, 0); // will print secret yaml

file_put_content("secret.yaml", $secretYaml); // will dump to file secret.yaml
```

or you can also call another function. 

```php

function getSecure()
{
	return "6619f3e7a1a9f0e75838d41ff368f72868e656b251e67e8358bef8483ab0d51c";
}

$yaml = "  
time_zone_system: Asia/Jakarta
default_charset: utf8
driver: mysql
host: localhost
port: 3306
username: root
password: password
database_name: music
database_schema: public
time_zone: Asia/Jakarta
salt: GaramDapur
";

$config = new MagicObject();
$config->loadYamlString($yaml, false, true, true);
$generator = new SecretGenerator($config, function(){
	// define your own key here
	return getSecure();
});

echo $generator; // will print JSON

$secretYaml = $generator->dumpYaml(null, 4, 0); // will print secret yaml

file_put_content("secret.yaml", $secretYaml); // will dump to file secret.yaml
```

### Implement Secure Config

Application configuration is usually written in a file or environment variable after being encrypted. This configuration cannot be read by anyone without decrypting it first. MagicObject will retrieve the encrypted value. If a user accidentally dumps an object using `var_dump` or `print_r`, then PHP will only display the encrypted value. When PHP makes a connection to the database using a credential, MagicObject will decrypt it but the value will not be stored in the object's properties.

Thus, to create an application configuration, it is enough to use the `@DecryptOut` annotation. Thus, MagicObject will only decrypt the configuration when it is ready to be used.

**Example 1**

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

### Multilevel Object Secure

MagicObject also support Multilevel Yaml.

**Example**

```php
<?php

use MagicObject\SecretObject;

require_once dirname(__DIR__) . "/vendor/autoload.php";

/**
 * @JSON(property-naming-strategy=SNAKE_CASE)
 */
class ConfigSecret1 extends SecretObject
{
    /**
     * Database
     * 
     * @EncryptOut
     * @var mixed
     */
    protected $database;

    /**
     * Session
     * 
     * @EncryptOut
     * @var mixed
     */
    protected $session;
}

/**
 * @JSON(property-naming-strategy=SNAKE_CASE)
 */
class ConfigSecret2 extends SecretObject
{
    /**
     * Database
     * 
     * @DecryptOut
     * @var mixed
     */
    protected $database;

    /**
     * Session
     * 
     * @DecryptOut
     * @var mixed
     */
    protected $session;
}

// only database and session will be encrypted

$secret1 = new ConfigSecret1(null, function(){
    return bin2hex("This is your secure key for Scrt");
});

$yaml1 = "
result_per_page: 20
song_base_url: http//domain.tld/songs
song_base_path: /var/www/songs
song_draft_base_url: http//domain.tld/songs-draft
song_draft_base_path: /var/www/songs-draft
proxy_provider: cloudflare
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
  driver: mysql
  host: localhost
  port: 3306
  username: user
  password: pass
  database_name: music
  database_schema: public
  time_zone: Asia/Jakarta
  salt: Asia/Jakarta
session:
  name: MUSICPRODUCTIONMANAGER
  max_life_time: 86400
  save_handler: files
  save_path: /tmp/session
vocal_guide_instrument: piano
";
$secret1->loadYamlString($yaml1, false, true, true);

echo $secret1->dumpYaml(null, 4);

$yaml2 = "
database:
    time_zone_system: 9jAU0W/ja1cGjgUJwMfZJbnuSz/ZokaYLdMeOItAKeXFAKTKyjIr2HLkxh4RXgkzER4Cbx9HdI2WFqGbHfT/IHnUEQP9RslMVDKOuw2hIEpt9+QIJXTeZSRifgJGLCyZI0HasjndiTqx6OBWQbs0KuKckLCRNllSQ/4lDj1p78pAvul/yoEhwU4DOjknlSot      
    default_charset: K5SqhKUKYBWuYQa1SzexG0iUnnQAAXlKKAugab5OdsqZgUA4hNzcb7sguARQc8nuk8MzQCzI9l2PUkJleUk0zPdDf07fyRW4QfsLdUmq39sTEgqjNpyTpL9KC3bZ8dDrs0ClAZrTV5nP+S5eI4Cm2wqpIwEnifIWlNkwWFaXuLdmRGvcaOiZ0pvg01jsTjgi       
    driver: cXWyWFxUhVvxlGZjvbz/VKaYg9mXcMc2foL99dbXJ9D+5znFaQb2uQBc0MR8ot6MyK7w6mqkYSyveSTKCOTugdaromJwe33NrX5Syk/kLx4baOTgMNtp+yzc4xf8u8DgaaCL6h/wPiiyagFCHrdXHmLMq6vyNsDMJAQ1pUhFw7fjRCNWxrT1wWCyCxHgL41b
    host: wKfsRPZtBdbQvP5eybGxbcZwajSeUzmg3o8eMoFtT8bgdhQCsqnsq2LtTrdsU8fngje8uCcUPianj3KB15CfBS0bVssOCyrqstyvWKz9HGGwBVFq7CtPVkrycDfPLy4Lmy5cBUESmzJ6oZaF4U09rAyWp9eNzMARQMl/SlCGVWICC9S4PQbRGNR525Jdjrkm
    port: TqQDnEKT+iaYHrp7hWP5/J9H42yolh8hyUrcn5r6Pak9rIKPWHLB2f0n1jBTFPAuTaim/LC2An02BM2HOWkRHb8yk2fRs8l6aLPl5MkgjPJ2WrX/nTgj5UnqckXM0QI54f8jl3vKRgKX2Mo1kSh51tlAHtHEAej7jAzB3MqoLYD/cWlLqWNszgMcluSXhNqg
    username: Zq1ceoiG57Bm7sJUIwWwggKmO69Dj6YYPVMSsx6cr/tWST4TQ60b9Ukmpnk5vmBxClK3Luz1FnQk6CUZfU1EkJx6Z0xvyOcDM8avnHm139Isw0KR5RrHnMFGA7XzlYg0sEOXc1ww6/TljZoB7VrPbIxOo7tUnfu3SKyrxq73iut2Ggvfqeg3WLvaUexcbsfx
    password: GSrwvtctYPV6kWQUPa0096TT6dVizB1rtc+DS816LqSJdd/8LnGLsrVU5y7wYP8wWjbszrNbiO3RrS8Yi4jGiQ3nVGYfMm6MxypqBS1HVyELscyIOWNdb3XG4vUnUc4hBGHBvpTBn3A39kV+oUbqjV5RX//hrwVSyUNs87W4YR5jRJh78c3Dcopxa8LPibxv
    database_name: piiCRCs33WNxN343DMMA6BLnKT54XjAB5HSX5TrRDZhwa5QM0jcnURrfRfaZ+h3zSiB97BjTLG5Ij5sKI4+vSfnpiRjmykYuZppKY1XxVbSP97SGAV4+WRLH5b/OLwGN5FpVQKBHJJW/hJb53AU5g3Ie0Wwy68Uk0CpIU1VQogUtbYmh8Ml+e6b676SNYxKr
    database_schema: g9U9chCj4HItkdJ7YrJJgLiiXk5QHcxQ/a7cRDNxJ1M3Z9hYsBKj4EM4fzdnmDkGrjdNyahBRlQ5pn771zAkzrUI06h0nb4sZiEmXEK8Ks+QqSJIc3SzKMXpRAlDUxiOqQmDGyy9lkTG7oQU8iZiQizVzXU/Yv5tSzTt0R9RX4nSddSU/tWJMWHw+S/SNyPE       
    time_zone: JlTEF/VnijNTFGHtW3coKPGV5/3lEGLMQfUNREkGRiVzOLk9n7hXyFI4bYTapjaAC614GPBsg+JysmYHcP+rb+ZczC8cwP12oKPaNa7wp2yd6iXW1GbcjXV4c7W4po4acI9hwcJkup9alHSymI61TgHyQkyO8whGd6TuTRZjXUtbisx9wNeX2/soTRAWixp1
    salt: u2dXCErgGfZHCUaRFCgxwJAPeOXAiLX5pB7yzjfmDDjIWSHX7FzDZ8TJPzBcSxl8I9t60/MKG36BTrQq+x7Nc7TKNo1vZw+Q9TCENxi9CVuEWr6A5+JEkIEpsywmHlNvNZbc8RFcUtKdh3ikAUS98K4sh9sZJdy6Ps89QhepQtulanvQ+OWI3RLa2vc/B8A+
session:
    name: iOZiWwizvDSAeaUoVxaLlGrlYyY6X9GHgxDKi85CB0Wq068jsx3TKkmTL92OWIctwoCbJaz7a3sGLvNi48SxqA9Lz3C4g1sWx3ZTBK6nzPt+7Jq91H6cYlragNCRdRwTdWrIaj2e75qT1Al71y09C0jXre9B+LH4sqdIdlBeHUHdopf+P+2meIFZ1MecPnjdTyVv6432uY67eO2KM/0dgw==
    max_life_time: b8gV+XITyZDg8MAuQyY3pR8oRynBwI8HeEyCJs3gCT3W92W6yf365fvO+ToFr1igr8atrj9a3n456zeVwoA8vWHtTghsHNfnZhZwsd3uB9E9kaIU6HJejKGsqKIrGaORucMMYFs/N0Uv4b+nrUTCsvuKEzPK1AGT9YcZw3Vfl3r7mdV3//1sVrc/mFmKBvfD
    save_handler: 4jsY+ExJo9VlEalJ7niSv5TZjF0qmoHlbYGQ9wyqI5fpbYXE9lWj865vV4gpk7dWRVZJOkD6Sqq0F36/QnjLr6ud1rdOlm8nwClT+YPalJ+nLygoYw8ZV+gwKJoSai1UapYDEBNS0OmyPQd69nj9BypDSnLLxUyDw8o/0vdw/MNLqcLNcGKcrZtjT6qS+1aG
    save_path: 1VQ4zmPuAMxVmjnGiLfaseBZdovfINp6kLa9VokR+n18BUiMMVppPN5oky4+ROwt1Jx5c2Y7+NjCmXA5KJZkewrFp1YPszEM/JqSBygBPsOKY/QH08fZfdxugDkiHuxPdXtZVQC40sHu+BTHUlexVee87MACjvpm+cQDgAwfVwLlW3KlYxEdcX6sPP4MTWeM
result_per_page: 20
song_base_url: http//domain.tld/songs
song_base_path: /var/www/songs
song_draft_base_url: http//domain.tld/songs-draft
song_draft_base_path: /var/www/songs-draft
proxy_provider: cloudflare
app_name: 'Music Production Manager'
user_image:
    width: 512
    height: 512
album_image:
    width: 512
    height: 512
song_image:
    width: 512
    height: 512
vocal_guide_instrument: piano
";

$secret2 = new ConfigSecret2(null, function(){
    return bin2hex("This is your secure key for Scrt");
});

$secret2->loadYamlString($yaml2, false, true, true);

echo $secret2->dumpYaml(null, 4);
```

### Secure Config from DynamicObject

```php
<?php

use MagicObject\Database\PicoDatabase;
use MagicObject\Database\PicoDatabaseCredentials;
use MusicProductionManager\App\ShutdownManager;
use MusicProductionManager\Config\ConfigApp;
use MagicObject\SecretObject;
namespace MagicObject\Database;

require_once __DIR__ . "/vendor/autoload.php";

/**
 * PicoDatabaseCredentials class
 * The SecretObject will encrypt all attributes to prevent unauthorized user read the database configuration
 */
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

$cfg = new ConfigApp(null, true);
$cfg->loadYamlFile(dirname(__DIR__)."/.cfg/app.yml", true, true);

$databaseCredentials = new PicoDatabaseCredentials($cfg->getDatabase());

$database = new PicoDatabase($databaseCredentials, 
    function($sql, $type) //NOSONAR
    {
        // callback when execute query that modify data
    }, 
    function($sql) //NOSONAR
    {
        // callback when execute all query
    }
);

try
{
    $database->connect();
    $shutdownManager = new ShutdownManager($database);
    $shutdownManager->registerShutdown();
}
catch(Exception $e)
{
    // do nothing
}

```