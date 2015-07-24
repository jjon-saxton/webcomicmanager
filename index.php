<?php
require dirname(__FILE__)"/appcore/dataconnect/database.inc.php";
require dirname(__FILE__)"/appcore/user.inc.php";

if (!empty($_SERVER['PATH_INFO'])) //Are we forwarding from 'pretty' urls?
{
 @list($section,$item,$action)=explode("/",ltrim($_SERVER['PATH_INFO'],"/")); //q holds a path, we need to split it!
}
else //we are going direct from the query string!
{
 foreach ($_GET as $var=>$val) //Parse through the $_GET array and set variables for each named key
 {
  ${$var}=$val;
 }
}

