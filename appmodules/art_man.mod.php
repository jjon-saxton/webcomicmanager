<?php

function build_manager_form($curusr,$action)
{
  $type=new DataBaseTable('types',true,DATACONF);
  $siteroot=SITEROOT;
  switch ($action)
  {
   case 'upload':
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
<form action="{$siteroot}dash/?section=upload" method="post" target="file-target" enctype="multipart/form-data">
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
<input type="hidden" id="uriTarget" name="temp_name">
</div>
</div>
<div class="form-group center">
<button class="btn btn-primary" disabled=disabled type="button" data-target="#messageModal" name="save" value="1">Save</button>
<a href="{$siteroot}dash/?section=projects" class="btn btn-danger" data-target="#this-modal">Cancel</a>
</div>
</form>
<iframe name="file-target" class="no-show"></iframe>
HTML;
   break;
   case 'remove':
   break;
   case 'view':
   default:
   /*Art Info*/
   $art=new DataBaseTable('art',true,DATACONF);
   $art=$art->getData("aid:`= {$_GET['aid']}`");
   $art=$art->fetch(PDO::FETCH_OBJ);
   
   /*Author Info*/
   $users=new DataBaseTable('users',true,DATACONF);
   $author=$users->getData("uid:`= {$art->uid}`");
   $author=$author->fetch(PDO::FETCH_ASSOC);
   $author=$author['name'];
   
   /*Parent Info*/
   $content=new DataBaseTable('content',true,DATACONF);
   $parent=$content->getData("cid:`= {$art->cid}`");
   $parent=$parent->fetch(PDO::FETCH_ASSOC);
   $parent=$parent['title'];
   
   /*Type Info*/
   $tq=$type->getData("ttid:`= {$art->ttid}`");
   $tinfo=$tq->fetch(PDO::FETCH_ASSOC);
   $tname=$tinfo['name'];
   return <<<HTML
   <div id="View-{$art->aid}">
   <a href="{$siteroot}{$art->uri}" target="_new" title="open '{$art->title}' in new tab"><img src="{$siteroot}{$art->uri}?type=image/png&w=300" width="300" align="left" alt="{$art->title}">
   <h4>{$art->title}</h4></a>
   <ul class="nobullet noindent">
   <li><strong>Created: </strong>{$art->created}</li>
   <li><strong>Modified: </strong>{$art->modified}</li>
   <li><strong>Created by: </strong>{$author}</li>
   <li><strong>Type: </strong>{$tname}</li>
   <li><strong>Linked to: </strong>{$parent}</li>
   </ul>
   </div>
   <div class="center">
   <a href="{$siteroot}dash/?section=projects" class="btn btn-primary" data-target="#this-modal">Back</a>
   <button data-dismiss="modal" class="btn btn-info">Close Dialog</button>
   </div>
HTML;
  }
}

function add_art($action,$data,$site_settings)
{
  $raw=file_get_contents($site_settings->project_dir."/".$data['temp_name']);
  unlink($site_settings->project_dir."/".$data['temp_name']);
  $image=imagecreatefromstring($raw);
  //TODO crop and/or resize image base on ttid?
  
  $user=new DataBaseTable("users",true,DATACONF);
  $user=$user->getData("uid:`= {$data['uid']}`");
  $user=$user->fetch(PDO::FETCH_ASSOC);
  
  if (!is_dir($site_settings->project_dir."/".storagename($user['name'])))
  {
    mkdir($site_settings->project_dir."/".storagename($user['name']));
  }
  $file=storagename($user['name'])."/".storagename($data['title']).".png";
  
  $art=new DataBaseTable("art",true,DATACONF);
  
  if (imagepng($image,$site_settings->project_dir."/".$file,9))
  {
    $data['uri']=$file;
    
    if ($aid=$art->putData($data))
    {
      return $aid." added successfully!"; 
    }
    else
    {
      return $aid." could not be added!";
    }
  }
  else
  {
    return $data['temp_name']." could not be opened for processing!";
  }
}

function upload_file($file,$site_settings,$curusr=null,$type=null)
{
  if ($file['error'] == UPLOAD_ERR_OK)
  {
    $temp=$site_settings->project_dir."/temp/".md5(time());
    if(move_uploaded_file($file['tmp_name'],$temp))
    {
      $temp_name="temp/".basename($temp);
      $upload_script=<<<TXT
$("#art .progress-bar span",pDoc).removeClass('sr-only').text("Complete!");
$("#art .progress-bar",pDoc).addClass("progress-bar-success").attr("aria-valuenow","100").css("width","100%");
$("input#uriTarget",pDoc).val("{$temp_name}");
$("button[name='save']",pDoc).removeAttr('disabled');
TXT;
    }
    else
    {
      $upload_script=<<<TXT
$("#art .progress-bar span",pDoc).removeClass('sr-only').text("Could not stage file as {$temp}!");
$("#art .progress-bar",pDoc).addClass("progress-bar-danger").attr("aria-valuenow","100").css("width","100%");
TXT;
    }
    
    if ($type == "panel")
    {
     if (!empty($curusr))
     {
       $panel=uniquename($site_settings->project_dir."/".storagename($curusr->name)."/",5);
       rename($temp,$panel);
       $file=SITEROOT.storagename($curusr->name)."/".basename($panel);
     }
     else
     {
       $file=SITEROOT.$temp_name;
     }
    }
    
    $upload_script.=<<<TXT
$(".canvas-asset form",pDoc).remove();
$(".canvas-asset:not(:has(img))",pDoc).append('<img src="{$file}?w=1280" style="width:100%" alt=\"New Asset\">');
TXT;
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