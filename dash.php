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
  case 'projects':
  default:
  $title="Your Projects";
  $body="<h1>{$title}</h1>\n";
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