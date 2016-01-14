<?php

function build_installer($step)
{
    switch ($step)
    {
        case 0:
            ?><div class="message">
                <h2>Welcome to Tower21 WebComiX Manager</h2>
                <p>The following pages are designed to assist you in setting up your server to run this application. The application will assist you and your users in managing WebComiX and related projects.</p>
                <div align=center><button onclick="window.location='http://www.tower21studios.com'">Cancel</button> <button onclick="window.location='./?section=app&action=install&step=1'">Get Started</button></div>
            </div><?php
            break;
        case 1:
            $types=pdo_drivers();
            @$host=getenv("IP");
            @$uname=getenv("C9_USER");
            ?><form action="./?section=app&action=install&step=2" method="post"><div class="form">
                <h2>Set-up DataConnect</h2>
                <p>This page is designed to help you set-up DataConnect to connect to the database software on your server. Please fill out the form below. Fields marked with and astrix '<span class="required">*</span>' are required.</p>
                <table width="100%" border=0 cellspacing=1 cellpadding=1>
                    <tr>
                        <th colspan=2>Server</th>
                    </tr>
                    <tr>
                        <td align=right>Server Type<span class="required">*</span>:</td><td align=left><select required="required" name="database[driver]"/><?php foreach ($types as $driver) { ?><option><?php print $driver ?></option> <?php } ?></select></td>
                    </tr>
                    <tr>
                        <td align=right>Hostname/Address<span class="required">*</span>:</td><td align=left><input type="text" required="required" name="database[host]" value="<?php print $host ?>"/></td>
                    </tr>
                    <tr>
                        <td align="right">Port:</td><td align="left"><input type="number" name="database[port]"></td>
                    </tr>
                    <tr>
                        <th colspan="2">Schema</th>
                    </tr>
                    <tr>
                        <td align="right">Database Name<span class="required">*</span>:</td><td align="left"><input type="text" required="required" name="schema[name]"/></td>
                    </tr>
                    <tr>
                        <td align="right">User<span class="required">*</span>:</td><td align="left"><input type="text" required=required name="schema[username]" value="<?php print $uname ?>"/></td>
                    </tr>
                    <tr>
                        <td align="right">Password:</td><td align="left"><input type="password" name="schema[password]"/></td>
                    </tr>
                    <tr>
                        <td align="right">Table Prefix:</td><td align="left"><input type="text" name="schema[tableprefix]"/></td>
                    </tr>
                    <tr>
                        <td align="right"><button onclick="history.back()">Previous</button></td><td align="left"><button type="submit">Continue</button></td>
                    </tr>
            </div></form><?php
            break;
        case 2:
            if(!empty($_POST['database']))
            {
                if (is_writable(dirname(__FILE__)."/dataconnect/"))
                {
                    if (write_ini_file($_POST,dirname(__FILE__)."/dataconnect/connect.ini"))
                    {
                        header("Location:./?section=app&action=install&step=2");
                    }
                }
            }
            else
            { ?><div class="message">
                <h2>Create Database Tables</h2>
                <p>With DataConnect set up you are now ready to create the tables that the WebComiX Manager requires in order to function. This may take a few moments.</p>
                <div align=center><button onclick="history.back()">Previous</button> <button onclick="window.location='./?section=app&action=install&step=3'">Continue</button></div>
            </div><?php }
            break;
        case 3:
            if (set_tables())
            { ?><form action="?=section=app&action=install&step=4" method="post"><div class="form">
                <h2>Populate Tables</h2>
                <p>Now that the database tables have been created we are ready to populate them with their default data, however, we need to know what some of that data should be. Fill out the form below to help us.</p>
            </div></form>
            <?php }
    }
}

