<?php

session_name("MC");
session_start();

if (@$_SESSION['data'])
{
 $GLOBALS['CURUSR']=unserialize($_SESSION['data']);
}
else
{
 $GLOBALS['CURUSR']=new MCSession();
 $_SESSION['data']=serialize($GLOBALS['CURUSR']) ;
}
