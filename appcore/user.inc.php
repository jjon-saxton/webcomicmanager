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
   return $new_usr->num;
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
 private $table;

 public function __construct($name='guest')
 {
  $this->table=new DataBaseTable('users');
  $info=$this->su($name);
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

 public function __sleep()
 {
  return array('usr');
 }

 public function __wakeup()
 {
  $this->table=new DataBaseTable('users');
 }

 public function nu(array $data)
 {
  if ($nu=$this->table->putData($data))
  {
   return $nu;
  }
  else
  {
   return false;
  }
 }

 private function su($name)
 {
  $datatab=$this->table;
  $user=$datatab->getData("name:'{$name}'");
  return $user->fetch(PDO::FETCH_ASSOC);
 }

 private function mu($col,$value)
 {
  $data[$col]=$value;
  $data['num']=$this->usr['num'];
  
  return $this->table->updateData($data);
 }
}
