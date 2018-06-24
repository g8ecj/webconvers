<?php
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $user = $_POST["user"];
   $channel = $_POST["channel"];
   $jsflag = $_POST["jsflag"];

   $user = ereg_replace(" ", "_", $user);
   $user = strip_tags($user);
   if ($user == "-") { $user = ""; }
   if ($user == "*") { $user = ""; }
   if ($user) {
    setcookie("webconversuser", $user, time()+600, "/webconvers");
    setcookie("webconversjsflag", $jsflag, time()+600, "/webconvers");
   }
 } else {
   $jsflag = $_GET["jsflag"];
 }

 include("common.php");

 if ($jsflag) {
    $onload = "onload=\"document.form.user.focus();document.form.user.select();\"";
 } else {
    $onload = "";
 }
  header("Content-Type: text/html; charset=" . strOut("charset"));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
 <head>
  <title>WW Convers @ <?php echo CONVERS_GATEWAY?></title>
 </head>

<?php if (!isset($user)):?>
 <body <?php echo $onload?>>
  <form name="form" action="<?php echo basename($_SERVER["PHP_SELF"])?>" method=POST>
    <?php echo strOut("AskForCallsign")?>
   <br><br><br>
   <input type="text"   name="user" size="10" maxlength="10">
   <br><br>
   <input type="text"   name="channel" value="<?php echo DEFAULT_CHANNEL?>" size="6" maxlength="6">
   <?php echo strOut("AskForChannel")?>
   <br><br>
   <input type="Submit" value="<?php echo strOut("AskForCallButton")?>" title="<?php echo strOut("TipAskForCall")?>">
   <input type="hidden" name="jsflag" value="<?php echo $jsflag?>">
   <br><br><br><br>
   <?php echo strOut("CookieNote")?>
  </form>

  <p>
<?php
  if ($jsflag) {
    echo strOut("ScriptProblem");
  }
 else {
    echo strOut("NoScriptProblem");
  }
?>

 </body>

<?php else:?>

 <?php CDBexists()?>

 <?php if ($jsflag) { // container for user frame ?>
  <frameset cols="*,15%">
 <?php } ?>

   <?php if ($jsflag) { // containers for output and hidden JS frames ?>
     <frameset rows="*,1,12%,15%"> 
       <frame name="output"  src="output.php?channel=<?php echo $channel?>">
       <frame name="java"    src="jsout.php" scrolling="no" noresize marginwidth="0" marginheight="0">
   <?php } else { // container for output only ?>
     <frameset rows="*,12%,15%"> 
       <frame name="output"  src="output.php?channel=<?php echo $channel?>">
   <?php } ?>
       <frame name="options" src="options.php?option=startup" scrolling="NO">
       <frame name="input" scrolling="NO" noresize src="input.php?startup=TRUE">
   </frameset>

<?php if ($jsflag) { // the actual user frame itself ?>
   <frame name="users"  src="users.php?startup=TRUE">
 </frameset>

<?php } ?>
 <noframes>
   <body bgcolor="#FFFFFF">
     <?php echo strOut("NoFrames")?>
   </body>	 
 </noframes>

<?php endif;?>

</html>
