 <?php
 function list_projects(MCSession $curusr,$filter=null)
 {
  $table=new DataBaseTable('content',true,DATACONF);
  $q="pid:`= 0`";
  if (!empty($filter))
  {
   $q.=" ".$filter;
  }
  $q=$table->getData($q,null,null,$curusr->items_per_page,$_GET['offset']);
  $cols=$curusr->items_per_page/$curusr->rows_per_page;
  if ($q instanceof PDOStatement)
  {
   $grid=con_list_to_grid($q);
   $list="<div id=\"ConList\" class=\"grid grid-col-{$cols}\">\n";
  }
  
  if (empty($grid))
  {
   if (empty($curusr) || $curusr->level >= 3)
   {
    $html="<div class=\"alert alert-warning\">No Content Posted! Please <a href=\"".SITEROOT."/?modal=login\">login</a> and post some stuff!!</div>";
   }
   else
   {
     $html="<div class=\"alert alert-warning\">No Content Posted! Please open the <a href=\"".SITEROOT."dash/?section=projects\" data-toggle=\"modal\" data-target=\"#AJAXModal\">project manager</a> to add new content!</div>";
   }
  }
  else
  {
   $html=$list;
  }

  return $html;
 }
