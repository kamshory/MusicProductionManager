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
$secret1->loadYamlString(
$yaml1,
false, true, true
);

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
});$secret2->loadYamlString($yaml2, false, true, true);

echo $secret2->dumpYaml(null, 4);