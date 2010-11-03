<?php

/*
    This file gives plugins a chance to override Phorum variables and settings 
    near the end of the common.php script. This can be used to override the 
    Phorum (settings) variables that are setup during this script. 
    
    Below is an example of how your plugin can override the template color
    variables.
    
*/

// Make sure that this script is loaded inside the Phorum environment.  DO NOT 
// remove this line
if (!defined("PHORUM")) return;

/* This example shows how a plugin can override the template color variables
 * If your plugin needs to do this, please also note the css_version set in the 
 * settings_post.php file.  The Drupal_6_x plugin can also be used as an example
 * for this functionality
 *
 * global $PHORUM;
 *
 * $PHORUM["phorum_mod_external_authentication"]["base_plugin"]["color_variables"] = array (
 *     "default_background_color" => "#EEEEEE",
 *     );
 *
 * if (!empty($PHORUM["phorum_mod_external_authentication"]["base_plugin"]["color_variables"])) {
 *     $PHORUM["DATA"] = array_merge($PHORUM["DATA"], $PHORUM["phorum_mod_external_authentication"]["base_plugin"]["color_variables"]);
 * }
 */
 
?>
