 <?php
 function list_projects($filter=null,$curusr=null)
 {
  $table=new DataBaseTable('content',true,DATACONF);
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
    $cq=$table->getData("pid:`= {$row['cid']}`",array('ttid','file'));
    $arts=array();
    while ($cover=$cq->fetch(PDO::FETCH_ASSOC))
    {
     $types=new DataBaseTable('types',true,DATACONF);
     $tq=$types->getData("ttid:`= {$cover['ttid']}`");
     $tinfo=$tq->fetch(PDO::FETCH_ASSOC);
     if ($tinfo['ctype'] == 'art')
     {
       $arts[]=array('file'=>$cover['file'],'type'=>$tinfo['name']);
     }
    }
    
    $list.="<div id=\"{$row['cid']}\" class=\"proj grid-item\">\n";
    if ($arts[0]['type'] == "Front Cover")
    {
     $list.="<img src=\"{$arts[0]['file']}\" class=\"proj-cover\"><caption class=\"proj-title\">{$row['title']}</caption>\n";
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
