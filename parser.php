<?php
include("scanner.php");
global $instructions;
$tokens=array();
$gt=GetToken();
$count=0;

//echo $gt[0][0];
var_dump($gt);
//echo count($gt);
exit(0);
if(count($gt)>1||$gt[0][0]!=header)
{
    Error(10);
}
unset($gt);
$gt=array();


$dom = new DomDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
$xmlRoot = $dom->createElement("program");
$xmlRoot->setAttribute("language", "IPPcode19");
$xmlRoot = $dom->appendChild($xmlRoot);
$dom->save('sitemap.xml');
$state="default";           
while(true)
{   
    var_dump($gt);    
    var_dump($gt);
    exit(0);
/*
    switch($state)
    {
        default:
        case "default":
            if($gt=GetToken())
            {   
                //echo $gt[0];
                var_dump($gt);
                $count=$count+1;
                for($i=0;$i<count($instructions);$i++)
                {
                    if($gt[0]==tokenEOF||$gt[1]==tokenEOF)
                    {
                        exit (0);
                    }
                    if($gt[0]==$instructions[$i]);
                    {
                        //var_dump($count);
                        //echo "ahoj";
                        $state="default";
                    }
                }
            }
            else
            {
                exit(0);
            }
        
        break;
        case "tv":

    }*/
}