#!/usr/bin/php

<?php
require dirname(__FILE__)."/appcore/dataconnect/database.inc.php";

fwrite(STDOUT,"Welcome to the Web Comic Manager!\n\nHere you will be able to perform basic functions and data manipulation as if you were logged in as an administrator. Please note: this program may present a security risk. In highly secure production environments it is a good idea to remove this file.\n");
fwrite(STDOUT,"What would you like to do?\n");
fwrite(STDOUT,"1) Look up, view, or edit a user\n");
fwrite(STDOUT,"0) Erase ALL data!\n");
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
 case 0:
  fwrite(STDOUT,"Are you sure you want to Erase ALL data!? This action CANNOT be undone! [Y]es, [N]o ");
  $confirm=trim(strtolower(fgets(STDIN)),"\n");
  if ($confirm == "y" || $confirm == "yes")
  {
   //TODO get a list of tables and drop them!
   $db=new DataBaseSchema();
   $tables=$db->listTables();
   fwrite (STDOUT,"Once again you are about to empty the entire database erasing all data with it. This action is primarily for development servers or in case of database corruption. To continue please type 'yes'! ");
   $final_confirm=trim(fgets(STDIN),"\n");
   if ($final_confirm == "yes")
   {
    foreach ($tables as $table)
    {
     $db->dropTable($table[0]);
    }
    fwrite(STDOUT,"Please verify that the database is now empty using the tools that came with your server software.\n");
    exit();
   }
   else
   {
    fwrite(STDOUT,"You typed '".$final_confirm."' this is not 'yes'. We will now exit without touching the database\n");
    exit();
   }
  }
  else
  {
   exit();
  }
  break;
 default:
 fwrite(STDOUT,"I'm sorry Jim, I can't let you do that!\n");
}

?>
