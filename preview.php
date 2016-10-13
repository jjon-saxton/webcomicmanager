 <?php
 require dirname(__FILE__)."/appcore/common.inc.php";
 
 switch ($_GET['action'])
 {
   case 'restore':
   echo $_POST['data'];
   break;
   case 'process':
   default:
   require_once dirname(__FILE__)."/appmodules/page.mod.php";
   echo load_page($_POST,$session);
}