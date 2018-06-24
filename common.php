<?php

 error_reporting(E_ALL ^ E_NOTICE);	// disregard notice errors
 $colour_index[0] = 0;		// make a global array
        // this colours palette is equal to mirc colours palette (apparently!!)
 include("config.php");
 include("strings." . LANGUAGE . ".php");
 include("convdb.php");

 if (isset($_COOKIE["webconversuser"])) {
  $user = $_COOKIE["webconversuser"];
  $jsflag = $_COOKIE["webconversjsflag"];
 }


function conv_colour_decode($text)
 {
    static $next_colour = 0, $this_colour = 0;
    global $colour_index;

    $colour[0] ="#00007f";                   // blue
    $colour[1] ="#7f0000";                   // light red
    $colour[2] ="#9f009f";                   // magenta
    $colour[3] ="#ff7f00";                   // orange
    $colour[4] ="#ff6464";                   // yellow(ish)
    $colour[5] ="#00ff00";                   // light green (lime)
    $colour[6] ="#006464";                   // cyan
    $colour[7] ="#00ffff";                   // light cyan (aqua)
    $colour[8] ="#0000ff";                   // light blue
    $colour[9] ="#c200c2";                   // light magenta (pink)
    $colour[10]="#7a7a7a";                   // grey
    $colour[11]="#a6a6a6";                   // light grey (silver)
    $colour[12]="#000000";                   // black
    $colour[13]="#ffffff";                   // white
    $colour[14]="#ff0000";                   // red
    $colour[15]="#007f00";                   // green
    $colour[16]="#ff00ff";		     // disgusting pink
 
    
    $black = 12;
    $white = 13;
    $red   = 14;
    $green = 15;
    $lurid  = 16;

// split out callsign if there is a '<' at the start to use as colour index
 if ($text[0] == "<") {
   if ($text[1] == "*") {
// a /msg directed at the user
     $this_colour = $lurid;
   } else {     
     $callsign = substr($text,1,strpos($text,">")-1);
// use the callsign as an index in an associative array to find the colour
// for this callsign - assign a new one if not found
     if (isset($colour_index[$callsign])) {
       $this_colour = $colour_index[$callsign];
     } else {
       $colour_index[$callsign] = $next_colour;
       $this_colour = $next_colour;
       $next_colour++;
       if ($next_colour >= sizeof($colour)-4) {
         $next_colour = 0;
       }
     }
   }
   
// look for alert messages starting with one or more '*'
 } elseif ($text[0] == "*") {
   if ($text[1] == "*") {
     $this_colour = $red;
   } else {
     $this_colour = $green;
   }
// if not a continuation string then its me so use black
 } elseif ($text[0] != " ") {
   $this_colour = $black;
 } 

// change html special characters to embedded ones
// change all pairs of spaces to non-breaking ones so we keep the table structure
// and remove newlines
 $text = htmlentities($text, ENT_QUOTES);
 $text = str_replace("  ", "&nbsp;&nbsp;", $text);
 $text = str_replace("\n", "<br>", $text);
 $text = str_replace("\\", "&#92;", $text);

// colourize it
 $text = "<font color=\"$colour[$this_colour]\">" . $text . "</font>";
 
 return ($text);

}



// make for easy translation of static messages
 function strOut($val, $message = "") {
  global $str;
  $translated = $str["$val"];
  $translated = ereg_replace("\%m",$message,$translated);
  return $translated;
 }

// make for easy translation of help messages
 function webconversHelp($user, $jsflag) {
  global $user, $help;

  $index = 0;
  while (strlen($help["$index"])) {
    webconversDisplay($user, $jsflag, $help["$index"], 0);
    $index++;
  }

}

// display & scroll the window/frame
 function webconversDisplay ($user, $jsflag, $data, $decodeflag) {

  if (eregi("Macintosh",$_SERVER["HTTP_USER_AGENT"])) { $scroll = 11; }
  else { $scroll = 10000; }

  if ($decodeflag) {
    $data = conv_colour_decode($data);
  } else {
    $data = ereg_replace("\n", "<br>", $data);
  }

  if ($jsflag) {
    CDBinsert($user,"output","",$data);
  } else {
    echo $data;
    echo "<SCRIPT LANGUAGE=\"JavaScript\">window.scrollBy(0, $scroll); </SCRIPT>";
    flush();                      // Send the output immediately.
  }
 }




// send message to convers host from user
 function webconversSendInput ($user, $message) {
  if ($user) {
    CDBinsert($user,"input","",$message);
  }

 }



// send status to main loop from user
 function webconversSendStatus ($user, $status) {
  if ($user) {
    CDBinsert($user,"status","",$status);
  }

 }


?>
