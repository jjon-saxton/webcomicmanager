<?php

function build_manager_form(MCSession $session,$action,$ctype=null,$cid=null)
{
  $html="<h1>Asset Manager: {$action} {$ctype}</h1>\n";
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
    }
    
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
<form method="post" enctype="multipart/form-data">
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
<div class="form-group">
<button class="form-control btn btn-primary" type="submit" name="save" value=1>Save</button>
</div>
</form>
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
     return $cid;
   }
   else
   {
     return false;
   }
  }
  else
  {
    if ($cid=$con->putData($data))
    {
      return $cid;
    }
    else
    {
      return false;
    }
  }
}