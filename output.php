<?php 
  include("common.php");
  include("access.php");
  $channel = $_GET["channel"];
  header("Content-Type: text/html; charset=" . strOut("charset"));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <title>WebConvers Output</title>
 <style type="text/css">
  <!--
   body {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt }
   p {  font-family: Arial, Helvetica, sans-serif }
  -->
 </style>
</head>

<body bgcolor="#FFFFFF">
&nbsp;
</body>
</html>

<?php 
   static $idle = 0, $listreq = 0;
   $listtimer = 0;

   flush();

   $signon = check_access($user);
   if ($signon == "END") 
   {
     die("");
   }

// clean out old user with same callsign
   CDBflushdb($user);
  
// message of the day
   include(CONVERS_MOTD);

   webconversDisplay ($user, $jsflag, strOut("PleaseWait", CONVERS_SERVER)."<br>", 0);

// open a socket
   $conv_connection_handle = fsockopen(CONVERS_SERVER, CONVERS_PORT);

   if ($conv_connection_handle == FALSE)
   {
     webconversDisplay ($user, $jsflag, strOut("NoHost"), 0);
     die("");
   }

// Now we have the connection, set it to non-blocking so we can poll for RX data
   stream_set_blocking($conv_connection_handle, FALSE);
   
// log onto convers host
   webconversSendInput ($user, $signon." ".$user." ".$channel);
// prevent line truncation - let browser do the line wrap
   webconversSendInput ($user, "/width 1000");

// main loop that talks to the convers host. We have to stay in this loop
// all the time otherwise the socket connection gets closed!!
   while (1) {

// convers host side - note... no idle timeout in this direction
     $data = fgets($conv_connection_handle, 1024);
     while (strlen($data)) {

// state machine to extract user list from host data stream
// 0 = normal data - if status message then kick user display change; 
// 1 = waiting for keyword in '/w *' output (user);
// 2 = process data and look for end of list (***);

       switch ($listreq) {
       case "0":
         webconversDisplay($user, $jsflag, $data, 1);
// kick the user list display if we see a status change message
         if (($jsflag) && (substr($data,0,3)=="***")) {
           CDBinsert($user,"output","",MAGICWORD);
         }
         break;
       case "1":
         if (substr($data,0,4)=="User") {
           $listreq = 2;
         } else {
           webconversDisplay($user, $jsflag, $data, 1);
         }
         break;
       case "2":
         if (substr($data,0,3)=="***") {
           $listreq = 0;
         } else {
           CDBinsert($user,"users","",$data);
         }
         break;
       case "101":
         if (substr($data,0,7)=="Channel") {
           $listreq = 102;
         } else {
           webconversDisplay($user, $jsflag, $data, 1);
         }
         break;
       case "102":
         if (substr($data,8,5)=="Users") {
           $listreq = 103;
         } else {
           webconversDisplay($user, $jsflag, $data, 1);
         }
         break;
       case "103":
         if (substr($data,0,3)=="***") {
           $listreq = 0;
         } else {
           CDBinsert($user,"channels","",$data);
         }
         break;
       }

       $data = fgets($conv_connection_handle, 1024);

     }

     $meta = stream_get_meta_data($conv_connection_handle);
     if ($meta["eof"]) {
       // convers host gone so tell user to exit
       if ($jsflag) {
         webconversDisplay ($user, $jsflag, strOut("JLoggedOff"), 0);
       } else {
         webconversDisplay ($user, $jsflag, strOut("FLoggedOff"), 0);
       }	 
       flush();
       sleep(JSREFRESHMIN + JSREFRESHMAX + 1);
       CDBflushdb($user);
       die("");
     }   

// user side. data first, then status
     $text = CDBgetinput($user);
     if (strlen($text)) {
       fputs($conv_connection_handle, stripslashes($text . "\n"));
       webconversDisplay ($user, $jsflag, stripslashes($text) . "\n", 1);
       $idle = 0;
     }

     $status = CDBgetstatus($user);
     switch ($status) {
     case "touch":
       $idle = 0;               // reset timeout
       break;
     case "help":
       $idle = 0;
       webconversHelp($user, $jsflag);
       break;
     case "logout":
       // the convers host will break the connection so we'll clean up
       fputs($conv_connection_handle, "/q\n");
       $idle = 0;
       break;
     case "userlist":
       /* initiate a request for the users on the channel - prevent duplicates 
          from multiple status changes by flushing any data already in the database */
       CDBflushusers($user);
       fputs($conv_connection_handle, "/w ****\n");
       $listreq = 1;
       $listtimer = 0;
       break;
     case "channels":
       // initiate a request for the total channel list 
       fputs($conv_connection_handle, "/w\n");
       $listreq = 101;
       $listtimer = 0;
       break;

     }

     // Sleep to save the CPU & implement timeout
     flush();
     sleep(1);
     // cancel request for user list if timeout
     $listtimer++;
     if ($listtimer >=LISTTIMEOUT) {
       $listreq = 0;
     }

     $idle++;
     if ($idle >= TIMEOUT) {
       // user must have gone as we are getting no refreshes
       CDBflushdb($user);
       fclose($conv_connection_handle);
       die("");
     }
   }


?>

</body>
</html>
