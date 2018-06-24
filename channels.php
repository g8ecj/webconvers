<?php
  include("common.php");

  $channel = $_REQUEST["channel"];
  $leave = $_REQUEST["leave"];
  if (strlen(trim($channel))) {
    if ($leave == 0) {
      webconversSendInput($user, "/c ".$channel);
    } else {
      webconversSendInput($user, "/le ".$channel);
    }
  }
  webconversSendStatus($user, "channels");
  sleep(LISTTIMEOUT);

  header("Expires: Sun, 28 Dec 1997 09:32:45 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");
  header("Content-Type: text/html; charset=" . strOut("charset"));
  header("Refresh: " . REFRESHTIMEOUT . "; URL=" . basename($_SERVER["PHP_SELF"]));

?>
  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
  <html>
  <head>                          
   <title>WebConvers Channels in Use</title>
   <style type="text/css">
    <!--
     table     { margin-left: auto; margin-right: auto; text-align: center }
     a:link    { color: #00007f; text-decoration: none }
     a:visited { color: #000000; text-decoration: none }
     a:active  { color: #000000; text-decoration: none }
     a:hover   { color: #ff0000; text-decoration: underline }
     a.join    { color: #007f00 }
     a.leave   { color: #ff0000 }
    -->
   </style>
   <script language="Javascript">
   <!--
     function msg(p) {
       slashpMsg = window.open(p, "slashp", "width=700,height=100,resizable=no,toolbar=no,menubar=no");
       slashpMsg.focus();
     }
     function doexit()
     {
       self.close();      
     }
   // -->
   </script>
  </head>

  <body bgcolor="#FFFFFF">
<?php

// display a channel, its topic and list of users
// if I'm flagged as being on this channel then ake a 'leave' link
function displaychan() 
{
  global $channel, $onchan, $topic, $userlist;

    if (strlen($channel)) {
      if ($onchan == 0) {
        echo '<a title="Change to channel '.$channel.'"';
        echo ' href="'.basename($_SERVER["PHP_SELF"]).'?channel='.$channel.'&leave=0"';
        echo ' class=join>'.$channel.'</a>';
      } else {         
        echo '<a title="Leave channel '.$channel.'"';
        echo ' href="'.basename($_SERVER["PHP_SELF"]).'?channel='.$channel.'&leave=1"';
        echo ' class=leave>'.$channel.'</a>';
        $onchan = 0;
      }
      
      if (strlen(trim($topic))) {
        echo ' '.trim($topic);
      }
      echo "<br>\n";
      $topic = "";
      echo $userlist."<br>\n";
      $userlist = "";
    }
}

// note that the positions of text on the line depends on the
// convers server version being used - most are the same but...

echo "<center><font color='#ff0000' size='+1'>Channel Selections available</font></center><br>\n";
// start with no channel, topic or users, get a line of text & off we go...
$topic = "";
$channel = "";
$userlist = "";
$onchan = 0;
$line = CDBgetchannels($user);
while (strlen($line)) {
// see if its a line with a channel number on it. If so, o/p last channel's data
  if (is_numeric(substr($line,6,1))) {
// display last channel & its users
    displaychan();
// get THIS channel
    $channel = trim(substr($line,2,5));
// remove channel number from start of line
    $line = substr($line,8);

// see if title string next - if so save it for display, then get another line and loop
    if (substr($line,2,4) == "    ") {
      $topic = $line;
      $line = CDBgetchannels($user);
      continue;
    }
  }
// a continuation line with more callsigns or remainder of channel line with callsigns
  $users = explode(' ', trim($line));
  
// parse the list of users to get callsigns and make links to slashp    
  foreach($users as $callsign) {
// truncate at a colon or bracket
    $pos = strpos($callsign, ':');
    if ($pos !== false) {
      $callsign = substr($callsign, 0, $pos);
    }
    $pos = strpos($callsign, '(');
    if ($pos !== false) {
      $callsign = substr($callsign, 0, $pos);
    }
    $userlist .= '<a title="Private message to '.$callsign.'" href=javascript:msg("'.dirname($_SERVER["PHP_SELF"]).'/slashp.php?callsign='.$callsign.'&personal=")>'.$callsign."</a> \n";
// if I'm on this channel then flag it (I could be on several!!)
    if ($callsign == $user) {
      $onchan = 1;
    }
  }
  $line = CDBgetchannels($user); 
}
// display last channel
displaychan();

?>
  <center>
    <input type="button" value="<?php echo strOut("privateClose")?>" onclick="doexit()">
  </center><br>
  
</body>
</html>