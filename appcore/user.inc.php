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

class MCSession
{
 private $usr;

 public function __construct()
 {
  $this->usr=new MCUser();
 }

 public function __get($key)
 {
  return $this->usr->$key;
 }

 public function login($name,$password)
 {
  $new_usr=new MCUser($name);
  $password=crypt($password,$new_usr->password);

  if ($password == $new_usr->password)
  {
   $this->usr=$new_usr;
   return $new_usr->id;
  }
  else
  {
   return false;
  }
 }

 public function check_auth($req_level)
 {
  $usr_level=$this->usr->level;

  if ($usr_level <= $req_level)
  {
   return true;
  }
  else
  {
   return false;
  }
 }

 public function logout()
 {
  $this->usr=new MCUser();
  return true;
 }
}

class MCUser
{
 private $usr;

 public function __construct($name='guest')
 {
  $info=$this->su($name);
  var_dump($info);
  $this->usr=$info;
 }

 public function __get($key)
 {
  return $this->usr[$key];
 }

 public function __set($key,$val)
 {
  return $this->mu($key,$val);
 }

 public function su($name)
 {
  $datatab=new DataBaseTable('users');
  $user=$datatab->getData("name:'{$name}'");
  return $user->fetch(PDO::FETCH_ASSOC);
 }

 public function mu($setting,$value)
 {
  //TODO change user info in database
 }
}
