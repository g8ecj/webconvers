<?php
  include("common.php");

  $option = $_REQUEST["option"];
  $onload = "";
  switch ($option) {
  case "logoff":
    webconversSendStatus($user, "logout");
    setcookie("webconversuser", "", time()-3600, "/webconvers");
    setcookie("webconversjsflag", "", time()-3600, "/webconvers");
    if ($jsflag) {
      $onload = "onLoad=\"parent.close();\"";
    }
    break;
  case "links":
    webconversSendInput($user, "/links\n");
    break;
  case "help":
    webconversSendStatus($user, "help");
    break;
  default:
    webconversSendStatus($user, "touch");
  }
  header("Content-Type: text/html; charset=" . strOut("charset"));
  header("Refresh: " . TIMEOUT/2 . "; URL=" . basename($_SERVER["PHP_SELF"]) . "?option=touch");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>                          
    <title>WebConvers Options</title>
   <style type="text/css">
    <!--
     a:link    { color: #000000; text-decoration: none }
     a:visited { color: #000000; text-decoration: none }
     a:active  { color: #000000; text-decoration: none }
     a:hover   { color: #ff0000; text-decoration: underline }
    -->
   </style>
    <script language="Javascript">
    <!-- 
       function ChanWin(p) {
         ChanList = window.open(p, "channels", "width=600,height=400,resizable=no,scrollbars=yes,toolbar=no,menubar=no");
         ChanList.focus();
       }
    // -->
    </script>
<?php if (($jsflag) && ($option !="startup")) { ?>
    <script language="Javascript">
    <!-- 
       parent.java.document.refresh.submit();
    // -->
    </script>
<?php } ?>
  </head>

  <body bgcolor="#FFFFFF" <?php echo $onload?>>
    <p><font size="-1">
    <a title="<?php echo strOut("TipChannels")?>"
       href="javascript:ChanWin('<?php echo dirname($_SERVER["PHP_SELF"])?>/channels.php?channel=&leave=')"><?php echo strOut("ChannelSelect")?></a> |
    <a title="<?php echo strOut("TipLinks")?>"
       href="<?php echo basename($_SERVER["PHP_SELF"])?>?option=links"><?php echo strOut("HostLinkLink")?></a> |
    <a title="<?php echo strOut("TipHelp")?>"
       href="<?php echo basename($_SERVER["PHP_SELF"])?>?option=help"><?php echo strOut("HelpLink")?></a> |
    <a title="<?php echo strOut("TipLogoff")?>"
       href="<?php echo basename($_SERVER["PHP_SELF"])?>?option=logoff"><?php echo strOut("LogoffLink")?></a>
    </font></p>
  </body>
</html>
