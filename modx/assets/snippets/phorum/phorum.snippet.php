<?php
//include($modx->config['base_path'].'assets/snippets/phorum/phorum.snippet.php');
// create a namespace for Phorum
if (!function_exists('phorum_namespace')) {
function phorum_namespace($page)
{
    global $PHORUM;  // globalize the $PHORUM array
    include_once("./$page.php");
}
}
// We have to alter the urls a little
if (!function_exists('phorum_custom_get_url')) {
function phorum_custom_get_url ($page, $query_items, $suffix, $pathinfo)
{
  global $modx,$phorum_assets_id;
  $bypass_pages = array('css','js','feed','file','redirect');
  $PHORUM=$GLOBALS["PHORUM"];

  $resid = in_array($page,$bypass_pages)?$phorum_assets_id:$modx->documentIdentifier;
  $url = $modx->makeUrl($resid);
  if ($pathinfo !== NULL) $url .= $pathinfo;
  $url .= "?$page";
  if(count($query_items)) $url.=",".implode(",", $query_items);
  if(!empty($suffix)) $url.=$suffix;
  return $url;
}
}

global $modx;
$action = isset($action)?$action:'content';

include($modx->config['base_path'].'assets/plugins/phorum/config.inc.php');

$curcwd = getcwd();
$olderror = error_reporting(E_ALL & ~E_NOTICE & ~E_WARINIG);
chdir($phorum_path);

// we set $PHORUM["CUSTOM_QUERY_STRING"] so Phorum will parse it instead of
// the servers QUERY_STRING.
if (preg_match("/^.*(&amp;|&|\\?)([a-z]+),?(.*)?$/",$_SERVER["QUERY_STRING"], $match)) {
  $page=$match[2]; 
  $GLOBALS["PHORUM_CUSTOM_QUERY_STRING"] = $match[3];
  
} else
  $page="index";


include_once($phorum_path.'/common.php');

switch($action) {
	case 'header':
		$headerfilepath = $phorum_path.'/templates/'.$PHORUM['DATA']['template_dir'].'/header_content.php';
		if (file_exists($headerfilepath)) {
			include($headerfilepath);
			$PHORUM['DATA']['INTEGRATE_HEADER'] = true;
			$PHORUM['DATA']['HEADER_LOADED'] = true;
		}
		break;
	case 'content':
		if(file_exists("./$page.php")){
		   phorum_namespace($page);
		}
		break;
}

chdir($curcwd);
error_reporting($olderror);

?>
