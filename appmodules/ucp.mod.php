<?php

function ucp_module($cusr)
{
 $html="<ul class=\"{$type} dropdown-menu\" role=\"menu\">\n";
 if ($cusr->level < 5)
 {
  $html.=<<<HTML
<li><a href="./dash.php?section=library" data-toggle="modal" data-target="AJAXModal">Manage Library</a></li>
HTML;
  if ($cusr->level ==1)
  {
   $html.="<li><a href=\"./dash.php?section=admincp\" data-toggle=\"modal\" data-target=\"#AJAXModal\">Manage Site</a></li>\n";
  }
  if ($cusr->level <=2)
  {
   $html.="<li><a href=\"./dash.php?section=projects\" data-toggle=\"modal\" data-target=\"#AJAXModal\">Manage Projects</a></li>\n";
  }
  $html.=<<<HTML
<li><a href="./app.php?action=logout">Logout</a></li>
</ul>
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
