<?php

# centralised configuration

define("VERSION", "2.1beta3");

// the following MUST be edited to suit your system (otherwise it won't work)
//define("CONVERS_SERVER","lurpac.lancs.ac.uk");
define("CONVERS_SERVER","ww2.n1uro.com");
define("CONVERS_GATEWAY","gilks.ath.cx");

// the following SHOULD be edited to suit your system (this is where TVIPUG hangs out)
define("DEFAULT_CHANNEL","160");

// path to the database, MOTD & access control files
define("CONVERS_PATH","/var/local/webconvers/");

// the following MAY be changed to suit your system (to suit area and traffic profiles)
define("LANGUAGE", "english");  // current translations: english - others required
define("JSREFRESHMIN", "2");        // Javascript mode refresh 'wait for data' time
define("JSREFRESHMAX", "10");       // Javascript mode refresh time
// these mostly work OK irrespective of the rest!!
define("CONVERS_PORT", "3600"); // this is standard on the tpp servers
define("TIMEOUT", "180");       // is the user timeout in case user just disappears
define("REFRESHTIMEOUT", "180");// time to refresh the user list
define("LISTTIMEOUT", "6");     // how long the server takes to return "/w *" data
define("MAGICWORD","UsErLiStReQuEsT");

// the inter-frame communication database file
define("CONVERS_DATA"  , CONVERS_PATH."convers-data.txt");

// message-of-the-day
define("CONVERS_MOTD"  , CONVERS_PATH."convers-motd.txt");

// the following is for access control defaults
define("DEFAULT_ACCESS", "allow");
define("CONVERS_ACCESS", CONVERS_PATH."convers-access.txt");
define("CONVERS_USER"  , CONVERS_PATH."convers-user.txt");

// tracing
define("DEBUG",  "1");
define("CONVERS_TRACE" , CONVERS_PATH."convers-trace.txt");


?>
