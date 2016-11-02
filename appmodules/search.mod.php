 <?php
function start_search($scope,array $q_items=null)
{
  switch($scope)
  {
   case 'random':
   return random_search();
   break;
   case 'advanced-search':
   if (empty($q_items))
   {
     return search_form(true);
   }
   else
   {
     return run_query($q_items);
   }
   break;
   case 'search':
   default:
   if (!empty($q_items))
   {
     return run_query($q_items);
   }
   else
   {
     return search_form(false);
   }
  }
}

function run_query(array $filters)
{
 $con=new DataBaseTable("content",true,DATACONF);
 if (!empty($filters['date-created']['max']))
 {
  $filters['created']="<> ".date("Y-m-d H:i",strtotime($filters['date-created']['min']))." ".date("Y-m-d H:i",strtotime($filters['date-created']['max']));
 }
 else
 {
  $filters['created']="<> ".date("Y-m-d H:i",strtotime($filters['date-created']['min']))." ".date("Y-m-d H:i");
 }
 
 if (!empty($filters['date-modified']['max']))
 {
  $filters['modified']="<> ".date("Y-m-d H:i",strtotime($filters['date-modified']['min']))." ".date("Y-m-d H:i",strtotime($filters['date-modified']['max']));
 }
 else
 {
  $filters['modified']="<> ".date("Y-m-d H:I",strtotime($filters['date-modified']['min']))." ".date("Y-m-d H:i");
 }
 unset($filters['date-created'],$filters['date-modified']);
 
 if (!empty($filters['price']['max']))
 {
  $filters['price']="<> ".$filters['price']['min']." ".$filters['price']['max'];
 }
 else
 {
  $filters['price']="> ".$filters['price']['min'];
 }
 
 if (!empty($filters['author']))
 {
   $adb=new DataBaseTable('users',true,DATACONF);
   $aq=$adb->getData("name:`{$filters['author']}`",array('uid'));
   $author=$aq->fetch();
   $filters['uid']=$author['uid'];
 }
 unset($filters['author']);
 
 if (is_array($filters['tags']))
 {
  $filters['tags']=implode(",",$filters['tags']);
 }
 
 if (!empty($filters['q']))
 {
  $q=$filters['q']." ";
 }
 else
 {
  $q=null;
 }
 unset($filters['q']);
 
 foreach ($filters as $col=>$val)
 {
  if (!empty($val))
  {
   if (is_numeric($val))
   {
    $val="= ".$val;
   }
   $q.="{$col}:`{$val}` ";
  }
 }
 $q=trim($q);
 if (!empty($q))
 {
  $q=$con->getData($q,null,"created",40,$_GET['offset'],array('title','data'));
  if ($q)
  {
   $grid=con_list_to_grid($q,true);
   if (!empty($grid))
   {
    $html="<div class=\"grid grid-col-4\">\n{$grid}</div>\n";
   }
   else
   {
    $html="<div class=\"alert alert-info\">No results found!</div>\n";
   }
  }
  else
  {
   $hml="<div class=\"alert alert-warning\">SQL Error, check out query string!</div>\n";
  }
  return $html;
 }
 else
 {
  return search_form(false);
 }
}

function random_search()
{
  $cdb=new DataBaseTable('content',true,DATACONF);
  $aq=$cdb->getData("pid:`= 0`",array('cid'));
  $ac=$aq->fetch(PDO::FETCH_OBJ,PDO::FETCH_ORI_LAST);
  if ($ac->cid > 0) // can't be zero
  {
   $rid=mt_rand(1,$ac->cid);
  }
  else
  {
   $rid=1;
  }
  if ($rq=$cdb->getData("cid:`= {$rid}`"))
  {
   $grid=con_list_to_grid($rq,true);
   if (!empty($grid))
   {
    return "<div class=\"grid grid-col-4\">\n".$grid."\n</div>\n";
   }
   else
   {
    return random_search(); //result empty try a different random ID
   }
  }
  else
  {
   return "<div class=\"alert alert-danger\">Cannot retrieve random series! Unknown server error.</div>\n";
  }
}

function search_form($full=false)
{
  $siteroot=SITEROOT;
  $form="<form action=\"".SITEROOT."search/\" method=\"get\">\n<div class=\"form-group\"><label for=\"query\">Text (in title or description)</label><input type=\"search\" class=\"form-control\" name=\"q\">\n</div>\n";
  if ($full == TRUE)
  {
    $tdb=new DataBaseTable('tags',true,DATACONF);
    $tq=$tdb->getData();
    while ($tag=$tq->fetch())
    {
      $filters.="<div class=\"grid-item\"><input id=\"t-{$tag['tid']}\" type=\"checkbox\" name=\"tags[]\" value=\"{$tag['tid']}\"> <label for=\"t-{$tag['tid']}\" class=\"tag tag-{$tag['type']}\">{$tag['name']}</label></div>\n";
    }
    $form.=<<<HTML
<script language="javascript">
$(function(){
 $("input#author").keyup(function(){
  $.get('{$siteroot}dash/?section=author-search&q='+$("input#author").val(),function(data){
   $("#authors").html('');
   for(var i=0;i<data.length;i++){
    $("#authors").append('<option value="'+data[i].name+'"></option>');
   }
  },'json');
 });
});
</script>
<div class="alert alert-info">Did you know you can perform most advanced searches with just the above textbox? It's true!</div>
<div class="form-group">
<label for="author">Author:</label><input type="text" list="authors" class="form-control" name="author" id="author">
<datalist id="authors">
</datalist>
</div>
<div class="form-group">
<label for="tags">Tags:</label>
<div id="tags" class="grid grid-col-4">
{$filters}
</div>
</div>
<div class="form-group">
<label for="cdate">Date Posted:</label>
<div class="input-group">
<span class="input-group-addon">Between</span>
<input type="date" id="cdate" class="form-control" name="date-created[min]">
<span class="input-group-addon">and</span>
<input type="date" class="form-control" name="date-created[max]">
</div>
</div>
<div class="form-group">
<label for="mdate">Date Modified:</label>
<div class="input-group">
<span class="input-group-addon">Between</span>
<input type="date" id="mdate" class="form-control" name="date-modified[min]">
<span class="input-group-addon">and</span>
<input type="date" id="mdate2" class="form-control" name="date-modified[max]">
</div>
</div>
<div class="form-group">
<label for="price-min">Price:</label>
<div class="input-group">
<span class="input-group-addon">Between</span>
<input type="number" id="price-min" class="form-control" placeholder="Min:" name="price[min]">
<span class="input-group-addon">and</span>
<input type="number" id="price-max" class="form-control" placeholder="Max:" name="price[max]">
</div>
</div>
HTML;
  }
  $form.="<div class=\"center\">\n<button class=\"btn btn-primary\" type=submit\"><span class=\"glyphicon glyphicon-search\"></span> Search</button>\n</form>\n";
  
  return $form;
}