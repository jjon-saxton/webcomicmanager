<?php

function build_manager_form($curusr,$action)
{
  switch ($action)
  {
   case 'upload':
   $type=new DataBaseTable('types',true,DATACONF);
   $tq=$type->getData("ctype:`art`");
   $type_opts=null;
   while ($tr=$tq->fetch(PDO::FETCH_ASSOC))
   {
    $type_opts.="<option value=\"{$tr['ttid']}\">{$tr['name']}</option>\n";
   }
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
<label for="type">Type</label>
<select id="type" class="form-control" name="ttid">
{$type_opts}
</select>
</div>
<div class="form-group">
<label for="file">File</label>
<div class="input-group">
<label class="input-group-btn">
<span class="btn btn-primary">
Choose File <input id="file" type="file" style="display:none" name="art">
</span>
</label>
<input type="text" class="form-control" disabled="disabled">
</div>
<div id="art" class="progress no-show">
<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin=0 aria-valuemax="100">
<span class="sr-only">0%</span>
</div>
<input type="hidden" id="uriTarget" name="temp_uri">
</div>
</div>
<div class="form-group center">
<button class="btn btn-primary" disabled=disabled type="button" data-target="#messageModal" name="save" value="1">Save</button>
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

function add_art($action,$data)
{
}

function upload_file($file,$site_settings)
{
  if ($file['error'] == UPLOAD_ERR_OK)
  {
    $temp=$site_settings->project_dir."/temp/".md5(time());
    if(move_uploaded_file($file['tmp_name'],$temp))
    {
      $upload_script=<<<TXT
$("#art .progress-bar span",pDoc).removeClass('sr-only').text("Complete!");
$("#art .progress-bar",pDoc).addClass("progress-bar-success").attr("aria-valuenow","100").css("width","100%");
$("input#uriTarget",pDoc).value("{$temp}");
$("button[name=save]",pDoc).removeAttr('disabled');
TXT;
    }
    else
    {
      $upload_script=<<<TXT
$("#art .progress-bar span",pDoc).removeClass('sr-only').text("Could not stage file");
$("#art .progress-bar",pDoc).addClass("progress-bar-danger").attr("aria-valuenow","100").css("width","100%");
TXT;
    }
  }
  return <<<HTML
<!doctype html>
<html>
<head>
<title>Tower21 WebComiX uploader</title>
<!-- Load jQuery -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<!-- Feedback Script -->
<script language="javascript" type="text/javascript">
$(document).ready(function(){
var pDoc=window.parent.document;
$("#art .progress-bar span",pDoc).text("67%");
$("#art .progress-bar",pDoc).attr("aria-valuenow","66").css("width","67%");

{$upload_script}
});
</script>
</head>
<body>
<p>You really shouldn't be seeing this...</p>
</body>
</html>
HTML;
}