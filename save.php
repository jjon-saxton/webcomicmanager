<?php
require dirname(__FILE__)."/appcore/common.inc.php";
if (!empty($_SERVER['REQUEST_URI'])) //Are we forwarding from 'pretty' urls?
{
 $path=$_SERVER['REQUEST_URI'];
 $path_parts=(explode("/",trim($path,"/")));
 if ($path_parts[1] == 'save')
 {
   $attachment='download';
 }
 $type=$path_parts[2];
 if ($type != 'content')
 {
  $type=$type."s";
 }
 $id=$path_parts[3];
 $format=$path_parts[4];
}
else //we are going direct from the query string!
{
 foreach ($_GET as $var=>$val) //Parse through the $_GET array and set variables for each named key
 {
  ${$var}=$val;
 }
}

 $db=new DataBaseTable($type,true,DATACONF);
 $col=substr($type,0,1)."id";
 $q=$db->getData($col.":`= {$id}`");
 $data=$q->fetch(PDO::FETCH_OBJ,PDO::FETCH_ORI_FIRST);
 
 if (!empty($data->file))
 {
   header("Location: //{$conf->base_uri}/{$data->file}");
 }
 elseif ($type == 'content')
 {
   $doc=$data->data;
 }
 else
 {
   $doc=$data->$path_parts[2];
 }
 
 if (!empty($attachment) && $attachment == 'download')
 {
  $filename=storagename($data->title);
  switch ($format)
  {
    /*case 'word':
    header ("Content-Type: application/msword");
    $filename.=".doc";
    TODO reformat $doc? create other formats?
    break;*/
    case 'html':
    $filename.=".html";
    header ("Content-Type: text/html");
    break;
    case 'text':
    default:
    $filename.=".txt";
    $doc=strip_tags($doc);
    header ("Content-Type: text/plain");
  }
  header("Content-Disposition: attachment; filename={$filename}");
  echo ($doc);
 }
 else
 {
   echo ("<body onload=\"window.print()\">".$doc."</body>");
 }