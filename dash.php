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
  case 'put':
  case 'update':
  case 'drop':
  require_once dirname(__FILE__)."/appmodules/asset_man.mod.php";
  $title="Asset Manager";
  if (empty($_POST['save']))
  {
    $body=build_manager_form($session,$_GET['section'],$_GET['type'],$_GET['cid']);
  }
  else
  {
    if (save_asset($_POST))
    {
      header("Location: http://{$conf->base_uri}/dash.php?section=projects");
    }
  }
  break;
  case 'projects':
  default:
  $title="Your Projects";
  $body="<h1>{$title}</h1>\n";
  $data=new DataBaseTable('content',true,DATACONF);
  $q=$data->getData("pid:`= 0` uid:`= {$session->uid}`");
  $c=0;
  if ($q instanceof PDOStatement)
  {
   $projects=null;
   while ($row=$q->fetch(PDO::FETCH_ASSOC))
   {
    $projects.=<<<HTML
<div class="panel panel-default">
  <div class="panel-heading">{$row['title']}</div>
  <div class="panel-body">{$row['data']}</div>
  <div class="panel-footer">
    <a href="?section=update&cid={$row['cid']}" class="btn btn-info">Edit</a>
    <a href="?section=put&pid={$row['cid']}" class="btn btn-success">Add Child</a>
    <a href="?section=drop&cid={$row['cid']}" class="btn btn-error">Remove</a>
  </div>
</div>
HTML;
    $c++;
   }
  }
  
  if ($c <= 0)
  {
    $body.="<div class=\"alert alert-warning\">You have no projects! Would you like to <a href=\"./dash.php?section=put&type=project\">add one</a>?</div>\n";
  }
  else
  {
    $body.="<div id=\"List\" class=\"panel-group\">\n{$projects}\n</div>\n<span class=\"alert alert-info\">You have {$c} project(s). <a href=\"?section=put&type=project\">Add another</a>?</span>\n";
  }
 }
}
?>
<html>
<head>
<title>Placeholder theme: <?php echo $title ?></title>
</head>
<body>
<?php echo $body ?>
</body>
</html>