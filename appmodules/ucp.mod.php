<?php

function ucp_module($cusr,$type)
{
 $html="<div id=\"UCP\" class=\"{$type}\"><ul class=\"{$type} nobullet\">\n";
 if ($cusr->level <= 7)
 {
  $html.=<<<HTML
<li>Welcome <a href="./?section=profile&item={$cusr->name}&action=view" title="view/edit profile">{$cusr->name}</a>!</li>
<li><a href="./?section=library">Manage Library</a></li>
HTML;
  if ($cusr->level ==1)
  {
   $html.="<li><a href=\"./?section=admincp\">Manage Site</li>\n";
  }
  if ($cusr->level <=2)
  {
   $html.="<li><a href=\"./section=projects\">Manage Projects</li>\n";
  }
  $html.=<<<HTML
<li><a href="?section=app&action=logout">Logout</a></li>
</ul></div>
HTML;
 }
 else
 {
  $html.=<<<HTML
<form action="?section=app&action=login" method=post>
<li><input type=text placeholder=username: name="name"></li>
<li><input type=password placeholder=password: name="password"></li>
<li><button name="do" type=submit value="login">Login</button> or <button name="do" type=submit value="register">Register a New Account</button></li>
</form>
</ul></div>
HTML;
 }

 return $html;
}
