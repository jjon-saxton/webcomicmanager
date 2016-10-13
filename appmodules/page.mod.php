<?php

function load_page(array $data,MCSession $cursur)
{
  $script=parse_page_data($data['data'],$curusr);
  return <<<HTML
<script language="javascript" type="text/javascript">
{$script['js']}
</script>
<div id="loading" class="panel panel-info">
<div class="panel-heading">Your Page is loading...</div>
<div class="panel-body">We're getting a few things ready please standby.
<div class="progress">
<div class="progress-bar" role="progressbar" style="width:25%">
<span>Processing page scripts</span>
</div>
</div>
</div>
</div>
<div id="pageAssets" class="page container" style="display:none">
{$script['html']}
</div>
HTML;
}

function parse_page_data($src)
{
  $origin=basename($_SERVER['PHP_SELF']);
  if (!empty($src))
  {
    if ($origin == 'preview.php')
    {
      $script['js']=<<<TXT
$(function(){
$("#loading .progress .progress-bar").delay(800).css('width','90%').find('span').text("Finalizing your preview");
$("#loading").delay(1400).remove();
$("#pageAssets").delay(1500).show();

TXT;
    }
    else
    {
     $script['js']=<<<TXT
$(function(){
  $("div#AS-2").remove();
  $("div#Page").attr("class","text-justify col-sm-12");
  $('.page .page-panel').each(function(){
    $(this).hide();
  });
  $("#loading .progress .progress-bar").css('width','65%').find('span').text('Loading Panels');
});
$(window).on('load',function(){
  $("#loading .progress .progress-bar").css('width','90%').find('span').text('Finalizing a few things').delay('500').css('width','100%').find('span').text('Complete!');
  $('#loading').delay(1000).fadeOut('slow');
  $('#pageAssets').delay(1500).fadeIn('slow');

TXT;
    }
    $html=str_get_html($src);
    if ($html->find("div.canvas .canvas-asset .transition",0))
    {
     $pid=1;
     foreach ($html->find(".canvas-asset") as $p)
     {
       if ($pid == 1)
       {
         if ($p->find("#delay"))
         {
          $tvalue=$p->find("#delay input",0);
          if ($tvalue=$tvalue->value)
          {
            $wait=$tvalue*1000+1600; //+1600 is is the last active delay (1500) in the script plus 100 milliseconds. This is so that the end user sees the delay as it is intended by the artist after the page is loaded.
            foreach ($p->find(".transition") as $a)
            {
             if($a->id != "delay")
             {
               $animation=$a->id;
               $d=$a->find("input",0);
               $duration=$d->value;
               if (is_numeric($duration))
               {
                 $duration=$duration*1000;
               }
               else
               {
                 $duration="'{$duration}'";
               }
             }
            }
            if (empty($animation))
            {
              $animation="fadeIn";
            }
            if (empty($duration))
            {
              $duration="'slow'";
            }
            $script['js'].="$(\"div#{$pid}.page-panel\").delay({$wait}).{$animation}($duration)";
          }
          else
          {
            $script['js'].="$(\"div#{$pid}.page-panel\").show()";
          }
         }
         else
         {
           $script['js'].="$(\"div#{$pid}.page-panel\").show()";
         }
       }
       else
       {
         if ($p->find("#delay.transition"))
         {
           if ($tvalue=$p->find("#delay input"))
           {
             $delay=$tvalue->value;
             $delay=$delay*1000+1600; //+1600 represents the last delay plus 100 milliseconds this will allow the delay to actually be proceived.
           }
           else
           {
             $delay=0;
           }
         }
         foreach ($p->find("div.transition") as $t)
         {
           if ($t->id != "delay")
           {
             $transition=$t->id;
             $duration=$t->find("input#tvalue",0);
             $duration=$duration->value;
             if (is_numeric($duration))
             {
               $duration=$duration*1000;
             }
             else
             {
               $duration="'{$duration}'";
             }
             if (!empty($delay))
             {
               $script['js'].=";\n$(\"div#{$pid}.page-panel\").delay({$delay}).{$transition}({$duration})";
             }
             elseif (!empty($p))
             {
               if (empty($transition))
               {
                 $transition="fadeIn";
               }
               $script['js'].=".click(function(){\n\t$(this).next('.page-panel').{$transition}({$duration})\n})";
             }
           }
         }
       }
       $pid++;
     }
    }
    else
    {     
      $script['js'].="\n$('.page .page-panel').each(function(){\n\t$(this).show();\n});\n";
    }
    $script['js'].="});\n";
    foreach($html->find("div.transition") as $remove)
    {
      $remove->outertext='';
    }
    unset($remove);
    foreach ($html->find(".ui-resizable-handle") as $remove)
    {
      $remove->outertext='';
    }
    $e=$html->find("div.active",0);
    $e->removeClass('active');
    $e=$html->find("div.desktop",0);
    $e->class="canvas visible-lg hidden-md hidden-sm hidden-xs";
    $e->id="DesktopPage";
    $e=$html->find("div.tablet",0);
    $e->class="canvas hidden-lg visible-md visible-sm hidden-xs";
    $e->id="TabletPage";
    $e=$html->find("div.phone",0);
    $e->class="canvas hidden-lg hidden-md hidden-sm visible-xs";
    $e->id="PhonePage";
  
    $canvases=$html->find(".canvas");
    foreach ($canvases as $canvas)
    {
        $ids=1;
        $panels=$canvas->find(".canvas-asset");
        foreach ($panels as $panel)
        {
            $panel->id=$ids;
            $panel->class="page-panel";
            $ids++;
        }
    }
    $html->save();
  
    $script['html']=$html;
  }
  else
  {
    $script['html']="<div class=\"alert alert-warning\">Page is empty!</div>";
  }
  return $script;
}
