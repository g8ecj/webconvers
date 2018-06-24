<?php
  include("common.php");

  if (!isset($_GET["startup"])) {
    webconversSendStatus($user, "userlist");
    sleep(LISTTIMEOUT);
  }
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
   <title>WebConvers Users</title>
   <style type="text/css">
    <!--
     table     { margin-left: auto; margin-right: auto; text-align: center }
     a:link    { color: #000000; text-decoration: none }
     a:visited { color: #000000; text-decoration: none }
     a:active  { color: #000000; text-decoration: none }
     a:hover   { color: #ff0000; text-decoration: underline }
    -->
   </style>
   <script language="Javascript">
   <!--
     function msg(p) {
       slashpMsg = window.open(p, "slashp", "width=700,height=100,resizable=no,toolbar=no,menubar=no");
       slashpMsg.focus();
     }
     function dorefresh (p) {
       document.refresh.option.value = p;
       document.refresh.submit();
    }
   // -->
   </script>
  </head>

  <body bgcolor="#FFFFFF">
<?php
// note that the positions of text on the line depends on the
// convers server version being used - most are the same but...

$line = CDBgetusers($user);
$channel = trim(substr($line,32,5));
echo "<table border=0>\n";
echo "<tr><td bgcolor=\"#00ffff\">Users on</td></tr>\n";
echo "<tr><td bgcolor=\"#00ffff\">Channel&nbsp;".$channel."</td></tr>\n";
echo "<tr><td bgcolor=\"#ffffff\"> </td></tr>\n";
while (strlen($line)) {
  $callsign = trim(substr($line,0,8));
  $personal = trim(substr($line,45));
  if (!strlen($personal)) {
     $personal="@@";
  }
  $line = CDBgetusers($user); 
?>
  <tr><td bgcolor="#ffffff"><a title="<?php echo $personal?>"
  href=javascript:msg('<?php echo dirname($_SERVER["PHP_SELF"])?>/slashp.php?callsign=<?php echo $callsign?>&personal=<?php echo urlencode($personal)?>')><?php echo $callsign?>
  </a></td></tr>
<?php
}
?>
  <tr><td bgcolor="#ffffff"> </td></tr>
  <tr><td bgcolor="#ff7f00"> 
  <form name="refresh" action="<?php echo basename($_SERVER['PHP_SELF'])?>" method=POST>
    <input type="hidden" name="option" 
    <a title="<?php echo strOut("TipRefresh")?>"
       href="javascript:dorefresh('refresh')"><?php echo strOut("RefreshUserList")?>
    </a>
  </form>
  </td></tr>
</table>  
  
  
</body>
</html>