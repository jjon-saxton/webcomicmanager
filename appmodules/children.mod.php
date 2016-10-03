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
  $list="<div id=\"Grid-{$cid}\" class=\"grid grid-col-{$cols}\">\n";
  $c=0;
  while ($row=$cq->fetch(PDO::FETCH_ASSOC))
  {
    $art=new DataBaseTable('art',true,DATACONF);
    $aq=$art->getData("cid:`= {$row['cid']}`",array('ttid','uri'));
    $arts=array();
    while ($cover=$aq->fetch(PDO::FETCH_ASSOC))
    {
     $tq=$types->getData("ttid:`= {$cover['ttid']}`");
     $tinfo=$tq->fetch(PDO::FETCH_ASSOC);
     if ($tinfo['ctype'] == 'art')
     {
       $arts[]=array('file'=>$cover['uri'],'type'=>$tinfo['name']);
     }
    }
    
    $path=build_con_path($row['cid']);
    
    //TODO replace hard root with root uri from database
    $list.="<a href=\"".SITEROOT."{$path}\"><div id=\"{$row['cid']}\" class=\"proj grid-item\">\n";
    if ($arts[0]['type'] == "Front Cover")
    {
     $list.="<figure class=\"figure\">\n<img src=\"".SITEROOT."/{$arts[0]['file']}?type=image/png&w=350\" width=\"350\" class=\"proj-cover figure-img img-fluid img-round\" alt=\"[cover]\">\n<figcaption class=\"proj-title figure-caption text-center\">{$row['title']}</figcaption>\n</figure>\n";
    }
    else
    {
     $list.="<h3 class=\"proj-title\">{$row['title']}</h3>\n<p class=\"proj-description\">{$row['data']}</p>\n";
    }
    $list.="</div></a>\n";
    $c++;
   }
   $list.="</div>\n";
   if ($c <= 0)
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
