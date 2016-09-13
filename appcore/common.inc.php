<?php
define("DATACONF",dirname(__FILE__)."/dataconnect/connect.ini");
require_once dirname(__FILE__)."/dataconnect/database.inc.php";
require dirname(__FILE__)."/user.inc.php";

class MCSettings
{
 private $temp=array();
 private $table;
 
 public function __construct()
 {
  $this->table=new DataBaseTable('settings',true,DATACONF);
  $this->dataini=DATACONF;
  /*extra settings if needed*/
  
  return $this->table;
 }
 
 public function __get($set)
 {
  $q=$this->table->getData("key: `{$set}`",array('value'),null,1);
  $set=$q->fetch(PDO::FETCH_ASSOC);
  if (array_key_exists($key,$this->temp))
  {
   return $this->temp[$key];
  }
  elseif (!empty($set['value']))
  {
   return $set['value'];
  }
  else
  {
   return false;
  }
 }
 
 public function __set($key,$value)
 {
   return $this->temp[$key]=$value;
 }
 
 public function get($as='array')
 {
  $q=$this->table->getData();
  $settings=array();
  while ($set=$q->fetch(PDO::FETCH_ASSOC))
  {
    $settings[$set['key']]=$set['value'];
  }
  $settings=array_merge($settings,$this->temp);
  
  switch ($as)
  {
   //TODO other formats?
   case 'array':
   default:
   return $settings;
  }
 }
 
 public function saveTemp()
 {
  $status=array();
  foreach ($this->temp as $key=>$value)
  {
   $data['key']=$key;
   $data['value']=$value;
   if ($query=$this->table->getData("key:`{$key}`",null,null,1))
   {
    $set=$query->fetch(PDO::FETCH_ASSOC);
    if (!empty($set['key']))
    {
     $status[$key]=$this->table->updateData($data) or die(trigger_error("Setting {$key} could not be updated!",E_USER_ERROR));
    }
    else
    {
     $status[$key]=$this->table->putData($data) or die(trigger_error("Setting {$key} could not be updated!",E_USER_ERROR));
    }
   }
   else
   {
    $status[$key]=$this->table->putData($data);
   }
  }
  $this->temp=array(); //empty temp array
  return $status;
 }
}

function con_to_html(array $row,$view=null)
{
  switch ($view)
  {
   case 'dropdown':
   return <<<HTML
<li><a href="./dash.php?section=update&cid={$row['cid']}" data-target="#this-modal">{$row['title']}</a></li>
HTML;
   break;
   case 'panel':
   default:
   return <<<HTML
<div id="{$crow->cid}" class="panel panel-default">
<div class="panel-heading">{$row['title']}</div>
<div class="panel-body">{$row['data']}</div>
<div class="panel-footer"><a href="./dash.php?section=update&cid={$row['cid']}" class="btn btn-info" data-target="#this-modal">Edit</a>
<a href="./dash.php?section=put&pid={$row['cid']}" class="btn btn-success" data-target="#this-modal">Add Child</a>
<a href="./dash.php?section=put&type=note&pid={$row['cid']}" class="btn btn-warning" data-target="#this-modal">Add Note</a>
<a href="./dash.php?section=drop&cid={$row['cid']}" class="btn btn-danger" data-target="#this-modal">Delete</a>
</div>
</div>
HTML;
  }
}