<?php

function ucp_module($cusr,$type)
{
 $html="<div id=\"UCP\" class=\"{$type}\"><ul class=\"{$type} nobullet\">\n";
 if ($cusr->level < 5)
 {
  $html.=<<<HTML
<li>Welcome <a href="./profile.php?name={$cusr->name}&action=view" title="view/edit profile">{$cusr->name}</a>!</li>
<li><a href="./dash.php?section=library">Manage Library</a></li>
HTML;
  if ($cusr->level ==1)
  {
   $html.="<li><a href=\"./dash.php?section=admincp\">Manage Site</li>\n";
  }
  if ($cusr->level <=2)
  {
   $html.="<li><a href=\"./dash.php?section=projects\">Manage Projects</li>\n";
  }
  $html.=<<<HTML
<li><a href="./app.php?action=logout">Logout</a></li>
</ul></div>
HTML;
 }
 else
 {
  if ($GLOBALS['config']['open_registration'] == 'y')
  {
   $registration=" or <button name=\"do\" type=submit value=\"register\">Register a New Account</button>";
  }
  else
  {
   $registration=null;
  }
  $html.=<<<HTML
<form action="./app.php?action=login" method=post>
<li><input type=text placeholder=username: name="name"></li>
<li><input type=password placeholder=password: name="password"></li>
<li><button name="do" type=submit value="login">Login</button>{$registration}</li>
</form>
</ul></div>
HTML;
 }

 return $html;
}
