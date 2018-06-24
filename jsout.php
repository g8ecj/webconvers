<?php

include ("common.php");

# search in database for stuff to output
# if nowt in database then output null - just refresh timeout

  sleep(JSREFRESHMIN);
  $dcount = 0;		/* data count */
  $mcount = 0;		/* magic word count */
  
  header("Expires: Sun, 28 Dec 1997 09:32:45 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");
  header("Content-Type: text/html; charset=" . strOut("charset"));
  header("Refresh: " . JSREFRESHMAX . "; URL=" . basename($_SERVER["PHP_SELF"]));
  setcookie("webconversuser", $user, time()+600, "/webconvers");
  setcookie("webconversjsflag", $jsflag, time()+600, "/webconvers");
?>
  
  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
  <html>
   <head>
    <script language="Javascript">
    <!--
     <?php
       $outdata = CDBgetoutput($user);
       while (strlen($outdata)) {
         if ($outdata == MAGICWORD) {
	   $mcount++;
     ?>
 <?php } else { ?>
           parent.output.document.write('<?php echo $outdata ?>');
     <?php
           $dcount++;
         }
         $outdata = CDBgetoutput($user);
       }
     ?>
 <?php if ($dcount) { ?>
          parent.output.scroll(1,5000000);
 <?php } ?>
 <?php if ($mcount) { ?>
          parent.users.document.refresh.submit();
 <?php } ?>

    // -->
    </script>
   </head>
   <body bgcolor="#FFFFFF">
     <form name="refresh" action="<?php echo basename($_SERVER["PHP_SELF"])?>" method=POST>
       <input type="submit" name="myname" value="myval">
     </form>
   </body>
  </html>

