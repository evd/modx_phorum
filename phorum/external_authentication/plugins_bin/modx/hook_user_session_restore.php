<?php
/*
    This file is used on each Phorum page to check the external application's 
    user session and create a session for the current user in Phorum.
    
    Please note, that any time you want to end the script (perhaps because an 
    error occurs) you must use the command: return $session_data;
    
    Below is an example of various possible needs for your plugin
    
*/

// Make sure that this script is loaded inside the Phorum environment.  DO NOT 
// remove this line
if (!defined("PHORUM")) return;

// If you need to run php code located in the external application's server path 
// you can use the following code as an example

// no need to continue if the external app path is not set.
if (empty($PHORUM["phorum_mod_external_authentication"]["app_path"])) return $session_data;

require_once dirname(__FILE__).'/modxapi.inc.php';

// save the working directory and move to the external application's directory
$curcwd = getcwd();
chdir($PHORUM["phorum_mod_external_authentication"]["app_path"]);

// get the user info from the external application
$LoginUserID = $modx->getLoginUserID();
if (!is_null($LoginUserID)) {
	$user_data = $modx->getWebUserInfo($LoginUserID);
}
// if there is no user data, then no need to continue
if (empty($user_data))  {
    // change back to the Phorum directory
    chdir($curcwd);
    // clear the previous session in case the user logged out of the external application
	$session_data[PHORUM_SESSION_LONG_TERM] = FALSE;
	$session_data[PHORUM_SESSION_SHORT_TERM] = FALSE;
    return $session_data;
}

//switch back to our working directory
chdir($curcwd);

include_once($PHORUM["phorum_mod_external_authentication"]["app_path"].'/assets/plugins/phorum/syncuser.php');
$user_id = syncuser($curcwd, $user_data);
if (empty($user_id))
	return $session_data;

//we have a legit user, so set there session info
$session_data[PHORUM_SESSION_LONG_TERM] = $user_id;
$session_data[PHORUM_SESSION_SHORT_TERM] = $user_id;

?>
