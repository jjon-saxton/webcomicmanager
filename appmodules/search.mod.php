 <?php
function search($scope,array $q_items=null)
{
  switch($scope)
  {
   case 'random':
   //TODO find random series
   break;
   case 'advanced-search':
   if (empty($q_items))
   {
     return search_form(true);
   }
   else
   {
     //TODO run query
   }
   break;
   case 'search':
   default:
   if (empty($q_item))
   {
     return search_form(false);
   }
   else
   {
    //TODO run query
   }
  }
}

function search_form($full=false)
{
  $form="<form action=\"".SITEROOT."search/\" method=\"get\">\n<div class=\"form-group\"><label for=\"query\">Text (in title or description)</label><input type=\"search\" class=\"form-control\" name=\"q\">\n</div>\n";
  if ($full == TRUE)
  {
    //TODO add advanced search options to form
  }
  $form.="<div class=\"center\">\n<button class=\"btn btn-primary\" type=submit\"><span class=\"glyphicon glyphicon-search\"></span> Search</button>\n</form>\n";
  
  return $form;
}