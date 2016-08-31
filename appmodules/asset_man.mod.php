<?php

function build_manager_form($action,$ctype,$cid=null)
{
  $html="<h1>Asset Manager: {$action} {$ctype}</h1>\n";
  if ($action != 'drop')
  {
    $types=new DataBaseTable('types',true,DATACONF);
    $ttids=$types->getData("ctype:`{$ctype}`");
    $ttid_opts="<select class=\"form-control\" name=\"ttid\">\n";
    while ($type=$ttids->fetch(PDO::FETCH_ASSOC))
    {
      $ttid_opts.="<option value=\"{$type['ttid']}\">{$type['name']}</option>\n";
    }
    $ttid_opts.="</select>\n";
    
    if ($action == 'update' && $cid != NULL)
    {
      $con=new DataBaseTable('content',true,DATACONF);
      $q=$con->getData("cid:`{$cid}`");
      $values=$q->fetch(PDO::FETCH_ASSOC);
      $values['modified']=date("Y-m-d H:i:s");
    }
    else
    {
     $values['created']=date("Y-m-d H:i:s");
     $values['modified']=null;
     $values['title']="New ".ucwords($ctype);
     $values['tags']=null;
    }
    
    $html.=<<<HTML
<form method="post">
<div class="form-group">
<label for="title">Title</label>
<input type="hidden" name="created" value="{$values['created']}">
<input type="hidden" name="modified" value="{$values['modified']}">
<input type="text" class="form-control" maxlength="160" id="title" name="title" value="{$values['title']}">
</div>
<div class="form-group">
<label for="tags">Tags</label>
<input type="text" class="form-control" id="tags" name="tags" value="{$values['tags']}">
</div>
<div class="form-group">
<label for="ttid">Type</label>
{$ttid_opts}
</div>
<div id="Preferences">
{$type_extras}
</div>
<div class="form-group">
<button class="form-control btn btn-primary" type="submit" name="save" value=1>Save</button>
</div>
</form>
HTML;
  }
  
  return $html;
}
