<?php

use MagicObject\MagicObject;


require_once "vendor/autoload.php";

$magicOject = new MagicObject();
$magicOject->loadYamlString("
dinding:
  warna: merah
  bahan: tembok
pagar:
  bahan: besi
  warna: hitam
  gerbang: 
    sistem: otomatis
    tenaga: listrik
    tegangan: 220
    poalitas: AC
", false, true, true);

$magicOject->getPagar()->getGerbang()->setKendali("remote");

echo $magicOject->getPagar();