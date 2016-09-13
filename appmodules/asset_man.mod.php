<?php

function build_manager_form(MCSession $session,$action,$ctype=null,$pid=null,$cid=null)
{
  $con=new DataBaseTable('content',true,DATACONF);
  if ($action != 'drop')
  {
    $types=new DataBaseTable('types',true,DATACONF);
    
    if ($action == 'update' && $cid != NULL)
    {
      $q=$con->getData("cid:`{$cid}`");
      $values=$q->fetch(PDO::FETCH_ASSOC);
      $values['modified']=date("Y-m-d H:i:s");
      $child_btn=<<<HTML
<a href="./dash.php?section=put&pid={$values['cid']}" class="btn btn-success" data-target="#this-modal">Add Child</a>
HTML;
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
     if (empty($pid))
     {
       $values['pid']=0;
     }
     else
     {
       $values['pid']=$pid;
     }
     $child_btn=null;
     $values['created']=date("Y-m-d H:i:s");
     $values['modified']=null;
     $values['title']="New ".ucwords($ctype);
     $values['uid']=$session->uid;
     $values['tags']=null;
     $values['ttid']=null;
     $values['price']=0;
     $values['data']="<p>Your text here...</p>";
     $values['file']=null;
     
     $qstr="?section={$action}&type={$ctype}";
    }
  
    
    if (empty($ctype) && !empty($pid))
    {
      $parent=$con->getData("cid:`= {$pid}`",array('ttid'));
      $parent=$parent->fetch(PDO::FETCH_ASSOC);
      $ptype=$types->getData("ttid:`= {$parent['ttid']}`");
      $ptype=$ptype->fetch(PDO::FETCH_ASSOC);
      $ctype=$ptype['child_types'];
    }
    if (empty($ctype))
    {
      $ctype="project";
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
      case 'note':
      $type_extras=<<<HTML
<div class="form-group">
<label for="note">Note</label>
<textarea id="note" name="data" class="full editor">
{$values['data']}
</textarea>
</div>
HTML;
      break;
      case 'art':
      break;
      case 'page':
      $type_extras=<<<HTML
<div class="form-group">
<label for="script">Script</label>
<textarea id="script" name="data" class="script editor form-control">
{$values['data']}
</textarea>
</div>
HTML;
      break;
      case 'section':
      case 'chapter':
      case 'project':
      default:
      $type_extras=<<<HTML
<div class="form-group">
<label for="description">Description</label>
<textarea id="description" name="data" class="limited editor form-control">
{$values['data']}
</textarea>
</div>
HTML;
    }
    
    $html.=<<<HTML
<form action="./dash.php{$qstr}" method="post" enctype="multipart/form-data">
<div class="form-group">
<label for="title">Title</label>
<input type="hidden" name="pid" value="{$values['pid']}">
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
<button class="btn btn-primary" type="button" data-target="#messageModal" name="save" value="1">Save</button>
{$child_btn}
<a href="./dash.php?secton=projects" class="btn btn-danger" data-target="#this-modal">Cancel</a>
</div>
</form>
HTML;

   if ($action == 'update')
   {
    $children=$con->getData("pid:`= {$values['cid']}`");
    $cc=0;
    $cdiv=null;
    while ($crow=$children->fetch(PDO::FETCH_ASSOC))
    {
     $cdiv.=con_to_html($crow);
     $cc++;
    }
    
    if ($cc > 1)
    {
     $html.="<div class=\"panel-group\">\n{$cdiv}\n</div>\n";
    }
    elseif ($cc == 1)
    {
     $html.=$cdiv;
    }
   }
  }
  else
  {
    $html=<<<HTML
<form action="./dash.php?section=drop&cid={$cid}" method="post">
<div class="panel panel-danger">
<div class="panel-heading">Are you sure?</div>
<div class="panel-body">Are you really sure you want to drop this item? This action cannot be undone no matter how much you complain or wine</div>
<div class="panel-footer">
<input type="hidden" name="cid" value="{$_GET['cid']}">
<button type="button" class="btn btn-danger" data-target="#messageModal" name="confirm" value="1">Yes</button>
<a href="./dash.php?section=projects" class="btn btn-info" data-target="#this-modal">No</a>
</div>
</div>
HTML;
  }
  
  return $html;
}

function save_asset($action,$data)
{
  $con=new DataBaseTable('content',true,DATACONF);
  //TODO process tags to tag associations
  //file uploads should be handled elsewhere...
  if ($action == 'drop')
  {
    if ($cid=$con->deleteData($data))
    {
      return $cid." dropped";
    }
    else
    {
      return $cid. "could not be removed!";
    }
  }
  elseif (!empty($_GET['cid']))
  {
   $data['cid']=$_GET['cid'];
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