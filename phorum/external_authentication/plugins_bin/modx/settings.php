<?php
/*
    This file allows you to add code to be executed on the module settings page.
    
    A common usage would be to create additional rows of settings to display. 
    Please look at the other Phorum modules' settings pages for examples of how 
    to add rows to a the admin input forms..
    
    Another use would be to create any necessary custom profile fields for your 
    plugin to use.  For custom profile fields, please use this format to ensure 
    unique variables: 
        phorum_mod_external_authentication_YOUR_PLUGIN_FOLDER_YOUR_PROFILE_FIELD
    
    Below is an example of the custom profile field code
    
*/

// Make sure that this script is loaded from the admin interface.  DO NOT remove 
// this line.
if(!defined("PHORUM_ADMIN")) return;
	$PHORUM["phorum_mod_external_authentication"]["remove_disable_phorum_logout"] = 1;

// check if the necessary custom profile field has been created
foreach ($PHORUM["PROFILE_FIELDS"] as $key => $cstm_field) {
    // if the field exists
	if ($cstm_field["name"] == "phorum_mod_external_authentication_modx_modxuserid") {
		// but has been deleted, flag it as disabled
        if (isset($cstm_field["deleted"]) && $cstm_field["deleted"] == TRUE) {
			$modxuserid_status = 2;
		// and is not deleted, flag it as enabled
        } else {
			$modxuserid_status = 1;
		}
	}
}
// if the field does not exist yet
if (!isset($modxuserid_status)) {
    // pull in the necessary api code
	include_once("./include/api/base.php");
	include_once("./include/api/custom_profile_fields.php");
    // and create the field
    phorum_api_custom_profile_field_configure(array (
    	// the id variable must be set to NULL
        'id'            => NULL,
    	'name'          => 'phorum_mod_external_authentication_modx_modxuserid',
    	// the length can be anywhere from 1 to 65000, try to keep it as low as 
        // possible
        'length'        => 255,
        // it is best to disable HTML for a custom profile field to avoid 
        // cross-site scripting
    	'html_disabled' => TRUE,
        // this option will allow the admin to see each user's content for the 
        // custom profile fields
    	'show_in_admin' => FALSE,
	));
    // now flag the field as enabled
    $modxuserid_status = 1;
}
// if the field exists but has been deleted, prompt the admin to recreate the 
// field.
if ($modxuserid_status == 2) {
	$frm->addmessage("Please add the deleted custom profile field named \"phorum_mod_external_authentication__modx_modxuserid\" if you would like to . . .");
} else {
	// possibly show settings based on the existence of your custom profile 
    // field. For example, if your custom profile field allowed users to enable 
    // or disable the page that reads "ni", you could add this input field to 
    // give the forum admin the option of allowing users the ability to enable
    // or disable the function
	$frm->addrow("Administrator groups in MODx:", $frm->text_box("modx_admin_group",$PHORUM["phorum_mod_external_authentication"]["MODx_Admin_Group"],50));
}
?>
