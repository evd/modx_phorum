<?php
	global $modx;
	global $database_type, $database_server, $database_user, $database_password, $database_connection_charset, $database_connection_method, $dbase, $table_prefix, $site_sessionname;

	if (!isset($modx)) {
		include $PHORUM["phorum_mod_external_authentication"]["app_path"].'/manager/includes/config.inc.php';
		session_name($site_sessionname);
		session_start();
		include $PHORUM["phorum_mod_external_authentication"]["app_path"].'/manager/includes/document.parser.class.inc.php';
		$modx = new DocumentParser;

		// set some parser options
		$modx->minParserPasses = 1; // min number of parser recursive loops or passes
		$modx->maxParserPasses = 10; // max number of parser recursive loops or passes
		$modx->dumpSQL = false;
		$modx->dumpSnippets = false; // feed the parser the execution start time
		$modx->tstart = $tstart;

		// Debugging mode:
		$modx->stopOnNotice = false;

		// Don't show PHP errors to the public
		if(!isset($_SESSION['mgrValidated']) || !$_SESSION['mgrValidated']) {
			@ini_set("display_errors","0"); 
		}
		$modx->db->connect();
	}
?>