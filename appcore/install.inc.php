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
            ?><form action="./?section=app&action=install&step=2" method="post"><div class="form">
                <h2>Set-up DataConnect</h2>
                <p>This page is designed to help you set-up DataConnect to connect to the database software on your server. Please fill out the form below. Fields marked with and astrix '<span class="required">*</span>' are required.</p>
                <table width="100%" border=0 cellspacing=1 cellpadding=1>
                    <tr>
                        <th colspan=2>Server</th>
                    </tr>
                    <tr>
                        <td align=right>Server Type<span class="required">*</span>:</td><td align=left><input type="text" required="required" name="database[driver]"/></td>
                    </tr>
                    <tr>
                        <td align=right>Hostname/Address<span class="required">*</span>:</td><td align=left><input type="text" required="required" name="database[host]"/></td>
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
                        <td align="right">User<span class="required">*</span>:</td><td align="left"><input type="text" required=required name="schema[username]"/></td>
                    </tr>
                    <tr>
                        <td align="right">Password:</td><td align="left"><input type="password" name="schema[password]"/></td>
                    </tr>
                    <tr>
                        <td align="right">Table Prefix:</td><td align="left"><input type="text" name="schema[tableprefix]"/>_</td>
                    </tr>
                    <tr>
                        <td align="right"><button onclick="history.back()">Previous</button></td><td align="left"><button type="submit">Continue</button></td>
                    </tr>
            </div></form><?php
            break;
        case 2:
            if(empty($_POST['driver']))
            {
                if (is_writable(dirname(__FILE__)."/dataconnect/"))
                {
                    if (write_ini_file($_POST,dirname(__FILE__)."/dataconnect/database.ini"))
                    {
                        header("Location:./?section=app&action=install&step=2");
                    }
                }
            }
    }
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