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

require dirname(__FILE__)."/appmodules/ucp.mod.php";
echo ucp_module($GLOBALS['CURUSR'],'box');

switch (@$section)
{
 case 'app':
 switch ($action)
 {
  case 'install':
  break;
  case 'login':
  if (@$_POST['do'] == "register")
  {
   header("Location: ./?section=app&action=register&name={$_POST['name']}");
  }
  if (isset($_POST['password']) && isset($_POST['name']))
  {
   if ($GLOBALS['CURUSR']->login($_POST['name'],$_POST['password']))
   {
    $_SESSION['data']=serialize($GLOBALS['CURUSR']);
    header("Location: ./");
   }
   else
   {
    echo "Unauthorized!"; //TODO replace with actual 401 error
   }
  }
  break;
  case 'register':
  if (!empty($_REQUEST['name']))
  {
   $supplied_name=$_REQUEST['name'];
  }
  else
  {
   $supplied_name=null;
  }
  if (!empty($_POST['do']))
  {
   $data=$_POST; //Put post into a array we can work with
   $data['level']=7;
   if (!empty($data['pass2']) && ($data['pass2'] == $data['pass1']))
   {
    $data['password']=crypt($data['pass2'],"MC");
   }
   else
   {
    trigger_error("No Password supplied, or passwords do not match",E_USER_ERROR);
   }
   $nu=new MCUser();
   if ($nu=$nu->nu($data))
   {
    header("Location: ./?section=app&action=login&name={$_POST['name']}");
   }
   else
   {
    echo "No user created!"; //TODO replace with actual error
   }
  }
  else
  {
   echo <<<HTML
<form action="./?section=app&action=register" method=post>
<h2 class="title form">Register a New Account</h2>
<ul class="nobullet noindent">
<li>User Name: <input type=text required=required name="name" value="{$supplied_name}"></li>
<li>You E-Mail Address: <input type=email required=required name="email"></li>
<li>Password: <input type=password required=required name="pass1"></li>
<li>Confirm Password: <input type=password required=required name="pass2"></li>
<li><button name=do value="register">Register</button></li>
</ul>
</form>
HTML;
  }
  break;
  case 'logout':
  if ($GLOBALS['CURUSR']->logout())
  {
   $_SESSION['data']=serialize($GLOBALS['CURUSR']);
   header("Location: ./");
  } 
 }
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


