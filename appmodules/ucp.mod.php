<?php

function ucp_module(MCSession $cusr, $siteroot, $registration)
{
 $html="<ul class=\"{$type} dropdown-menu\" role=\"menu\">\n";
 if ($cusr->level < 5)
 {
  $html.=<<<HTML
<li><a href="//{$siteroot}/dash/?section=library" data-toggle="modal" data-target="#AJAXModal">Manage Library</a></li>
HTML;
  if ($cusr->level ==1)
  {
   $html.="<li><a href=\"//{$siteroot}/dash/?section=admincp\" data-toggle=\"modal\" data-target=\"#AJAXModal\">Manage Site</a></li>\n";
  }
  if ($cusr->level <=2)
  {
   $html.="<li><a href=\"//{$siteroot}/dash/?section=projects\" data-toggle=\"modal\" data-target=\"#AJAXModal\">Manage Projects</a></li>\n";
  }
  $html.=<<<HTML
<li><a href="//{$siteroot}/app/?action=logout">Logout</a></li>
</ul>
HTML;
 }
 else
 {
  if (!empty($registration))
  {
   $registration=" or <button name=\"do\" class=\"btn btn-info\" type=submit value=\"register\">Register a New Account</button>";
  }
  else
  {
   $registration=null;
  }
  $html.=<<<HTML
<form action="//{$siteroot}/app/?action=login" method=post>
<li><label for="uname">Username</label>
<input class="form-control" id="uname" type=text placeholder=username: name="name"></li>
<li><label for="pword">Password</label>
<input class="form-control" id="pword" type=password placeholder=password: name="password"></li>
<li><button class="btn btn-primary" name="do" type=submit value="login">Login</button>{$registration}</li>
</form>
</ul>
HTML;
 }

 return $html;
}
