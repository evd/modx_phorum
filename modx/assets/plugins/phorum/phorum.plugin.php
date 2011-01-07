<?php //if you paste this code in plugin remove this line
//include($modx->config['base_path'].'assets/plugins/phorum/phorum.plugin.php');
global $modx;
$e =&$modx->event;
if ($e->name == 'OnWebSaveUser') {
	include($modx->config['base_path'].'assets/plugins/phorum/config.inc.php');
	include($modx->config['base_path'].'assets/plugins/phorum/syncuser.php');
	$user_data = array(	'id'=>$userid,
						'username'=>$username,
						'fullname'=>$userfullname,
						'password'=>$userpassword,
						'email'=>$useremail);
	$curcwd = getcwd();
	chdir($phorum_path);
	syncuser($phorum_path,$user_data);
	chdir($curcwd);
}    
//if you paste this code in plugin remove line below
?>
