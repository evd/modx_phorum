<?php
global $modx;
$curcwd = getcwd();
chdir($modx->config["base_path"]."phorum");

// we set $PHORUM["CUSTOM_QUERY_STRING"] so Phorum will parse it instead of
// the servers QUERY_STRING.
if (preg_match("/^.*(&amp;|&|\\?)([a-z]+),?(.*)?$/",$_SERVER["QUERY_STRING"], $match)) {
  $page=$match[2]; 
  $GLOBALS["PHORUM_CUSTOM_QUERY_STRING"] = $match[3];
} else
  $page="index";

if(file_exists("./$page.php")){
    phorum_namespace($page);
}
chdir($curcwd);

// create a namespace for Phorum
function phorum_namespace($page)
{
    global $PHORUM;  // globalize the $PHORUM array
    include_once("./$page.php");
}

// We have to alter the urls a little
function phorum_custom_get_url ($page, $query_items, $suffix, $pathinfo)
{
  global $modx; 
  $PHORUM=$GLOBALS["PHORUM"];

  $url = $modx->makeUrl($modx->documentIdentifier);
  if ($pathinfo !== NULL) $url .= $pathinfo;
  $url .= "?$page";
  if(count($query_items)) $url.=",".implode(",", $query_items);
  if(!empty($suffix)) $url.=$suffix;
  return $url;
}
?>
