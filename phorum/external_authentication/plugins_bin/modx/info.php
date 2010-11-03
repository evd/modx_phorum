<?php

//add this external application's info to the list of possible apps 
$PHORUM["phorum_mod_external_authentication"]["possible_apps"][] = array(
    //The name of your external application, possibly with the supported version 
    //number
    "name"              =>  "MODx Evolution",
    //The folder for your plugin (the folder which contains this info.php file)
    "app_folder"        =>  "modx",
    //The required version of the External Authentication which has the 
    //necessary hook support for your module
    "required_version"  =>  "5.2.1.01",
    //Your name, callsign, etc.
    "author"            =>  "EVD",
    );

?>
