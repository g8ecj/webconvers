<?php  
  include("common.php");

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST["message"];
    if ($message) {
      webconversSendInput($user, $message);
    }
  }
  if ($jsflag) {
     $highlite = "\"document.form.message.focus();document.form.message.select();\"";
  } else {
     $highlite = "";
  }
  header("Content-Type: text/html; charset=" . strOut("charset"));
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
 <head>
  <title>
    Webconvers Input
  </title>
<?php if (($jsflag) && (!isset($_GET["startup"]))) { ?>
  <script language="Javascript">
  <!-- 
     parent.java.document.refresh.submit();
  // -->
  </script>
<?php } ?>
 </head>

 <body bgcolor="#FFFFFF" onload=<?php echo $highlite?>>
  <form name="form" action="<?php echo basename($_SERVER["PHP_SELF"])?>" method=POST
      onsubmit=<?php echo $highlite?>>
    <input type="text" name="message" size="60" STYLE="width: 100%">
  </form>
 </body>
</html>

