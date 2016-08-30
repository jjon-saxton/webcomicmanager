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
  switch ($type)
  {
    //TODO change put form based on $type
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
    $body.="<div class=\"alert alert-warning\">You have '{$c}' projects! Would you like to <a href=\"./dash.php?section=put&type=project\">add one</a>?</div>\n";
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