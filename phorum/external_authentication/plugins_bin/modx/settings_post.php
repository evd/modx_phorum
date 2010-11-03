<?php
/*
    This file allows you to process post data on the module settings page.  This 
    code will execute just before the $PHORUM["phorum_mod_external_authentication"]
    data is committed to the database.
    
    The example below continues from the example of the settings.php plugin file 
    in which the admin was given the option to allow users to disable the page 
    that reads "ni."  This is also an example of how to toggle a checkbox in the 
    admin input form.
    
    This file can also be used to setup a user id synchronization table in the 
    Phorum database.  This database can be used to synchronize users when the
    username cannot be used.  This is especially useful in systems where users
    can change their usernames.
    
    You will find the necessary code on line 41 of this base settings_post.php 
    file.  Simply un-comment the line to utilize the synchronization table.
    
    If you would like to allow the settings page to search for the external 
    application's server path, you can set the $external_application_path_ids
    array. This array consists of two values:
        unique_file_name is a file which should be unique to your external
            application.  Please note that if the file is in a sub-directory of
            the external application, you should include that sub-directory in 
            this field (eg. "includes/misc/myfile.php").
        unique_string is an optional string to search for in your unique file.
            If you are sure that your file is unique, you can simply leave this
            string empty.
    
    You will find the necessary code on line 45 of this settings_post.php file.
    Simply remove the opening and closing comment lines and change the settings
    to reflect your external application.
*/

// Make sure that this script is loaded from the admin interface.  DO NOT remove 
// this line.
if(!defined("PHORUM_ADMIN")) return;

// Utilize the synchronization table
//$utilize_synchronization_table = TRUE;

// Allow the system to search for your external applications path

$external_application_path_ids = array (
    //unique file name for application path auto-detection
    "unique_file_name"  =>  "common.php",
    //optional unique string to search for in unique file
    "unique_string"     =>  "define( \"PHORUM\"",
    );


$PHORUM["phorum_mod_external_authentication"]["MODx_Admin_Group"] = !empty($_POST["modx_admin_group"]) ? $_POST["modx_admin_group"] : '';

/*
    By default, this module allows the forum admin to disable/enable 
    registration, login, and logut directly in Phorum.  If your plugin does not 
    support Phorum registration, login, or logout, you can remove these options 
    from the settings page.
    
    Please remove the commenting from any of the following options that you 
    would like to make use of.
*/

// remove the option to transfer admin status to Phorum
//$PHORUM["phorum_mod_external_authentication"]["remove_transfer_admin_status"] = 1;
// and force transfering admin status
//$PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"] = 0;
// or disable transfering admin status
//$PHORUM["phorum_mod_external_authentication"]["transfer_admin_status"] = 1;

// remove the option to disable/enable Phorum logout
$PHORUM["phorum_mod_external_authentication"]["remove_disable_phorum_logout"] = 1;
// and enable Phorum logout
$PHORUM["phorum_mod_external_authentication"]["disable_phorum_logout"] = 0;
// or disable Phorum logout
//$PHORUM["phorum_mod_external_authentication"]["disable_phorum_logout"] = 1;

// remove the option to disable/enable Phorum login
$PHORUM["phorum_mod_external_authentication"]["remove_disable_phorum_login"] = 1;
// and enable Phorum login
$PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"] = 0;
// or disable Phorum login
//$PHORUM["phorum_mod_external_authentication"]["disable_phorum_login"] = 1;

// remove the option to disable/enable Phorum registration
$PHORUM["phorum_mod_external_authentication"]["remove_disable_phorum_registration"] = 1;
// and enable Phorum registration
$PHORUM["phorum_mod_external_authentication"]["disable_phorum_registration"] = 0;
// or disable Phorum registration
//$PHORUM["phorum_mod_external_authentication"]["disable_phorum_registration"] = 1;

// If you need to override the default color variables, be sure to update the 
// css version each time those variables are changed from the settings page.
// The easiest way to do this is to update the css version each time the admin
// submits the settings page.
/*
if (!empty($PHORUM["phorum_mod_external_authentication"]["base_plugin"]["color_variables"])) {
    $PHORUM['phorum_mod_external_authentication']['css_version'] =
		    isset($PHORUM['phorum_mod_external_authentication']['css_version'])
        ? $PHORUM['phorum_mod_external_authentication']['css_version'] + 1 : 1;
}
*/
?>