function set_tables()
{
    //#Table definition for 'settings' table
    $def['settings'][0]="`key` VARCHAR(30) NOT NULL PRIMARY KEY";
    $def['settings'][1]="`value` VARCHAR(20)";
    
    //#Table definition for 'genres' table
    $def['genres'][0]="`num` INT(4) NOT NULL PRIMARY KEY AUTO_INCREMENT";
    $def['genres'][1]="`name` VARCHAR(160)";
    
    //#Table definition for 'logs' table
    $def['logs'][0]="`num` INT(11) PRIMARY KEY AUTO_INCREMENT";
    $def['logs'][1]="`time` DATETIME";
    $def['logs'][2]="`code` INT(11)";
    $def['logs'][3]="`action` VARCHAR(20)";
    $def['logs'][4]="`message` TEXT";
    
    //#Table definition for 'users' table
    $def['users'][0]="`num` INT(255) PRIMARY KEY AUTO_INCREMENT";
    $def['users'][1]="`registered` DATETIME";
    $def['users'][2]="`name` VARCHAR(160)";
    $def['users'][3]="`first` TEXT";
    $def['users'][4]="`last` TEXT";
    $def['users'][5]="`birtdate` DATE";
    $def['users'][6]="`email TEXT";
    $def['users'][7]="`level` INT(1)";
    $def['users'][8]="`level_time` INT(4)";
    $def['users'][9]="`level_date DATETIME";
    $def['users'][10]="`passes` TEXT";
    $def['users'][11]="`library` TEXT";
    
    //#Table definition for 'comments' table
    $def['comments'][0]="`num` INT(255) PRIMARY KEY AUTO_INCREMENT";
    $def['comments'][1]="`subject VARCHAR(160) NOT NULL";
    $def['comments'][2]="`ctype` VARCHAR(60)";
    $def['comments'][3]="`cid` INT(255)";
    $def['comments'][4]="`aid` INT(255)";
    $def['comments'][5]="`leid` INT(255)";
    $def['comments'][6]="`enotes` TEXT";
    $def['comments'][7]="`comment` TEXT";
    
    //#Table definition for 'projects' table
    $def['projects'][0]="`num` INT(255) PRIMARY KEY AUTO_INCREMENT";
    $def['projects'][1]="`title` VARCHAR(160)";
    $def['projects'][2]="`aid INT(255)";
    $def['projects'][3]="`created` DATETIME";
    $def['projects'][4]="`modified` DATETIME";
    $def['projects'][5]="`genre_id` INT(3)";
    $def['projects'][6]="`editors` TEXT";
    $def['projects'][7]="`children` TEXT";
    $def['projects'][8]="`tags` TEXT";
    $def['projects'][9]="`description` TEXT";
    $def['projects'][10]="`notes` TEXT";
    
    //#Table definition for 'volumes' table
    $def['volumes'][0]="`num` INT(255) PRIMARY KEY AUTO_INCREMENT";
    $def['volumes'][1]="`created` DATETIME";
    $def['volumes'][2]="`modified` DATETIME";
    $def['volumes'][3]="`title` VARCHAR(160)";
    $def['volumes'][4]="`pid` INT(255)";
    $def['volumes'][5]="`children` TEXT";
    $def['volumes'][6]="`notes` TEXT";
    
    //#Table definition for 'issues' table
    $def['issues'][0]="`num` INT(255) PRIMARY KEY AUTO_INCREMENT";
    $def['issues'][1]="`created` DATETIME";
    $def['issues'][2]="`modified` DATETIME";
    $def['issues'][3]="`price` INT(5)";
    $def['issues'][4]="`title` VARCHAR(160)";
    $def['issues'][5]="`pid` INT(255)";
    $def['issues'][6]="`script` TEXT";
    $def['issues'][7]="`pdf` TEXT";
    $def['issues'][8]="`notes` TEXT";
    
    $okay=0;
    $tottables=0;
    $db=new DataBaseSchema(null,dirname(__FILE__)."/dataconnect/connect.ini");
    foreach ($def as $tablename=>$cols)
    {
        if ($table=$db->addTable($tablename,$cols))
        {
        $okay++;
        }
        $tottables++;
    }
  
    if ($okay == $tottables)
    {
        return true;
    }
    else
    {
        trigger_error("Only ".$okay." of ".$tottables." were created! Please empty the database and try again!",E_USER_WARNING);
        return false;
    }
}

function put_defaults($admin,$guest,$settings)
{
    //TODO place the three arrays into their proper tables and fill any app default
}

function write_ini_file($array,$file=null)
{
    $text=null;
    foreach ($array as $key=>$val)
    {
        if (is_array($val))
        {
            $text.="[{$key}]\n";
            foreach ($val as $skey=>$sval)
            {
                $text.="{$skey} =".(is_numeric($skey) ? $sval : '"'.$sval.'"')."\n";
            }
            $text.="\n";
        }
        else
        {
            $text.="{$key} = ".(is_numeric($key) ? $val : '"'.$val.'"')."\n";
        }
    }
    
    if (empty($file))
    {
        return $text;
    }
    else
    {
        return file_put_contents($file,$text);
    }
}