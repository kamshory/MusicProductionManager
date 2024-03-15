<?php

use MagicObject\Request\InputPost;
use MusicProductionManager\Utility\ServerUtil;

require_once "inc/app.php";

$inputPost = new InputPost();

if($inputPost->hasValueInstall())
{
    if(ServerUtil::getOs() == ServerUtil::OS_WINDOWS)
    {
        installWindows($inputPost);
    }
    else if(ServerUtil::getOs() == ServerUtil::OS_LINUX)
    {
        installLinux($inputPost);
    }
}

/**
 * Install on Windows
 *
 * @param InputPost $input
 * @return void
 */
function installWindows($input)
{
    if($input->hasValueDatabaseType())
    {
        $dbType = $input->getDatabaseType();
        shell_exec("SETX APP_DATABASE_TYPE \"$dbType\"");
    }
    if($input->hasValueDatabaseHost())
    {
        $dbHost = $input->getDatabaseHost();
        shell_exec("SETX APP_DATABASE_HOST \"$dbHost\"");
    }
    if($input->hasValueDatabasePort())
    {
        $dbPort = $input->getDatabasePort();
        shell_exec("SETX APP_DATABASE_PORT \"$dbPort\"");
    }
    if($input->hasValueDatabaseSchema())
    {
        $dbSchema = $input->getDatabaseSchema();
        shell_exec("SETX APP_DATABASE_SCHEMA \"$dbSchema\"");
    }
    if($input->hasValueDatabaseUser())
    {
        $dbUser = $input->getDatabaseUser();
        shell_exec("SETX APP_DATABASE_USER \"$dbUser\"");
    }
    if($input->hasValueDatabasePassword())
    {
        $dbPass = $input->getDatabasePassword();
        shell_exec("SETX APP_DATABASE_PASSWORD \"$dbPass\"");
    }
    if($input->hasValueDatabaseSalt())
    {
        $dbSalt = $input->getDatabaseSalt();
        shell_exec("SETX APP_DATABASE_SALT \"$dbSalt\"");
    }
}
/**
 * Install on Linux
 *
 * @param InputPost $input
 * @return void
 */
function installLinux($input)
{
    $path = "/etc/httpd/conf.d/music.conf";
    truncateFile($path);
    if($input->hasValueDatabaseType())
    {
        $dbType = $input->getDatabaseType();
        appendFile($path, "SETENV APP_DATABASE_TYPE \"$dbType\"");
    }
    if($input->hasValueDatabaseHost())
    {
        $dbHost = $input->getDatabaseHost();
        appendFile($path, "SETENV APP_DATABASE_HOST \"$dbHost\"");
    }
    if($input->hasValueDatabasePort())
    {
        $dbPort = $input->getDatabasePort();
        appendFile($path, "SETENV APP_DATABASE_PORT \"$dbPort\"");
    }
    if($input->hasValueDatabaseSchema())
    {
        $dbSchema = $input->getDatabaseSchema();
        appendFile($path, "SETENV APP_DATABASE_SCHEMA \"$dbSchema\"");
    }
    if($input->hasValueDatabaseUser())
    {
        $dbUser = $input->getDatabaseUser();
        appendFile($path, "SETENV APP_DATABASE_USER \"$dbUser\"");
    }
    if($input->hasValueDatabasePassword())
    {
        $dbPass = $input->getDatabasePassword();
        appendFile($path, "SETENV APP_DATABASE_PASSWORD \"$dbPass\"");
    }
    if($input->hasValueDatabaseSalt())
    {
        $dbSalt = $input->getDatabaseSalt();
        appendFile($path, "SETENV APP_DATABASE_SALT \"$dbSalt\"");
    }
}

function truncateFile($path)
{
    file_put_contents($path, "");
}
function appendFile($path, $content)
{
    $fp = fopen($path, "w+");
    fputs($fp, $content);
    fclose($fp);
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install</title>
</head>

<body>

    <div class="all">
        <div class="form">
            <form action="">
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Application Name</td>
                            <td><input type="text" name="application_name" id="application_name"></td>
                        </tr>
                        <tr>
                            <td>Base URL</td>
                            <td><input type="url" name="base_url" id="base_url"></td>
                        </tr>
                        <tr>
                            <td>Database Type</td>
                            <td><select name="database_type" id="database_type">
                                    <option value="mariadb">MariaDB</option>
                                    <option value="mysql">MySQL</option>
                                    <option value="postgresql">PostgreSQL</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Database Host</td>
                            <td><input type="text" name="database_host" id="database_host"></td>
                        </tr>
                        <tr>
                            <td>Database Port</td>
                            <td><input type="number" step="1" min="0" max="65535" name="database_port" id="database_port"></td>
                        </tr>
                        <tr>
                            <td>Database Schema</td>
                            <td><input type="text" name="database_schema" id="database_schema"></td>
                        </tr>
                        <tr>
                            <td>Database Name</td>
                            <td><input type="text" name="database_name" id="database_name"></td>
                        </tr>
                        <tr>
                            <td>Database Username</td>
                            <td><input type="text" name="database_usernname" id="database_usernname"></td>
                        </tr>
                        <tr>
                            <td>Database Password</td>
                            <td><input type="text" name="database_password" id="database_password"></td>
                        </tr>
                        <tr>
                            <td>Database Salt</td>
                            <td><input type="text" name="database_salt" id="database_salt"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" name="install" value="Install"></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

</body>

</html>