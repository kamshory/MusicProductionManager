<?php

$name = "";

if($argc > 1)
{
    $name = trim($argv[1]);
}
else
{
    if(isset($_GET['name']))
    {
        $name = trim($_GET['name']);
    }
}

$file = __DIR__."/$name.php";


$notes = explode(" ", "C Db D Eb E F Gb G Ab A Bb B");
if(file_exists($file))
{
    $content = file_get_contents($file);
    foreach($notes as $note)
    {
        for($i = 0; $i<10; $i++)
        {
            $code = $note.$i;
            $content = str_replace($code, '"'.$code.'"', $content);
        }
    }
    file_put_contents($file, $content);
}