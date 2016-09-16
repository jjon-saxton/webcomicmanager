<?php

function build_manager_form($curusr,$action)
{
  switch ($action)
  {
   case 'upload':
   $values['uid']=$curusr->uid;
   if (!empty($_GET['cid']))
   {
    $values['cid']=$_GET['cid'];
   }
   else
   {
     $values['cid']=1;
   }
   $values['created']=date("Y-m-d H:i:s");
   return <<<HTML
<form action="./dash.php?section=upload" method="post" target="file-target" enctype="multipart/form-data">
<div class="form-group">
<label for="title">Title</label>
<input type="hidden" name="uid" value="{$values['uid']}">
<input type="hidden" name="cid" value="{$values['cid']}">
<input type="hidden" name="created" value="{$values['created']}">
<input id="title" type="text" class="form-control" name="title">
</div>
<div class="form-group">
<label for="file">File</label>
<div class="input-group">
<label class="input-group-btn">
<span class="btn btn-primary">
Browse... <input id="file" type="file" style="display:none" name="art">
</span>
</label>
<input type="text" class="form-control" disabled="disabled">
</div>
</div>
<div class="form-group center">
<button class="btn btn-primary" type="submit" name="save" value="1">Save</button>
<a href="./dash.php?section=projects" class="btn btn-danger" data-target="#this-modal">Cancel</a>
</div>
</form>
<iframe name="file-target" class="no-show"></iframe>
HTML;
   break;
   case 'remove':
   break;
   case 'view':
   default:
  }
}

function upload_art($action,$data,$files)
{
  var_dump($files);
}