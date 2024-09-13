<?php

use MusicProductionManager\Data\Entity\EntitySong;

require_once "inc/app.php";

$songEntity = new EntitySong(null, $database);
$rowData = $songEntity->findAll();

?>

<pre>
<?php
echo $rowData->getResult()[0];
?>
</pre>
