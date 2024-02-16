<?php

use WS\PicoWebSocket\WSSession;

require_once "vendor/autoload.php";

$sessions = array(
    'username'=>'kamshory',
    'password'=>'rahasia'
);
$encoded = WSSession::serialize($sessions);
echo $encoded."\r\n";

$decoded1 = WSSession::unserialize($encoded, "php");
print_r($decoded1);
echo "\r\n";

$decoded2 = WSSession::unserialize($encoded, "php_binary");
print_r($decoded2);
echo "\r\n";



$sessions3 = array(
    "key1"=>'value 1 iuwefiu wf ijwfei woif iewf ihwefiu hwefihwiuehf iuwe hfiuwehfiuhweiuf hwie hfiuweh fiuewhf uwehfiuhewiufhweiufh uiwe hgiwe jgijoiwjfoiwejg ijg oijwg oijwig jwoiejg owjegoiwjeg woigjwoiegjiowej goijweig wiegj oiwjegi jweoijg oiewjgoijweg jowej gwiejgoiwjeoigjweoigj oiwejgwi eiw geiwj geoijweig joiwejgoiwje goijweoig woiejg oijwgoigjewoig joiwejg oiwj geoiwjeoigjwe oigjoiwegoi ewjgoi jweoigjewoi jgoiwejgoi',
    "key2"=>2,
    "key3"=>1.3,
    "key4"=>true,
    "key5"=>false,
    "key6"=>null
);

$encoded3 = WSSession::serializePhpBinary($sessions3);
$decoded3 = WSSession::unserialize($encoded3, "php_binary");
var_dump($decoded3);
echo json_encode($decoded3);
echo "\r\n\r\n";
echo json_encode($encoded3);
echo "\r\n";
