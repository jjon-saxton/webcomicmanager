<?php

function build_manager_form(MCSession $session,$action,$ctype=null,$cid=null)
{
  if ($action != 'drop')
  {
    $types=new DataBaseTable('types',true,DATACONF);
    
    if ($action == 'update' && $cid != NULL)
    {
      $con=new DataBaseTable('content',true,DATACONF);
      $q=$con->getData("cid:`{$cid}`");
      $values=$q->fetch(PDO::FETCH_ASSOC);
      $values['modified']=date("Y-m-d H:i:s");
      if (empty($ctype))
      {
        $cttid=$types->getData("ttid:`= {$values['ttid']}`");
        $cttid=$cttid->fetch(PDO::FETCH_ASSOC);
        $ctype=$cttid['ctype'];
      }
      $qstr="?section={$action}&cid={$cid}";
    }
    else
    {
     $ttid_opts.="</select>\n";
     $values['created']=date("Y-m-d H:i:s");
     $values['modified']=null;
     $values['title']="New ".ucwords($ctype);
     $values['uid']=$session->uid;
     $values['tags']=null;
     $values['ttid']=null;
     $values['price']=0;
     $values['data']=null;
     $values['file']=null;
     
     $qstr="?section={$action}&type={$ctype}";
    }
  
    $html="<h4>{$action} {$ctype}</h4>\n";
    
    $ttids=$types->getData("ctype:`{$ctype}`");
    $ttid_opts="<select class=\"form-control\" id=\"ttid\" name=\"ttid\">\n";
    while ($type=$ttids->fetch(PDO::FETCH_ASSOC))
    {
      if ($type['ttid'] == $values['ttid'])
      {
        $tval=" selected=\"selected\"";
      }
      else
      {
        $tval=null;
      }
      $ttid_opts.="<option{$tval} value=\"{$type['ttid']}\">{$type['name']}</option>\n";
    }
    $ttid_opts.="</select>\n";
    
    switch ($ctype)
    {
      //TODO other ctypes
      case 'project':
      default:
      $type_extras=<<<HTML
<div class="form-group">
<label for="description">Description</label>
<input type="hidden" name="pid" value="0">
<textarea id="description" name="data" rows="5" cols="15">
{$values['data']}
</textarea>
</div>
HTML;
    }
    
    $html.=<<<HTML
<form action="./dash.php{$qstr}" method="post" enctype="multipart/form-data">
<div class="form-group">
<label for="title">Title</label>
<input type="hidden" name="uid" value="{$values['uid']}">
<input type="hidden" name="created" value="{$values['created']}">
<input type="hidden" name="modified" value="{$values['modified']}">
<input type="text" class="form-control" maxlength="160" id="title" name="title" value="{$values['title']}">
</div>
<div class="form-group">
<label for="price">Price</label>
<input type="number" size="3" maxlength="5" class="form-control" id="price" name="price" value="{$values['price']}">
</div>
<div class="form-group">
<label for="tags">Tags</label>
<input type="text" class="form-control" id="tags" name="tags" value="{$values['tags']}">
</div>
<div class="form-group">
<label for="ttid">Type</label>
{$ttid_opts}
</div>
<div id="Preferences">
{$type_extras}
</div>
<div class="form-group center">
<button class="btn btn-primary" type="submit" name="save" value="1">Save</button>
<a href="./dash.php?secton=projects" class="btn btn-danger" data-target="#this-modal">Cancel</a>
</div>
</form>
HTML;
  }
  else
  {
    $html=<<<HTML
<form action="./dash.php?section=drop&cid={$cid}" method="post">
<div class="panel panel-danger">
<div class="panel-heading">Are you sure?</div>
<div class="panel-body">Are you really sure you want to drop this item? This action cannot be undone no matter how much you complain or wine</div>
<div class="panel-footer">
<button type="submit" class="btn btn-danger">Yes</button>
<a href="./dash.php?section=projects" class="btn btn-info" data-target="#this-modal">No</a>
</div>
</div>
HTML;
  }
  
  return $html;
}

function save_asset($data)
{
  $con=new DataBaseTable('content',true,DATACONF);
  //TODO process tags to tag associations
  //file uploads should be handled elsewhere...
  if (!empty($data['modified']))
  {
   if ($cid=$con->updateData($data))
   {
     return $cid." successfully updated.";
   }
   else
   {
     return $cid." could not be updated.";
   }
  }
  else
  {
    if ($cid=$con->putData($data))
    {
      return $cid." successfully added.";
    }
    else
    {
      return $cid." could not be added.";
    }
  }
}