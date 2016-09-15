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
   $con=new DataBaseTable('content',true,DATACONF);
   $cq=$con->getData("pid:`= {$row['cid']}`");
   $cli=null;
   while ($child=$cq->fetch(PDO::FETCH_ASSOC))
   {
    $cli.=con_to_html($child,'dropdown');
   }
   if (!empty($cli))
   {
    $cli="<li class=\"divider\"></li>\n".$cli;
   }
   $note=new DataBaseTable('notes',true,DATACONF);
   $nq=$note->getData("cid:`= {$row['cid']}`");
   $nli=null;
   while ($note=$nq->fetch(PDO::FETCH_ASSOC))
   {
    $nli.=note_to_html($note,'dropdown');
   }
   if (!empty($nli))
   {
    $nli="<li class=\"divider\"></li>\n".$nli;
   }
   
   $art=new DataBaseTable('art',true,DATACONF);
   $aq=$art->getData("cid:`= {$row['cid']}`");
   $ali=null;
   while ($art=$aq->fetch(PDO::FETCH_ASSOC))
   {
    $ali.=art_to_html($art,'dropdown');
   }
   if (!empty($ali))
   {
    $ali="<li class=\"divider\"></li>\n".$ali;
   }
   return <<<HTML
<div id="{$row['cid']}" class="panel panel-default">
<div class="panel-heading">{$row['title']}</div>
<div class="panel-body">{$row['data']}</div>
<div class="panel-footer"><a href="./dash.php?section=update&cid={$row['cid']}" class="btn btn-info" data-target="#this-modal">Edit</a>
<div id="{$row['cid']}-Art" class="dropdown no-box">
<button class="btn btn-info" type="button" data-toggle="dropdown">Artwork <span class="caret"></span></button>
<ul class="dropdown-menu">
<li><a href="./dash.php?section=upload&cid={$row['cid']}" data-target="#this-modal">Add Artwork</a></li>
{$ali}
</ul>
</div>
<div id="{$row['cid']}-Children" class="dropdown no-box">
<button class="btn btn-success" type="button" data-toggle="dropdown">Children <span class="caret"></span></button>
<ul class="dropdown-menu">
<li><a href="./dash.php?section=put&pid={$row['cid']}" data-target="#this-modal">Add Child</a></li>
{$cli}
</ul>
</div>
<div id="{$row['cid']}-Notes" class="dropdown no-box">
<button class="btn btn-warning" type="button" data-toggle="dropdown">Notes <span class="caret"></span></button>
<ul class="dropdown-menu">
<li><a href="./dash.php?section=write&cid={$row['cid']}" data-target="#this-modal">Add Note</a></li>
{$nli}
</ul>
</div>
<a href="./dash.php?section=drop&cid={$row['cid']}" class="btn btn-danger" data-target="#this-modal">Delete</a>
</div>
</div>
HTML;
  }
}

function art_to_html($row,$view=null)
{
  switch ($view)
  {
   case 'dropdown':
   return <<<HTML
<li><a href="./dash.php?section=view&type=art&aid={$row['aid']}" data-target="#this-modal">{$row['title']}</a></li>
HTML;
   break;
   //TODO default single view
 }
}

function note_to_html($row,$view=null)
{
  switch ($view)
  {
   case 'dropdown':
   return <<<HTML
<li><a href="./dash.php?section=edit&nid={$row['nid']}" data-target="#this-modal">{$row['title']}</a></li>
HTML;
   break;
   //TODO default single view
 }
}