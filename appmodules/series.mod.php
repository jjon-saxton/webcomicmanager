 <?php
 function list_projects($filter=null,$curusr=null)
 {
  $table=new DataBaseTable('content',true,DATACONF);
  $art=new DataBaseTable('art',true,DATACONF);
  $q="pid:`= 0`";
  if (!empty($filter))
  {
   $q.=" ".$filter;
  }
  $q=$table->getData($q);
  if ($q instanceof PDOStatement)
  {
   $c=0;
   $list="<div id=\"ConList\" class=\"grid\">\n";
   while ($row=$q->fetch(PDO::FETCH_ASSOC))
   {
    $aq=$art->getData("cid:`= {$row['cid']}`",array('ttid','uri'));
    $arts=array();
    while ($cover=$aq->fetch(PDO::FETCH_ASSOC))
    {
     $types=new DataBaseTable('types',true,DATACONF);
     $tq=$types->getData("ttid:`= {$cover['ttid']}`");
     $tinfo=$tq->fetch(PDO::FETCH_ASSOC);
     if ($tinfo['ctype'] == 'art')
     {
       $arts[]=array('file'=>$cover['uri'],'type'=>$tinfo['name']);
     }
    }
    
    $list.="<div id=\"{$row['cid']}\" class=\"proj grid-item\">\n";
    if ($arts[0]['type'] == "Front Cover")
    {
     $list.="<figure class=\"figure\">\n<img src=\"./download.php?file={$arts[0]['file']}&type=image/png&w=350\" width=\"350\" class=\"proj-cover figure-img img-fluid img-round\" alt=\"[cover]\">\n<figcaption class=\"proj-title figure-caption text-center\">{$row['title']}</figcaption>\n</figure>\n";
    }
    else
    {
     $list.="<h3 class=\"proj-title\">{$row['title']}</h3>\n<p class=\"proj-description\">{$row['data']}</p>\n";
    }
    $list.="</div>\n";
    $c++;
   }
   $list.="</div>\n";
  }
  
  if ($c <= 0)
  {
   if (empty($curusr) || $curusr->level >= 3)
   {
    $html="<div class=\"alert alert-warning\">No Content Posted! Please <a href=\"./?modal=login\">login</a> and post some stuff!!</div>";
   }
   else
   {
     $html="<div class=\"alert alert-warning\">No Content Posted! Please open the <a href=\"./dash.php?section=projects\" data-toggle=\"modal\" data-target=\"#AJAXModal\">project manager</a> to add new content!</div>";
   }
  }
  else
  {
   $html=$list;
  }

  return $html;
 }
