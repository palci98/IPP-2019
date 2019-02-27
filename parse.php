<?php
function Token($TokenKey)
{
    echo $TokenKey;
    return array(2,3);
}
function GetToken()
{
    $state = "default";
    $ch = "";
    $word = "";
    while(true)
    {
        $ch=fgetc(STDIN);
        switch($state)
        {
            default:
            case "default":
                if($ch == ".")
                {   
                    $word = $word.$ch;
                    $state = "header";
                    continue;
                }

                if($ch == "#")
                { 
                    $state="comment_line";
                    continue;
                }

                if($ch == "\r" || $ch == "\n") // ocekovat pred odovzdanim
                {
                    return(PHP_EOL);
                }

                if(ctype_alpha($ch))
                {
                    $word = $word . $ch;
                    $state="possible keywords";
                }
                
                else 
                    {
                        echo ord($ch);
                        Error(21,"Error wrong Header");
                    }
                break;
               
            case "possible keywords":
                    
                    if($ch== "@")
                    {
                        if($word=="bool")
                        {
                            $word="";
                            $state="bool";
                            continue;
                        }

                        if($word=="int")
                        {
                            $word="";
                            $state="int";
                            continue;
                        }
                        if($word=="string")
                        {
                            $word="";
                            $state="string";
                            continue;
                        }
                        if($word=="nil")
                        {
                            $word="";
                            $state="nil";
                            continue;
                        }
                    }
                    $word=$word.$ch;
                    break;
            case "nil":
                    $word=$word.$ch;
                    if($word=="nil")
                    {    
                        $ch=fgetc(STDIN);
                        if($ch==" "||$ch=="\t"|| $ch == PHP_EOL)
                        {
                        echo $word;
                        return $word;
                        }
                    
                    else
                    {
                        Error(21,"Jebe mi");
                    }
                    }
                    break;

            case "bool":
                    $word=$word.$ch;
                    if(strlen($word) == 4)
                    {
                        if($word=="true")
                        {
                            $ch=fgetc(STDIN);
                            echo ord($ch);
                            if($ch==" "||$ch=="\t"|| $ch == PHP_EOL)
                            {
                                return $word;
                            }    
                            else
                            {
                                Error(21,"Jebe mi");
                            }
                        }
                    }
                    if(strlen($word) == 5)
                    {
                        if($word=="false")
                        {
                            $ch=fgetc(STDIN);
                            echo ord($ch);
                            if($ch==" "||$ch=="\t"|| $ch == PHP_EOL)
                            {
                                echo $word;
                                return $word;
                            }    
                            else
                            {
                                Error(21,"Jebe mi");
                            }
                        }
                    }
                    break;
            
            case "int":
                    $word=$word.$ch;
                    if($ch=="+")
                    {
                        $state="+";
                    }
                    else if($ch=="-")
                    {
                        $state="-";
                    }
                    
                    else if(ctype_digit($ch))
                    {
                        $state="numberp";
                    }
                    break;

            case "numberp":
                    if($ch==PHP_EOL||$ch==" "||$ch=="\t")
                    {
                        echo $word;
                        return $word;
                    }
                    if(ctype_digit($ch))
                    {
                        $word=$word.$ch;
                    }
                    else 
                        {
                            Error(21,"zle cislo");
                        }    
                    break;

            case "+":
                    
                    if($ch==PHP_EOL||$ch==" "||$ch=="\t")
                    {
                        echo $word;
                        return $word;
                    }
                    if(ctype_digit($ch))
                    {
                        $word=$word.$ch;
                    }
                    if(!ctype_digit($ch))
                        {
                            Error(21,"zle cislo");
                        }
                    break;
                    
            case "-":

            if($ch==PHP_EOL||$ch==" "||$ch=="\t")
            {
                echo $word;
                return $word;
            }
            else if(ctype_digit($ch))
            {
                $word=$word.$ch;
            }
            else 
                {
                    Error(21,"zle cislo");
                }
            break;
            
            case "string":
                $word=$word.$ch;
                if($ch=="")

            

            break;
            case "header":

                $word = $word.$ch;
                if(strlen($word) == 11)
                {     
                    //echo $word;
                    if(strtoupper($word) == ".IPPCODE19\r") // 
                    {   
                        echo "zdravim";
                        Token($word);
                    }    
                    else 
                        Error(21,"Header error") ;
                }
                break;
            
            case "comment_line":
                if($ch==PHP_EOL)
                {
                    $state="default";
                    continue;
                }
                break;
            
        }
    }
}

function Error($ReturnValue,$text)
{
	fputs(STDERR,"$text\n");
	exit($ReturnValue);
}

GetToken();