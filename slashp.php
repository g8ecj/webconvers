<?php  
  include("common.php");

  $callsign = $_REQUEST["callsign"];
  $personal = $_REQUEST["personal"];
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $slashp = $_POST["slashp"];
    if ($slashp) {
      webconversSendInput($user, "/msg ".$callsign." ".$slashp);
    }
  }
  header("Content-Type: text/html; charset=" . strOut("charset"));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
 <head>
  <title>
    Webconvers: <?php echo "talking to ".$callsign."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [".$personal."]"?>
  </title>

  <script language="Javascript">
  <!--
    function doexit()
    {
      self.close();      
    }
    
    window.opener.parent.java.document.refresh.submit();
  // -->
  </script>
 </head>

 <body bgcolor="#FFFFFF" onload="document.form.slashp.focus()">
  <table border="0" width="100%">
   <colgroup width="80%">
   <colgroup width="10%">
   <colgroup width="10%">
   <tr>
     <td><?echo strOut("privateMsg",$callsign)?>
     <td>&nbsp;
     <td><input type="button" value="<?php echo strOut("privateClose")?>" onclick="doexit()">
  </table>
  <br>
  <form name="form" action="<?php echo basename($_SERVER["PHP_SELF"])?>" method=POST>
    <input type="text" name="slashp" size="60" STYLE="width: 100%">
    <input type="hidden" name="callsign" value="<?php echo $callsign?>">
    <input type="hidden" name="personal" value="<?php echo $personal?>">
  </form>
 </body>
</html>
