<?php

$instructions=array("MOVE", "CREATEFRAME", "PUSHFRAME", "POPFRAME", "DEFVAR", "CALL",
                        "RETURN", "PUSHS", "POPS", "ADD", "SUB", "MUL", "IDIV", "LT", "GT",
                        "EQ", "AND", "OR", "NOT", "INT2CHAR", "STRI2INT", "READ", "WRITE",
                        "CONCAT", "STRLEN", "GETCHAR", "SETCHAR", "TYPE", "LABEL", "JUMP",
                        "JUMPIFEQ", "JUMPIFNEQ", "EXIT", "DPRINT", "BREAK");

const header = 10;
const variable = 11;
const constant = 2;
const instruction = 3;
const tokenEOF = 5;
function GetToken()
{
    // Specialne znaky, ktore sa mozu nachadzat na zaciatku premennej
    global $instructions;
    $specialchars=array("_","-","$","&","%","*","!","?");
    $result = array();
    $state = "default";
    $ch = "";
    $word = "";
    $count = 0;
    while(true)
    {
        $ch=fgetc(STDIN); // nacitanie postupne po znakoch
        switch($state)
        {
            default:
            case "default":
            if(ord($ch)==0)
            {
                return $result;
            }
                if($ch == ".")
                {
                    $word = $word.$ch;
                    $state = "header";
                    continue 2;
                }
                if($ch == "#")
                {
                    $state="comment_line";
                    continue 2;
                }

                if($ch == PHP_EOL) // ocekovat pred odovzdanim
                {
                    return($result);
                }

                if(ctype_alpha($ch))
                {
                    $word = $word . $ch;
                    $state="possible vars or types";
                    continue 2;
                }

                if(ctype_space($ch)||$ch=="\t")
                {
                    $state = "default";
                    continue 2;
                }
                else
                {
                    Error(21);
                }
                break;

            case "possible vars or types":
                    if($word == "GF@")
                    {
                      for($i=0;$i<count($specialchars);$i++)
                      {
                        if($ch==$specialchars[$i]||ctype_alpha($ch))
                        {
                          $word=$word.$ch;
                          $state="GF";
                          continue 3;
                        }
                      }
                      Error(21);
                    }

                    if($word == "LF@")
                    {
                      for($i=0;$i<count($specialchars);$i++)
                      {
                        if($ch==$specialchars[$i]||ctype_alpha($ch))
                        {
                          $word=$word.$ch;
                          $state="LF";
                          continue 3;
                        }
                      }
                      Error(21);
                    }

                    if($word == "TF@")
                    {
                      for($i=0;$i<count($specialchars);$i++)
                      {
                        if($ch==$specialchars[$i]||ctype_alpha($ch))
                        {
                          $word=$word.$ch;
                          $state="TF";
                          continue 3;
                        }
                      }
                      Error(21);
                    }

                    if($ch== "@")
                    {
                        if($word=="bool")
                        {
                            $word="";
                            $state="bool";
                            continue 2;
                        }
                        if($word=="int")
                        {
                            $word="";
                            $state="int";
                            continue 2;
                        }
                        if($word=="string")
                        {
                            $word="";
                            $state="string";
                            continue 2;
                        }
                        if($word=="nil")
                        {
                            $word="";
                            $state="nil";
                            continue 2;
                        }
                    }

                    else if ($ch == "\n" || $ch == "\r" || $ch == "\t" || ord($ch) == 0 || ctype_space($ch))
                    {
                      //$word=$word.$ch;
                      // Zbavime sa posledneho charakteru
                      
                      if(($ch == "\n" || $ch == "\r" || $ch == "\t" || ctype_space($ch)))
                      {
                      //$word = substr($word,0,strlen($word)-1);
                      $state="Instruction";
                      continue 2;
                      }
                      else
                      {
                          $state = "Instruction";
                          continue 2;
                      }
                    }
                    
                    $word=$word.$ch;


                    break;
            case "Instruction":
            //echo $word;
                for($i=0;$i<count($instructions);$i++)
                {
                  //  echo "toto je word: $word\n";
                    if(strtoupper($word) == strtoupper($instructions[$i]))
                    {
                    //    echo "kokot";
                      //echo "$word";
                      array_push($result,$word);
                      $state = "default";
                      $word = $ch;
                      continue 3;
                  }
                }
                Error(22);
                    break;
            case "GF":
                    if($ch==" "||$ch=="\t"|| $ch == PHP_EOL||ord($ch)==0)
                    {
                        array_push($result,array($word));
                        //return $result;
                        $word="";
                        if(ord($ch)== 0)
                        {
                            return $result;
                        }
                        $state="default";
                        continue 2;
                    }
                    $word=$word.$ch;
            break;

            case "LF":
                    if($ch==" "||$ch=="\t"|| $ch == PHP_EOL||ord($ch)==0)
                    {
                      array_push($result,array($word,variable));
                      $word="";
                      if(ord($ch)== 0)
                      {
                          return $result;
                      }
                      $state="default";
                      continue 2;
                    }
                    $word=$word.$ch;
            break;

            case "TF":
                    
                    if($ch==" "||$ch=="\t"|| $ch == PHP_EOL)
                    {
                      array_push($result,array($word,variable));
                      //return $result;
                      $word="";
                      $state="default";
                      continue 2;
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
                          array_push($result,array($word,constant));
                          return $result;
                        }

                    else
                    {
                        Error(21);
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
                            if($ch==" "||$ch=="\t"|| $ch == PHP_EOL)
                            {
                              array_push($result,array($word,constant));
                              return $result;
                            }
                            else
                            {
                                Error(21);
                            }
                        }
                    }
                    if(strlen($word) == 5)
                    {
                        if($word=="false")
                        {
                            $ch=fgetc(STDIN);
                            if($ch==" "||$ch=="\t"|| $ch == PHP_EOL)
                            {
                              array_push($result,array($word,constant));
                              return $result;
                            }
                            else
                            {
                                Error(21);
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
                        array_push($result,array($word,constant));
                        return $result;
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
                      array_push($result,array($word,constant));
                      return $result;
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
                array_push($result,array($word,constant));
                return $result;
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
                if($ch == " ")
                {
                  array_push($result,array($word,constant));
                  return $result;
                }
                if($ch == PHP_EOL)
                {
                  array_push($result,array($word,constant));
                  return $result;
                }
                if($ch == "#")
                {
                  array_push($result,array($word,constant));
                  return $result;
                }
// DORIESIT ESCAPE SEKVENCI!!!!!!!!!!!!!!!!!!!!!!!!!!!!1
/*               if($ch == "\\")
                {
                    $state="esc";
                    return $word;
                }
                $word=$word.$ch;
            break;
            case "esc":
                if($word==)
                $word=$word.$ch;
*/
            case "header":
                if(strlen($word) == 10)
                {
                    if(strtoupper($word) == ".IPPCODE19") //
                    {
                        array_push($result,array(header));
                        $word="";
                        $state ="default";
                        continue 2;
                    }
                    else
                        Error(21);
                }
                if(ord($ch)==0)
                {
                    Error(21);
                }
                $word = $word.$ch;
                break;

            case "comment_line":
                if($ch==PHP_EOL||ord($ch)==0)
                {
                    return $result;
                }
                break;
        }

    }

}
function Error($ReturnValue)
{
	exit($ReturnValue);
}
/*
$gt=array();
for($i=0;$i<10;$i++){
$gt=GetToken();
var_dump($gt);
}

*/