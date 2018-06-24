<?php
/*************************************************************/
/*		constants definitions              	     */
/*************************************************************/

/*
Access control is done at 2 levels - firstly by ip address and then
by callsign. This allows say amprnet users to always have access but
internet users must be registered in the user file



IP address access file format is "domain{^netmask}:action" where domain may
be a FQD or a partial FQD, a dotted quad or a dotted quad with a netmask
 eg. 44.0.0.0^255.0.0.0:check
 is equivalent to
     ampr.org:check



User file format is "callsign:action". We only get as far as the user
file if the action in the domain file says further validation is required

The default action in either case is given by a line of the form
     default:<action>
 eg. default: allow

Action is one of:
allow   - no further validation - allow the user in
deny    - tell user they are not allowed and drop connection
check   - check for callsign in user file. Use action field from user file
monitor - allow user to log in as observer
fail    - silently fail to connect & report (spurious) problem

The last option is to avoid hassle with characters who think they have
a right enshrined in law to access your system!!

Default action is defined by config file if either access file is missing
*/

/*************************************************************/
/*              access functions                           */
/*************************************************************/


function check_access ($user) {


    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
      $ip=$_SERVER["HTTP_X_FORWARDED_FOR"]; 
    } else { 
      $ip=$_SERVER["REMOTE_ADDR"];
    } 

    $level = match_ip($ip);
    if ($level == "check") {
      $level = match_call($user);
    }

    switch ($level) {
    case "allow":
      $signon = "/n";
      break;
    case "deny":
      echo strOut("Access_deny");
      $signon = "END";
      break;
    case "check":
      echo strOut("Access_error");
      $signon = "END";
      break;
    case "monitor":
      $signon = "/ob";
      break;
    case "fail":
      echo strOut("Access_fail");
      $signon = "END";
      break;
    default:
      echo strOut("Access_error");
      $signon = "END";
      break;
    }
    return ($signon);     
}


// try to resolve the user IP address so we have it in both forms
// return action

function match_ip ($ip) {

    $default = DEFAULT_ACCESS;
    clearstatcache();
      if (file_exists( CONVERS_ACCESS )) {
      $content = file_get_contents( CONVERS_ACCESS);
// the whole file is now in $contents
      $lines = explode("\n",$content);
      foreach ($lines as $line) {
	// check for comments and blank lines (no delimiter)
        if (strstr($line, ':') && substr($line, 0, 1) != "#") {
    
          list ( $domain , $action) = explode( ':',$line);

// munge $domain into a string we can compare against. If 1st char numeric
// assume dotted quad with optional netmask
          if (is_numeric(substr($domain,0,1))) {
            if ($c = strpos($domain,'^')) {
              $n=substr($domain,0,$c);
              $n=ip2long($n);
              $m = substr($domain,$c+1);
              $m = ip2long($m);			// extract mask
	      $t = ip2long($ip);
              $domain = long2ip($n & $m);	// network AND mask
              $host = long2ip($t & $m);		// host AND mask
            }
            if($host == $domain) {
              return ($action);
            }

          } else {

// not numeric so convert user IP to hostname
            if($domain=="default") {
              $default = $action;
            } else {
              $host = gethostbyaddr($ip);
              if(strstr($host, $domain)) {
                return ($action);
              }
            }
          }

        }
      }
    }
    return ($default);
}


function match_call ($callsign) {

    $default = DEFAULT_ACCESS;
    clearstatcache();
      if (file_exists( CONVERS_USER )) {
      $content = file_get_contents( CONVERS_USER);
// the whole file is now in $contents
      $lines = explode("\n",$content);
      foreach ($lines as $line) {
	// check for comments and blank lines (no delimiter)
	if (strstr($line, ':') && substr($line, 0, 1) != "#") {
          list ( $user , $action) = explode( ':', $line);

//     	  if (strstr($callsign, $user)) { use if you want to match a substring
    	  if (strcasecmp($callsign, $user) == 0) {
            return ($action);
          }
          if($user=="default") {
            $default = $action;
          }
        }
      }
    }
    return ($default);      
}


?>
