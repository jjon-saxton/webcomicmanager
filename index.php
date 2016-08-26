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
 define("DATACONF",dirname(__FILE__)."/appcore/dataconnect/connect.ini");
 require dirname(__FILE__)."/appcore/user.inc.php";
 $settings=new DataBaseTable('settings',true,DATACONF);
 $query=$settings->getData();
 $rows=$query->fetchAll(PDO::FETCH_ASSOC);
 foreach ($rows as $item)
 {
  $GLOBALS['config']["{$item['key']}"]=$item['value'];
 }
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
{?>
<head>
 <title>Tower21 WebComiX: <?php echo($path) ?></title>
</head>
<body>
 <?php var_dump(basename($path)); ?>
</body>
<?php }

function load_page($page,$path)
{
 var_dump($page);
}
?>
</html>
<?php
