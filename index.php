<?php
if (!empty($_SERVER['ORIG_PATH_INFO'])) //Are we forwarding from 'pretty' urls?
{
 $path=$_SERVER['ORIG_PATH_INFO'];
}
else //we are going direct from the query string!
{
 foreach ($_GET as $var=>$val) //Parse through the $_GET array and set variables for each named key
 {
  ${$var}=$val;
 }
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
}

?>
<!doctype html>
<html>
<head>
 <title>Tower21 WebComiX: <?php echo($path) ?></title>
 <!-- Load Bootstrap and dependencies -->
 <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="//<?php echo $conf->base_uri ?>/appcore/scripts/summernote.min.js"></script>
 <script src="//www.tower21studios.com/assets/js/bootstrap.js"></script>
 <script src="//<?php echo $conf->base_uri ?>/appcore/scripts/editor.js"></script>
 <!-- jQuery UI draggable and resizable required for editor -->
 <script src="//<?php echo $conf->base_uri ?>/appcore/scripts/jquery-ui.min.js"></script>
 <script language="javascript">
 $(function(){
    if ($("#Page .page").length > 0){
        $("#AS-2").remove();
        $("#Page").attr("class","text-justify col-sm-12");
    }
 <?php if (!empty($_GET['modal'])){ ?>
    $("#messageModal .modal-title").html("<?php echo ucwords($_GET['modal']) ?>");
    $("#messageModal .modal-body").html("<?php switch($_GET['modal']){
      case 'private':
      echo "<div class='alert alert-warning'>The author has marked this item as either private or as a draft. It, therefore, cannot be accessed through the index. If you are the author or an editor, please access this through the project manager.</div>";
      break;
      case 'login':
      echo "<div class='alert alert-info'>Please provide login credentials</div><form action='./app.php?action=login' method='post'><div class='form-group'><label for='user'>Username</label><input type='text' class='form-control' name='name' id='user'></div><div class='form-group'><label for='pass'>Password</label><input type='password' class='form-control' name='password' id='pass'></div><button type='submit' class='btn btn-primary'>Login</button> <button data-dismiss='modal' class='btn btn-danger'>Cancel</button></form>";
    } ?>");
    $("#messageModal").modal('show');
    $("#messageModal").on('hidden.bs.modal',function(){
        history.back();
    });
 <?php } ?>
 });
 </script>
 <link rel="stylesheet" href="//<?php echo $conf->base_uri ?>/appcore/styles/editor.css" type="text/css">
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
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title">Message</h3>
</div>
<div class="modal-body">
<p>We have a message for you!</p>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-primary" data-dismiss="modal">Okay</button>
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
      <a class="navbar-brand" href="//<?php echo $conf->base_uri ?>/">#Dare2Dream</a></div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="defaultNavbar1">
      <ul class="nav navbar-nav">
        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php if($session->level < 5){ print "Welcome ". $session->name; }
        else { print "Login"; } ?><span class="caret"></span></a>
          <?php
          require_once dirname(__FILE__)."/appmodules/ucp.mod.php";
          echo ucp_module($session,$conf->base_uri,$conf->open_registration);
          ?>
        </li>
        <li><a href="//<?php echo $conf->base_uri ?>/random/" role="button" aria-expanded="false">Discover</a>
        </li>
        <li><form class="navbar-form navbar-left" role="search" action="//<?php echo $conf->base_uri ?>/search/" method="get">
        <div class="input-group">
         <input type="text" class="form-control" name="q" placeholder="Search"<?php
         if (!empty($_GET['q']))
         {
          echo " value=\"{$_GET['q']}\"";
         }?>>
         <div class="input-group-btn">
          <button type="submit" class="btn btn-primary" title="Search"><span class="glyphicon glyphicon-search"></span></button>
          <button type="button" onclick="window.location='//<?php echo $conf->base_uri ?>/advanced-search/'" class="btn btn-primary" title="Advanced"><span class="glyphicon glyphicon-cog"></span></button>
         </div>
        </div>
        </form></li>
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
<div class="text-center as col-sm-12" id="AS-1">
<?php
require_once dirname(__FILE__)."/appmodules/adspace.mod.php";
//echo inner_add('random','h');
?>
</div>
</div>
<hr>
<div class="row">
<div class="text-justify as col-lg-2 col-md-4 col-sm-5" id="AS-2">
<?php
 require dirname(__FILE__)."/appmodules/adspace.mod.php";
 //echo inner_add('random','v');
?>
</div>
<div class="text-justify col-lg-10 col-md-8 col-sm-7" id="Page">
<?php
  $path_parts=(explode("/",trim($path,"/")));
  switch($path_parts[0])
  {
    case "random":
    case "search":
    case "advanced-search":
    require_once dirname(__FILE__)."/appmodules/search.mod.php";
    echo start_search($path_parts[0],$_GET);
    break;
    case "view":
    echo view_doc($path_parts[1],$path_parts[2]);
    break;
    default:
    echo load_index($path,$session);
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
  return list_projects($curusr,$_GET['q']);
 }
 else
 {
  require_once dirname(__FILE__)."/appmodules/children.mod.php";
  return list_children($path,$curusr,$_GET['q']);
 }
}

function view_doc($type,$id)
{
  $table=new DataBaseTable($type."s",true,DATACONF);
  $col=substr($type,0,1)."id";
  $q=$table->getData($col.":`= ".$id);
  $siteroot=SITEROOT;
  $doc=$q->fetch(PDO::FETCH_ASSOC);
  return <<<HTML
<h2>{$doc['title']}</h2>
<div class="left" style="width:1em"><a href="{$siteroot}save/{$type}/{$id}/html"><span class="glyphicon glyphicon-save" title="Download"></span></a> <a href="{$siteroot}print/{$type}/{$id}"><span class="glyphicon glyphicon-print" title="Print"></span></a></div>
<div id="DocViewer" class="page" style="width:95%">
{$doc['note']}
</div>
HTML;
}