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
    // clear the previous session in case the user logged out of the external application and Phorum login is disabled
    if (!empty($PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"])) {
        $session_data[PHORUM_SESSION_LONG_TERM] = FALSE;
        $session_data[PHORUM_SESSION_SHORT_TERM] = FALSE;
    }
    return $session_data;
}

//switch back to our working directory
chdir($curcwd);

// get the api code for various user-related functions
include_once("./include/api/user.php");

include_once("./include/api/custom_profile_fields.php");
// use the external username to get a Phorum user_id
// First try find by custom field
$userid_customfield = phorum_api_custom_profile_field_byname('phorum_mod_external_authentication_modx_modxuserid');
$user_id = phorum_api_user_search_custom_profile_field($userid_customfield['id'],$user_data['id']);
if (!$user_id) {
	//Custom field not found
	//Try search by username
	$user_id = phorum_api_user_search("username",$user_data['username']);
	if ($user_id) {
		//User found, add custom profile field
		$phorum_user_data = array(
			"user_id" => $user_id,
			"phorum_mod_external_authentication_modx_modxuserid" => $user_data['id']
			);
		phorum_api_user_save($phorum_user_data);	
	}
}
// then get the Phorum user data from that user_id
$phorum_user_data = phorum_api_user_get($user_id);

if (!empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"]) &&
		strlen($PHORUM["phorum_mod_external_authentication"]["MODx_Admin_Group"])>=0) {
	if ($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"] &&
			$modx->isMemberOfWebGroup(explode(',',$PHORUM["phorum_mod_external_authentication"]["MODx_Admin_Group"])))
          $user_data["admin"] = 1;   
}
// if the Phorum user does not exist then we need to create them
if (empty($phorum_user_data)) {
    $phorum_user_data = array(
        // The user_id must be NULL to create a new user
        "user_id" => NULL,
        "username" => $user_data["username"],
		"real_name" => $user_data["fullname"],
        // by transferring the password, we are ensuring that the user will be
        // able to login if the admin enables Phorum login
        "password" => $user_data["password"],
        // Phorum requires an email.  If the external application does not, 
        // a fake email should be used.
        "email" => $user_data["email"],
        // By default, create a non-admin user.  Admin status is handled later.
        "admin" => 0,
        "active" => PHORUM_USER_ACTIVE,
		"phorum_mod_external_authentication_modx_modxuserid" => $user_data['id']
        );
   
    // if the admin wants to automatically transfer admin status
	if (!empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"])) {
		$phorum_user_data["admin"] = $user_data["admin"];
	}
    // create the new user and get the user_id with which to create a session.
    // Please note, most applications will give you the md5 of the user's 
    // password.  The constant PHORUM_FLAG_RAW_PASSWORD tells Phorum that the 
    // password is already in md5.  If you need to create a user with a plain
    // text password, simply omit the second variable in this call
    $user_id = phorum_api_user_save($phorum_user_data, PHORUM_FLAG_RAW_PASSWORD);
    
// however, if the user exists but is not active, then we should not log them in    
} elseif (empty($phorum_user_data["active"])) {
    return $session_data;
// or, if the user exists, then run some check on the user's data
} else {
    // if the extenal application user's password or real name has changed, update the phorum 
    // user's password
    if ($phorum_user_data["password"] != $user_data["password"] || 
		$phorum_user_data["real_name"] != $user_data["fullname"]) {
        $phorum_user_data["password"] = $user_data["password"];
        $phorum_user_data["real_name"] = $user_data["fullname"];
        // save the updated user data, again with a preset md5 password
        $user_id = phorum_api_user_save($phorum_user_data,PHORUM_FLAG_RAW_PASSWORD);
    }
    
    // if the admin wants to automatically transfer admin status and the 
    // external user has been upgraded to admin, upgrade the phorum user, again 
    // assuming the external application establishes admin status this way
    if ($user_data["admin"] && empty($phorum_user_data["admin"]) && !empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"])) {
        $phorum_user_data["admin"] = 1;
        // save the updates user data
        $user_id = phorum_api_user_save($phorum_user_data);
    // if the admin wants to automatically transfer admin status and the 
    // external user has been downgraded from admin, downgrade the phorum user
    } elseif (!$user_data["admin"] && !empty($phorum_user_data["admin"]) && !empty($PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"])) {
        $phorum_user_data["admin"] = 0;
        // save the updates user data
        $user_id = phorum_api_user_save($phorum_user_data);
    }
}

//we have a legit user, so set there session info
$session_data[PHORUM_SESSION_LONG_TERM] = $user_id;
$session_data[PHORUM_SESSION_SHORT_TERM] = $user_id;

?>
