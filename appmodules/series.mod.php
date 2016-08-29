 <?php
 function list_projects($filter=null)
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
   $list="<div id=\"ConList\">\n";
   while ($row=$q->fetch(PDO::FETCH_ASSOC))
   {
    //TODO add data to $list
    $c++;
   }
   $list.="</div>\n";
  }
  
  $html="<h1 class=\"title\">Series</h1>\n";
  if ($c >= 0)
  {
   $html.="<div class=\"alert alter-warning\">No Content Posted! Check above to login and post some stuff!!</div>";
  }
  else
  {
   $html=$list;
  }

  return $html;
 }
