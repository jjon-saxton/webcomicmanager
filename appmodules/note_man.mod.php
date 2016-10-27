<?php

function build_manager_form($curusr,$action)
{
  $types=new DataBaseTable('types',true,DATACONF);
  $tq=$types->getData("ctype:`note`");
  $qstr="section={$action}";
  $type_opts=null;
  $siteroot=SITEROOT;
  if ($action == 'edit' && !empty($_GET['nid']))
  {
    $nt=new DataBaseTable('notes',true,DATACONF);
    $nq=$nt->getData("nid:`= {$_GET['nid']}`");
    $values=$nq->fetch(PDO::FETCH_ASSOC);
    $values['modified']=date("Y-m-d H:i:s");
    $qstr=$qstr."&nid={$values['nid']}";
  }
  else
  {
    $values['uid']=$curusr->uid;
    $values['cid']=$_GET['cid'];
    $values['created']=date("Y-m-d H:i:s");
    $values['modified']=null;
    $values['note']="<p>Write your note here...</p>";
  }
  
  while ($type=$tq->fetch(PDO::FETCH_ASSOC))
  {
    $selected=null;
    if ($type['ttid'] == $values['ttid'])
    {
      $selected=" selected=\"selected\"";
    }
    $type_opts.="<option{$selected} value=\"{$type['ttid']}\">{$type['name']}</option>\n";
  }
  
  return <<<HTML
<form action="{$siteroot}dash/?{$qstr}" method="post">
<div class="form-group">
<label for="title">Note Title</label>
<input type="hidden" name="uid" value="{$values['uid']}">
<input type="hidden" name="cid" value="{$values['cid']}">
<input type="hidden" name="created" value="{$values['created']}">
<input type="hidden" name="modified" value="{$values['modified']}">
<input id="title" class="form-control" name="title" value="{$values['title']}">
</div>
<div class="form-group">
<label for="ttid">Type</label>
<select class="form-control" name="ttid">
{$type_opts}
</select>
</div>
<div class="form-group">
<label for="note">Note Text (full formatting available)</label>
<textarea class="form-control full editor" name="note">
{$values['note']}
</textarea>
</div>
<div class="form-group center">
<button class="btn btn-primary" type="button" data-target="#messageModal" name="save" value="1">Save</button>
<a href="{$siteroot}view/?type=note&id={$_GET['nid']}" class="btn btn-success" target="_new">View (in new tab)</a>
<a href="{$siteroot}dash/?section=projects" class="btn btn-danger" data-target="#this-modal">Cancel</a>
</div>
HTML;
}

function save_note($action,$data)
{
  $nt=new DataBaseTable('notes',true,DATACONF);
  if ($action == 'edit' && !empty($_GET['nid']))
  {
    $data['nid']=$_GET['nid'];
    if ($nid=$nt->updateData($data))
    {
      return $nid;
    }
    else
    {
      return false;
    }
  }
  elseif ($action != "remove")
  {
    if ($nid=$nt->putData($data))
    {
      return $nid;
    }
    else
    {
      return false;
    }
  }
  else
  {
    if ($nid=$nt->deleteData($data))
    {
      return $nid;
    }
    else
    {
      return false;
    }
  }
}