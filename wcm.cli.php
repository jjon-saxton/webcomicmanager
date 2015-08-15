#!/usr/bin/php

<?php
require dirname(__FILE__)."/appcore/dataconnect/database.inc.php";

fwrite(STDOUT,"Welcome to the Web Comic Manager!\n\nHere you will be able to perform basic functions and data manipulation as if you were logged in as an administrator. Please note: this program may present a security risk. In highly secure production environments it is a good idea to remove this file.\n");
fwrite(STDOUT,"What would you like to do?\n");
fwrite(STDOUT,"1) Look up, view, or edit a user\n");
$select=fgets(STDIN);
switch ($select)
{
 case 1:
 $table=new DataBaseTable('users');
 fwrite(STDOUT,"Search query (press enter to look up all users): ");
 $query=fgets(STDIN);
 $query=$table->getData($query);
 while ($row=$query->fetch(PDO::FETCH_OBJ))
 {
  fwrite(STDOUT,$row->id."|".$row->name."|".$row->email."\n");
 }
 fwrite(STDOUT,"Select a user's id: ");
 $id=trim(fgets(STDIN),"\n");
 if (!is_numeric($id))
 {
  exit();
 }
 else
 {
  $query=$table->getData("id:'= {$id}'");
  $user=$query->fetch(PDO::FETCH_ASSOC);
  foreach ($user as $key=>$value)
  {
   if ($key == 'password')
   {
    $value="****";
   }
   fwrite (STDOUT,$key.": ".$value."\n");
  }
  fwrite (STDOUT,"What would you like to do? [E]dit, [U]pgrade, [D]elete, E[x]it ");
  $action=trim(strtolower(fgets(STDIN)),"\n");
  switch ($action)
  {
   case 'edit':
   case 'e':
   break;
   case 'upgrade':
   case 'u':
   break;
   case 'delete':
   case 'd':
   break;
   case 'exit':
   case 'x':
   exit();
   break;
   default:
   fwrite(STDOUT,"Really? That's what you're going with? Not even going to try for the options huh?\n");
  }
 }
 break;
 default:
 fwrite(STDOUT,"I'm sorry Jim, I can't let you do that!\n");
}

?>
