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
    $body=build_manager_form($_GET['section'],$_GET['type']);
  }
  else
  {
    $body=save_asset($_POST);
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
   while ($row=$q->fetch(PDO::FETCH_ASSOC))
   {
    //TODO generate table rows
    $c++;
   }
  }
  
  if ($c <= 0)
  {
    $body.="<div class=\"alert alert-warning\">You have no projects! Would you like to <a href=\"./dash.php?section=put&type=project\">add one</a>?</div>\n";
  }
  else
  {
    //TODO set $body two table with above rows
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