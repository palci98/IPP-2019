<?php
include("scanner.php");
global $instructions;
$tokens=array();
$gt=GetToken();
$count=0;

// Kontrola headeru
if(count($gt) == 2)
{
  if ($gt[0][0] != header)
  {
    echo "Header error.\n";
    Error(10);
  }
  if (!($gt[1] == tokenEOF || $gt[1] == tokenEOL))
  {
    echo "Header error.\n";
    Error(10);
  }
}


$dom = new DomDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
$xmlRoot = $dom->createElement("program");
$xmlRoot->setAttribute("language", "IPPcode19");
$xmlRoot = $dom->appendChild($xmlRoot);


$orderCounter = 1;

while(true)
{
    $gt = GetToken();

    // Skipni prazdne riadky
    if ($gt[0] == tokenEOL)
    {
      continue;
    }

    // Koniec suboru
    if ($gt[0] == tokenEOF)
    {
        echo "Koniec suboru\n";
        break;
    }

    // Ak to nie je instrukcia tak je chyba
    if ($gt[0][0] != instruction)
    {
      Error(22);
    }

    $instruction = $dom->createElement("instruction");
    $instruction->setAttribute("order",$orderCounter++);
    $instruction->setAttribute("opcode",strtoupper($gt[0][1]));
    $xmlRoot->appendChild($instruction);

    // Get the instruction
    switch(strtoupper($gt[0][1]))
    {
        default:
        case "default":
          echo "Default state reached\n";
          Error(23);
        break;

        //
        case "CREATEFRAME":
        case "PUSHFRAME":
        case "POPFRAME":
        case "RETURN":
        case "BREAK":

          if (count($gt) != 2)
          {
            Error(23);
          }

          if (!($gt[1] == tokenEOL || $gt[1] == tokenEOF))
          {
            Error(23);
          }

        break;

        // LABEL
        case "LABEL":
        case "JUMP":
        case "CALL":

          if (count($gt) != 3)
          {
            Error(23);
          }

          if ($gt[1][0] == instruction)
          {
            $gt[1][0] = label;
          }

          if ($gt[1][0] != label)
          {
            Error(23);
          }

          if (!($gt[2] == tokenEOL || $gt[2] == tokenEOF))
          {
            Error(23);
          }

          $arg = $dom->createElement("arg1",$gt[1][1]);
          $arg->setAttribute("type","label");
          $instruction->appendChild($arg);
        break;

        // VAR
        case "DEFVAR":
        case "POPS":
          if (count($gt) != 3)
          {
            Error(23);
          }

          if ($gt[1][0] != variable)
          {
            Error(23);
          }

          if (!($gt[2] == tokenEOL || $gt[2] == tokenEOF))
          {
            Error(23);
          }

          $arg = $dom->createElement("arg1",$gt[1][1]);
          $arg->setAttribute("type","var");
          $instruction->appendChild($arg);
        break;

        // SYMB
        case "DPRINT":
        case "PUSHS":
        case "WRITE":
        case "EXIT":

          if (count($gt) != 3)
          {
            Error(23);
          }

          if (!($gt[2] == tokenEOL || $gt[2] == tokenEOF))
          {
            Error(23);
          }

          $arg = $dom->createElement("arg1",$gt[1][1]);

          if ($gt[1][0] == constant_string)
          {
            $arg->setAttribute("type","string");
          }
          else if ($gt[1][0] == constant_int)
          {
            $arg->setAttribute("type","int");
          }
          else if ($gt[1][0] == constant_nil)
          {
            $arg->setAttribute("type","nil");
          }
          else if ($gt[1][0] == constant_bool)
          {
            $arg->setAttribute("type","bool");
          }
          else if ($gt[1][0] == variable)
          {
            $arg->setAttribute("type","var");
          }
          else
          {
            Error(23);
          }
          $instruction->appendChild($arg);

        break;

        // VAR SYMB
        case "MOVE":
        case "INT2CHAR":
        case "STRLEN":
        case "NOT":
        case "TYPE":

              if (count($gt) != 4)
              {
                Error(23);
              }

              if (!($gt[3] == tokenEOL || $gt[3] == tokenEOF))
              {
                Error(23);
              }

              if ($gt[1][0] != variable)
              {
                Error(23);
              }

              $arg = $dom->createElement("arg1",$gt[1][1]);
              $arg->setAttribute("type","var");
              $instruction->appendChild($arg);

              $arg = $dom->createElement("arg2",$gt[2][1]);

              if ($gt[2][0] == constant_string)
              {
                $arg->setAttribute("type","string");
              }
              else if ($gt[2][0] == constant_int)
              {
                $arg->setAttribute("type","int");
              }
              else if ($gt[2][0] == constant_nil)
              {
                $arg->setAttribute("type","nil");
              }
              else if ($gt[2][0] == constant_bool)
              {
                $arg->setAttribute("type","bool");
              }
              else if ($gt[2][0] == variable)
              {
                $arg->setAttribute("type","var");
              }
              else
              {
                Error(23);
              }
              $instruction->appendChild($arg);
        break;

        // VAR TYPE
        case "READ":

              if (count($gt) != 4)
              {
                Error(23);
              }

              if (!($gt[3] == tokenEOL || $gt[3] == tokenEOF))
              {
                Error(23);
              }

              if ($gt[1][0] != variable)
              {
                Error(23);
              }

              if ($gt[2][0] != type)
              {
                Error(23);
              }
              $arg = $dom->createElement("arg1",$gt[1][1]);
              $arg->setAttribute("type","var");
              $instruction->appendChild($arg);

              $arg = $dom->createElement("arg2",$gt[2][1]);
              $arg->setAttribute("type","type");
              $instruction->appendChild($arg);

        break;

        // VAR SYMB SYMB
        case "ADD":
        case "SUB":
        case "MUL":
        case "IDIV":
        case "AND":
        case "OR":
        case "GT":
        case "LT":
        case "EQ":
        case "STRI2INT":
        case "CONCAT":
        case "GETCHAR":
        case "SETCHAR":

                if (count($gt) != 5)
                {
                  Error(23);
                }

                if (!($gt[4] == tokenEOL || $gt[4] == tokenEOF))
                {
                  Error(23);
                }

                if ($gt[1][0] != variable)
                {
                  Error(23);
                }

                $arg = $dom->createElement("arg1",$gt[1][1]);
                $arg->setAttribute("type","var");
                $instruction->appendChild($arg);

                $arg = $dom->createElement("arg2",$gt[2][1]);

                if ($gt[2][0] == constant_string)
                {
                  $arg->setAttribute("type","string");
                }
                else if ($gt[2][0] == constant_int)
                {
                  $arg->setAttribute("type","int");
                }
                else if ($gt[2][0] == constant_nil)
                {
                  $arg->setAttribute("type","nil");
                }
                else if ($gt[2][0] == constant_bool)
                {
                  $arg->setAttribute("type","bool");
                }
                else if ($gt[2][0] == variable)
                {
                  $arg->setAttribute("type","var");
                }
                else
                {
                  Error(23);
                }
                $instruction->appendChild($arg);

                $arg = $dom->createElement("arg3",$gt[3][1]);

                if ($gt[3][0] == constant_string)
                {
                  $arg->setAttribute("type","string");
                }
                else if ($gt[3][0] == constant_int)
                {
                  $arg->setAttribute("type","int");
                }
                else if ($gt[3][0] == constant_nil)
                {
                  $arg->setAttribute("type","nil");
                }
                else if ($gt[3][0] == constant_bool)
                {
                  $arg->setAttribute("type","bool");
                }
                else if ($gt[3][0] == variable)
                {
                  $arg->setAttribute("type","var");
                }
                else
                {
                  Error(23);
                }
                $instruction->appendChild($arg);
        break;

        // LABEL SYMB SYMB
        case "JUMPIFEQ":
        case "JUMPIFNEQ":

              if (count($gt) != 5)
              {
                Error(23);
              }

              if (!($gt[4] == tokenEOL || $gt[4] == tokenEOF))
              {
                Error(23);
              }

              if ($gt[1][0] == instruction)
              {
                $gt[1][0] = label;
              }
              if ($gt[1][0] != label)
              {
                Error(23);
              }

              $arg = $dom->createElement("arg1",$gt[1][1]);
              $arg->setAttribute("type","var");
              $instruction->appendChild($arg);

              $arg = $dom->createElement("arg2",$gt[2][1]);

              if ($gt[2][0] == constant_string)
              {
                $arg->setAttribute("type","string");
              }
              else if ($gt[2][0] == constant_int)
              {
                $arg->setAttribute("type","int");
              }
              else if ($gt[2][0] == constant_nil)
              {
                $arg->setAttribute("type","nil");
              }
              else if ($gt[2][0] == constant_bool)
              {
                $arg->setAttribute("type","bool");
              }
              else if ($gt[2][0] == variable)
              {
                $arg->setAttribute("type","var");
              }
              else
              {
                Error(23);
              }
              $instruction->appendChild($arg);

              $arg = $dom->createElement("arg3",$gt[3][1]);

              if ($gt[3][0] == constant_string)
              {
                $arg->setAttribute("type","string");
              }
              else if ($gt[3][0] == constant_int)
              {
                $arg->setAttribute("type","int");
              }
              else if ($gt[3][0] == constant_nil)
              {
                $arg->setAttribute("type","nil");
              }
              else if ($gt[3][0] == constant_bool)
              {
                $arg->setAttribute("type","bool");
              }
              else if ($gt[3][0] == variable)
              {
                $arg->setAttribute("type","var");
              }
              else
              {
                Error(23);
              }
              $instruction->appendChild($arg);

        break;
    }
}

$dom->save('sitemap.xml');
