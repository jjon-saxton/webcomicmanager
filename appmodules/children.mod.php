<?php

function list_children($from,MCSession $curusr,$filter=null)
{
  $path_parts=explode("/",$from);
  $names=array();
  foreach ($path_parts as $name)
  {
   if (!empty($name))
   {
    $names[]=storagenamedecode($name);
   }
  }
  
  $cid=find_cid($names);
  
  $con=new DataBaseTable('content',true,DATACONF);
  $types=new DataBaseTable('types',true,DATACONF);
  $cq=$con->getData("pid:`= {$cid}`",null,null,$curusr->items_per_page,$_GET['offset']);
  $cols=$curusr->items_per_page/$curusr->rows_per_page;
  $grid=con_list_to_grid($cq);
  $list="<div id=\"Grid-{$cid}\" class=\"grid grid-col-{$cols}\">".$grid."</div>\n";
   if (empty($grid))
   {
     $self=$con->getData("cid:`= {$cid}`");
     $self=$self->fetch();
     $type=$types->getData("ttid:`= {$self['ttid']}`");
     $type=$type->fetch();
     if ($type['ctype'] == 'page')
     {
       require_once dirname(__FILE__)."/page.mod.php";
       $list=load_page($self,$curusr);
     }
     else
     {
       $list="<div id=\"Message-{$cid}\" class=\"alert alert-warning\">No Children detected for this content!</div>\n";
     }
   }
   
  return $list;
}

function find_cid(array $titles,$parent=0,$offset=0)
{
  $con=new DataBaseTable('content',true,DATACONF);
  $cq=$con->getData("title:`{$titles[$offset]}` pid:`= {$parent}`",array('cid','title'));
   $ci=$cq->fetch(PDO::FETCH_OBJ);
  
  $offset++;
  if (!empty($titles[$offset]))
  {
   return find_cid($titles,$ci->cid,$offset);
  }
  else
  {
   return $ci->cid;
  }
}
