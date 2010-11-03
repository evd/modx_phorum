<?php

/*
    This file gives modules a chance to override Phorum variables and settings, 
    after the active user has been loaded. The settings for the active forum are
    also loaded before this hook is called, therefore this hook can be used for 
    overriding general settings, forum settings and user settings. 
    
*/

// Make sure that this script is loaded inside the Phorum environment.  DO NOT 
// remove this line
if (!defined("PHORUM")) return;

?>
