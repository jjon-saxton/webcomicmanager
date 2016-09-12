<?php
if (!empty($_SERVER['PATH_INFO'])) //Are we forwarding from 'pretty' urls?
{
 @list($section,$item,$action)=explode("/",ltrim($_SERVER['PATH_INFO'],"/")); //q holds a path, we need to split it!
 $path="/".ltrim($_SERVER['PATH_INFO'],"/");
}
else //we are going direct from the query string!
{
 foreach ($_GET as $var=>$val) //Parse through the $_GET array and set variables for each named key
 {
  ${$var}=$val;
 }
 $path="/".ltrim(@$_GET['path'],"/");
}

if (@$action == "install")
{
 define("USR","installer");
}
elseif (!file_exists(dirname(__FILE__)."/appcore/dataconnect/connect.ini"))
{
 header("Location: ./app.php?action=install");
}
else
{
 require_once dirname(__FILE__)."/appcore/common.inc.php";
 $conf=new MCSettings();
 $GLOBALS['CONF']=$conf->get();
}

?>
<!doctype html>
<html>
<head>
 <title>Tower21 WebComiX: <?php echo($path) ?></title>
 <!-- Load Bootstrap and dependencies -->
 <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="//www.tower21studios.com/assets/js/bootstrap.js"></script>
 <script src="./appcore/scripts/editor.js"></script>
 <?php if (!empty($_GET['modal'])){ ?>
 <script language="javascript">
 $(function(){
    $("#messageModal .modal-title").html("<?php echo ucwords($_GET['modal']) ?>");
    $("#messageModal .modal-body").html("<?php switch($_GET['modal']){
      case 'login':
      echo "<div class='alert alert-info'>Please provide login credentials</div><form action='./app.php?action=login' method='post'><div class='form-group'><label for='user'>Username</label><input type='text' class='form-control' name='name' id='user'></div><div class='form-group'><label for='pass'>Password</label><input type='password' class='form-control' name='password' id='pass'></div><button type='submit' class='btn btn-primary'>Login</button> <button data-dismiss='modal' class='btn btn-danger'>Cancel</button></form>";
    } ?>");
    $("#messageModal").modal('show');
 });
 </script>
 <?php } ?>
 
 <link rel="stylesheet" href="./appcore/styles/editor.css" type="text/css">
 <!-- Styles from main domain -->
 <link rel="stylesheet" href="//www.tower21studios.com/assets/css/bootstrap.css" type="text/css">
 <link rel="stylesheet" href="//www.tower21studios.com/assets/css/v2.css" type="text/css">
</head>
<body>
<header>
<div id="messageModal" class="modal fade" role="dialog">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type=button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title">Message</h3>
</div>
<div class="modal-body">
<p>We have a message for you!</p>
</div>
</div>
</div>
</div>
<div id="AJAXModal" class="modal fade" role="dialog">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type=button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title">Preparing something awesome...</h3>
</div>
<div class="modal-body">
<p>Becoming 20% more awesome...</p>
</div>
</div>
</div>
</div>
<nav class="navbar navbar-default">
  <div class="container-fluid"> 
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#defaultNavbar1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
      <a class="navbar-brand" href="../">#Dare2Dream</a></div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="defaultNavbar1">
      <ul class="nav navbar-nav">
        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php if($session->level < 5){ print "Welcome ". $session->name; }
        else { print "Login"; } ?><span class="caret"></span></a>
          <?php
          require_once dirname(__FILE__)."/appmodules/ucp.mod.php";
          echo ucp_module($session);
          ?>
        </li>
        <li class="dropdown"><a href="./app.php?section=randomizer" role="button" aria-expanded="false">Discover</a>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Tower21 Studios Limited<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="http://www.towe21studios.com">Main Site</a></li>
            <li><a href="http://blog.tower21studios.com">Our Blog</a></li>
            <li><a href="https://twitter.com/Tower21Studios">Twitter</a></li>
            <li><a href="https://www.facebook.com/Tower21Studios">Facebook</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
</header>
<div class="container">
<hr>
<div class="row">
<div class="text-justify col-lg-4 col-md-5 col-sm-6" id="Updates">
<?php
if ($action != 'install')
{
 require dirname(__FILE__)."/appmodules/updated.mod.php";
 //echo recently_updated($session,'box');
} ?>
</div>
<div class="text-justify col-lg-8 col-md-7 col-sm-6" id="Page">
<?php
if (!@$_GET['page'])
{
  echo load_index($path,$session);
}
else
{
  echo load_page($_GET['page'],$page,$session);
}
?>
</div>
</div>
</div>
</body>
</html>

<?php function load_index($path,MCSession $curusr)
{
 if (empty($path) || $path == "/")
 {
  require_once dirname(__FILE__)."/appmodules/series.mod.php";
  return list_projects($_GET['q'],$curusr);
 }
}

function load_page($page,$path,MCSession $curusr)
{
 var_dump($page);
}
?>
</html>
<?php
