<?php
require dirname(__FILE__)."/appcore/dataconnect/database.inc.php";

if (!empty($_SERVER['PATH_INFO'])) //Are we forwarding from 'pretty' urls?
{
 @list($section,$item,$action)=explode("/",ltrim($_SERVER['PATH_INFO'],"/")); //q holds a path, we need to split it!
 $path="/".ltrim($_SERVER['PATH_INFO'],"/");
}
else //we are going direct from the query string!
{
 foreach ($_GET as $var=>$val) //Parse through the $_GET array and set variables for each named key
 {
  ${$var}=$val;
 }
 $path="/".ltrim(@$_GET['path'],"/");
}

if (@$action == "install")
{
 define("USR","installer");
}
elseif (!file_exists(dirname(__FILE__)."/appcore/dataconnect/connect.ini"))
{
 header("Location: ./app.php?action=install");
}
else
{
 require_once dirname(__FILE__)."/appcore/common.inc.php";
 $conf=new WCMSettings();
 $GLOBALS['CONF']=$conf->get();
}

if ($action != 'install')
{
 require dirname(__FILE__)."/appmodules/ucp.mod.php";
 echo ucp_module($GLOBALS['CURUSR'],'box');
} ?>
<!doctype html>
<html>
<?php
if (!@$_GET['page'])
{
  load_index($path);
}
else
{
  load_page($_GET['page'],$page);
}

function load_index($path)
{
 if (empty($path) || $path == "/")
 {
  require_once dirname(__FILE__)."/appmodules/series.mod.php";
  $body=list_projects();
 }
?>
<head>
 <title>Tower21 WebComiX: <?php echo($path) ?></title>
</head>
<body>
 <?php echo($body); ?>
</body>
<?php }

function load_page($page,$path)
{
 var_dump($page);
}
?>
</html>
<?php
