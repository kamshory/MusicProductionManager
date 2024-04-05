<?php

$files = glob("inc.lib/classes/MusicProductionManager/Data/Entity/*.php");
function getLast($haystack, $needle)
{
    $lastFound = false;
    $found = false;
    $offset = 0;
    do
    {
        $lastFound = strpos($haystack, $needle, $offset);
        if($lastFound !== false)
        {
            $found = $lastFound;
            $offset = $lastFound + 1;
        }
    }
    while($lastFound !== false);
    return $found;
}

function updateBody($body)
{
    $arr = explode("\r\n", $body);
    foreach($arr as $key=>$line)
    {
        $arr[$key] = rtrim($line);
        if(substr($arr[$key], strlen($arr[$key]) - 1) == "*")
        {
            $arr[$key] = $arr[$key]." ";
        }
    }
    $body = implode("\r\n", $arr);
    
    $arr = explode("\r\n\r\n", $body);
    foreach($arr as $key=>$block)
    {
        $arr[$key] = updateBlock($block);
    }
    return implode("\r\n\r\n", $arr);
}
function updateBlock($block)
{
    $arr = explode("\r\n", $block);
    $lineVar = "";
    $firstLine = "";
    foreach($arr as $line)
    {
        if(stripos($line, "@var ") !== false)
        {
            $lineVar = $line;
        }
        $line2 = $line;
        if($firstLine == "" && trim(str_replace(array('/', '*'), '', $line2)) != "")
        {
            
            $firstLine = trim(str_replace(array('/', '*'), '', $line2));
        }
    }
    if($lineVar != "" && $firstLine != "")
    {
        $trailer = substr($lineVar, 0, strpos($lineVar, "@"));
        
        $replacer = $trailer."@Label(content=\"$firstLine\")";
        $block = str_replace($lineVar, $replacer."\r\n".$lineVar, $block);
    }
    return $block;
}
foreach($files as $file)
{
    $content = file_get_contents($file);
    $start = getLast($content, "{") + 1;
    $end = getLast($content, "}");
    $body = substr($content, $start, $end-$start);
    $header = substr($content, 0, $start);
    $footer = substr($content, $end);
    
    
    $updatedBody = updateBody($body);
    
    file_put_contents($file, $header.$updatedBody.$footer);
}
