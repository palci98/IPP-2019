<?php
include("scanner.php");

$tokens=array();
$gt=GetToken();

//echo $gt[0][0];
//var_dump($gt);
$dom = new DomDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
$xmlRoot = $dom->createElement("program");
$xmlRoot->setAttribute("language", "IPPcode19");
$xmlRoot = $dom->appendChild($xmlRoot);
$dom->save('sitemap.xml');

if(count($gt)>1||$gt[0][0]!=header)
{
    Error(10,"syntax hlavicka");
}

while(true)
{
    if($gt=GetToken());
    {
        if($gt==false)
        {
            exit(0);
        }
    echo $gt[0][0];

    }
}
