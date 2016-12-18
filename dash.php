<?php
if (!file_exists(dirname(__FILE__)."/appcore/dataconnect/connect.ini"))
{
 header("Location: ./app.php?action=install");
}
else
{
 require_once dirname(__FILE__)."/appcore/common.inc.php";
 $conf=new MCSettings();
 define ("SITEROOT","//".$conf->base_uri."/");
}

if ($session->level > 4)
{
 $title="403 Forbidden";
 $body=<<<HTML
<h1>Forbidden!</h1>
<p>You must be <a href="//{$conf->base_uri}/app.php?action=login">logged in</a> to view this section!</p>
HTML;
}
else
{
 switch ($_GET['section'])
 {
  case 'write':
  case 'edit':
  case 'delete':
  require_once dirname(__FILE__)."/appmodules/note_man.mod.php";
  $title="Note Manager";
  if (empty($_POST['cid']))
  {
    $body=build_manager_form($session,$_GET['section']);
  }
  else
  {
    if (($id=save_note($_GET['section'],$_POST)))
    {
      $message="Note {$id} successfully saved!";
      $success=true;
    }
    else
    {
      $message="Note could not be saved due to a script or server error!";
      $success=false;
    }
    $body="<div class=\"panel panel-default\">Operation complete! {$message} <a href=\"{$siteroot}/dash/?section={$_GET['type']}\" data-dismiss=\"modal\" data-target=\"#AJAXModal\" data-toggle=\"modal\">Return to project manager</a></div>";
  }
  break;
  case 'view':
  case 'upload':
  case 'remove':
  require_once dirname(__FILE__)."/appmodules/art_man.mod.php";
  $title="Upload/View Art";
  if (!empty($_POST['temp_name']))
  {
    if ($message=add_art($_GET['section'],$_POST,$conf))
    {
      $success=true;
      $body="<div class=\"panel panel-default\">Operation complete! {$message} <a href=\"{$siteroot}/dash/?section={$_GET['type']}\" data-dismiss=\"modal\" data-target=\"#AJAXModal\" data-toggle=\"modal\">Return to project manager</a></div>";
    }
  }
  elseif (!empty($_FILES['art']['name']))
  {
    echo upload_file($_FILES['art'],$conf,$session,$_GET['type']);
    exit();
  }
  else
  {
    $body=build_manager_form($session,$_GET['section']);
  }
  break;
  case 'put':
  case 'update':
  case 'drop':
  require_once dirname(__FILE__)."/appmodules/asset_man.mod.php";
  $title="Asset Manager";
  if (empty($_POST['title']) && empty($_POST['cid']))
  {
    $body=build_manager_form($session,$_GET['section'],$_GET['type'],$_GET['pid'],$_GET['cid']);
  }
  else
  {
    if (($id=save_asset($_GET['section'],$_POST)))
    {
      $success=true;
      $message="Asset {$id} saved!";
    }
    else
    {
      $success=false;
      $message="Asset could not be saved due to a script or server error!";
    }
    $body="<div class=\"panel panel-default\">Operation complete! {$message} <a href=\"//{$conf->base_uri}/dash?section={$_GET['type']}\" data-dismiss=\"modal\" data-target=\"#AJAXModal\" data-toggle=\"modal\">Return to project manager</a></div>";
  }
  break;
  case 'saveset':
  $title="Saving settings...";
  $table=new DataBaseTable('settings',true,DATACONF);
  $okay=true;
  foreach ($_POST as $key=>$value)
  {
   $data['key']=$key;
   $data['value']=$value;
   if (!$row=$table->updateData($data))
   {
     $okay=false;
   }
  }
  if ($okay == true)
  {
   $body="<div class=\"alert alert-info\">Site settings saved.</div>";
  }
  else
  {
   $body="<div class=\"alert alert-danger\">A fetal error occured while attempting to save your settings.</div>";
  }
  break;
  case 'admincp':
  $title="Your Site";
  $body=null;
  $table=new DataBaseTable('settings',true,DATACONF);
  $q=$table->getData();
  if ($q instanceof PDOStatement)
  {
    $settings=array();
    while ($row=$q->fetch(PDO::FETCH_ASSOC))
    {
      $settings[$row['key']]=$row['value'];
    }
    $body.="<form action=\"".SITEROOT."/dash/?section=saveset\" method=\"post\">\n";
    foreach ($settings as $key=>$value)
    {
      $label=ucwords(str_replace("_"," ",$key));
      if ($key == 'guest_views' || $key == 'open_registration')
      {
       if ($value == 'y')
       {
        $body.="<div class=\"form-group\">\n<label for=\"{$key}\">{$label}</label>\n<div id=\"{$key}\ class=\"radio\"><label class=\"radio-inline\"><input type=\"radio\" name=\"{$key}\" checked=\"checked\" value=\"y\">Yes</label> <label class=\"radio-inline\"><input type=\"radio\" name=\"{$key}\" value=\"n\">No</label></div>\n</div>\n";
       }
       else
       {
        $body.="<div class=\"form-group\">\n<label for=\"{$key}\">{$label}</label>\n<div id=\"{$key}\ class=\"radio\"><label class=\"radio-inline\"><input type=\"radio\" name=\"{$key}\" value=\"y\">Yes</label> <label class=\"radio-inline\"><input type=\"radio\" name=\"{$key}\" checked=\"checked\" value=\"n\">No</label></div>\n</div>\n";
       }
      }
      else
      {
        $body.="<div class=\"form-group\">\n<label for=\"{$key}\">{$label}</label><input class=\"form-control\" type=text name=\"{$key}\" id=\"{$key}\" value=\"{$value}\"></div>\n";
      }
    }
    $body.="<div class=\"form-group center\">\n<button class=\"btn btn-primary\" data-target=\"#messageModal\" name=\"save\" value=\"1\">Save</button>\n</div>\n</form>\n";
  }
  else
  {
    $body="<div class=\"alert alert-danger\">There was a fetal error while attempting to retrieve your current settings</div>\n";
  }
  break;
  case 'library':
  $title="Your Library";
  break;
  case 'projects':
  default:
  $title="Your Projects";
  $body=null;
  $data=new DataBaseTable('content',true,DATACONF);
  $q=$data->getData("pid:`= 0` uid:`= {$session->uid}`");
  $c=0;
  if ($q instanceof PDOStatement)
  {
   $projects=null;
   while ($row=$q->fetch(PDO::FETCH_ASSOC))
   {
    $projects.=con_to_html($row);
    $c++;
   }
  }
  
  if ($c <= 0)
  {
    $body.="<div class=\"alert alert-warning\">You have no projects! Would you like to <a href=\"//{$conf->base_uri}/dash/?section=put&type=project\" data-target=\"#this-modal\">add one</a>?</div>\n";
  }
  else
  {
    $body.="<div id=\"List\" class=\"panel-group\">\n{$projects}\n</div>\n<span class=\"alert alert-info\">You have {$c} project(s). <a href=\"./dash.php?section=put&type=project\" data-target=\"#this-modal\">Add another</a>?</span> <a href=\"javascript:location.reload()\" class=\"right btn btn-info\">Reload Index</a>\n";
  }
 }
}

if (!empty($_GET['json']))
{
  header("Content-type:'text/json'");
  $return_arr['okay']=$success;
  $return_arr['message']=$message;
  echo json_encode($return_arr);
}
elseif (!empty($_GET['section'] == "author-search"))
{
  $udb=new DataBaseTable('users',true,DATACONF);
  $uq=$udb->getData("name:`%{$_GET['q']}%`");
  echo json_encode($uq->fetchALL(PDO::FETCH_ASSOC));
}
else
{
  echo <<<HTML
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">{$title}</h4></div>
<div class="modal-body">
<script src="//{$conf->base_uri}/appcore/scripts/ajaxlinks.js" type="text/javascript"></script>
{$body}
</div>
HTML;
}
?>