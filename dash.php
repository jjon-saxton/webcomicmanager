<?php
if (!file_exists(dirname(__FILE__)."/appcore/dataconnect/connect.ini"))
{
 header("Location: ./app.php?action=install");
}
else
{
 require_once dirname(__FILE__)."/appcore/common.inc.php";
 $conf=new MCSettings();
}

if ($session->level > 4)
{
 $title="403 Forbidden";
 $body=<<<HTML
<h1>Forbidden!</h1>
<p>You must be <a href="./app.php?action=login">logged in</a> to view this section!</p>
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
    if ($message=save_note($_GET['section'],$_POST))
    {
      $body="<div class=\"panel panel-default\">Operation complete! {$message} <a href=\"./dash.php?section={$_GET['type']}\" data-dismiss=\"modal\" data-target=\"#AJAXModal\" data-toggle=\"modal\">Return to project manager</a></div>";
    }
  }
  break;
  case 'view':
  case 'upload':
  case 'remove':
  require_once dirname(__FILE__)."/appmodules/art_man.mod.php";
  $title="Upload/View Art";
  if (!empty($_POST['temp_uri']))
  {
    if ($message=add_art($_GET['section'],$_POST))
    {
      $body="<div class=\"panel panel-default\">Operation complete! {$message} <a href=\"./dash.php?section={$_GET['type']}\" data-dismiss=\"modal\" data-target=\"#AJAXModal\" data-toggle=\"modal\">Return to project manager</a></div>";
    }
  }
  elseif (!empty($_FILES['art']['name']))
  {
    echo upload_file($_FILES['art']);
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
    if ($message=save_asset($_GET['section'],$_POST))
    {
      $body="<div class=\"panel panel-default\">Operation complete! {$message} <a href=\"./dash.php?section={$_GET['type']}\" data-dismiss=\"modal\" data-target=\"#AJAXModal\" data-toggle=\"modal\">Return to project manager</a></div>";
    }
  }
  break;
  case 'admincp':
  $title="Your Site";
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
    $body.="<div class=\"alert alert-warning\">You have no projects! Would you like to <a href=\"./dash.php?section=put&type=project\" data-target=\"#this-modal\">add one</a>?</div>\n";
  }
  else
  {
    $body.="<div id=\"List\" class=\"panel-group\">\n{$projects}\n</div>\n<span class=\"alert alert-info\">You have {$c} project(s). <a href=\"./dash.php?section=put&type=project\" data-target=\"#this-modal\">Add another</a>?</span> <a href=\"javascript:location.reload()\" class=\"right btn btn-info\">Reload Index</a>\n";
  }
 }
}

echo <<<HTML
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">{$title}</h4></div>
<div class="modal-body">
<script src="./appcore/scripts/ajaxlinks.js" type="text/javascript"></script>
{$body}
</div>
HTML;
?>