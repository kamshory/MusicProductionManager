<?php

function parseKeyValue($queryString)
{
    // Please test with https://regex101.com/
    
    // parse with quotes
    $re1 = '/([_\-\w+]+)\=\"([a-zA-Z0-9\-\+ _,.\(\)\{\}\`\~\!\@\#\$\%\^\*\\\|\<\>\[\]\/&%?=:;\t\'\r\n|\r|\n]+)\"/m';
    preg_match_all($re1, $queryString, $matches);
    $c1 = array_combine($matches[1], $matches[2]);
    
    // parse without quotes
    $re2 = '/([_\-\w+]+)\=([a-zA-Z0-9._]+)/m';
    preg_match_all($re2, $queryString, $matches);
    $c2 = array_combine($matches[1], $matches[2]);
    
    // merge result
    return array_merge($c1, $c2);
}

$queryString = '
data-column="Aa3`~!@#$%^&*()_+-={}[]:;\'<>,.?/|  wweewrwr\"
ergerggr" apa=aaa coba=ccc';

$ret = parseKeyValue($queryString);
echo json_encode($ret, JSON_PRETTY_PRINT);