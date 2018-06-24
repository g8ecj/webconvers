<?php
/*************************************************************/
/*		constants definitions              	     */
/*************************************************************/

/*
Database format is "callsign:action:time:userid:text"
text is URL encoded to avoid a clash with the ':' seperator
Not currently using userid... but you never know
Now does 'flock' file locking
*/



/*************************************************************/
/*		database functions              	     */
/*************************************************************/


function CDBexists() {
// check to see if the data file exists, if not create an empty one

  if ( !file_exists( CONVERS_DATA )) {
     $fd = fopen(CONVERS_DATA,"w");
     fclose($fd);
  }
}



function CDBinsert( $callsign, $action, $userid, $text) {
//insert a new message in database
    $dbfile = fopen(CONVERS_DATA,"a+");
// lock file whilst we add new data to the end
    flock($dbfile,LOCK_EX);
    $info=sprintf("%s:%s:%d:%s:%s\n",$callsign,$action,time(),$userid,urlencode($text));
    fputs($dbfile, $info);
    fclose($dbfile);
    if (DEBUG) {
      $dbfile = fopen(CONVERS_TRACE,"a+");
      flock($dbfile,LOCK_EX);
      fputs($dbfile, $info);
      fclose($dbfile);
    }
}    


function CDBgetdata ( $callsign, $action ) {
// search the database for callsign with the correct action type and return
// the text (one line at a time) or null string if nowt
    clearstatcache();
    $flength = filesize( CONVERS_DATA );
    if ($flength) {
      $found = FALSE;
      $ncontent = $text = "";
// this takes longer than file_get_contents but allow locking
      $dbfile = fopen(CONVERS_DATA,"r");
      flock($dbfile,LOCK_SH);
      $content = fread($dbfile, $flength);
      fclose($dbfile);
// the whole file is now in $contents
      $lines = explode("\n",$content);
      foreach ($lines as $line) {
        if (strstr($line, ':')) {	// skip lines with no delimiters
          list ( $dbcallsign , $dbaction, $dbtime, $dbuserid, $dbtext) = explode( ':',$line);
// if the call and action we are looking for then prepare to return it
          if(($callsign == $dbcallsign) && ($dbaction == $action) && (!$found)) {
            $text = urldecode($dbtext);
// only do a line at a time
            $found = TRUE;
            } elseif (strlen($dbcallsign) && ($dbtime+3600 > time())) {
// if valid line copy to output, expire old lines
            $ncontent = $ncontent.sprintf("%s:%s:%d:%s:%s\n",$dbcallsign,$dbaction,$dbtime,$dbuserid,$dbtext);
	  }
        }
      }
// write unexpired data back out to dbase file
      $dbfile = fopen(CONVERS_DATA,"w");
      flock($dbfile,LOCK_EX);
      fputs($dbfile,$ncontent);
      fclose($dbfile);
    }
    return ($text);    	 
}


function CDBgetstatus ( $callsign ) {
  return CDBgetdata( $callsign, "status");
}

function CDBgetinput ( $callsign ) {
  return CDBgetdata( $callsign, "input");
}

function CDBgetoutput ( $callsign ) {
  return CDBgetdata( $callsign, "output");
}

function CDBgetusers ( $callsign ) {
  return CDBgetdata( $callsign, "users");
}

function CDBgetchannels ( $callsign ) {
  return CDBgetdata( $callsign, "channels");
}

function CDBflushusers ( $callsign) {
  do {
    $users =  CDBgetdata( $callsign, "users");
  } while (strlen($users));
}

function CDBflushdb ( $callsign) {
  do {
    $status = CDBgetdata( $callsign, "status");
    $input =  CDBgetdata( $callsign, "input");
    $output = CDBgetdata( $callsign, "output");
    $users =  CDBgetdata( $callsign, "users");
    $channels =  CDBgetdata( $callsign, "channels");
  } while ((strlen($status)) || (strlen($input)) || (strlen($output)) || (strlen($users)) || (strlen($channels)));
}

?>
