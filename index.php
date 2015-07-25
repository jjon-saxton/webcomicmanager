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

if (!file_exists(dirname(__FILE__)."/appcore/dataconnect/connect.ini"))
{
 header("Location: ./app/dataconnect/install");
}
elseif (@$action == "install")
{
 define("USR","installer");
}
else
{
 require dirname(__FILE__)."/appcore/user.inc.php";
}

switch (@$section)
{
 case 'app':
 break;
 case 'profile':
 break;
 default: //path is actually an item path
 if (!@$_GET['page'])
 {
  load_index($path);
 }
 else
 {
  load_page($_GET['page'],$page);
 }
}

function load_index($path)
{
 var_dump(basename($path));
}

function load_page($page,$path)
{
 var_dump($page);
}


