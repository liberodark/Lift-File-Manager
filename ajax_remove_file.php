<?php
if (!isset($core))
{
	require_once 'filemanager_core.php';
	$core = new filemanager_core();
}
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
{
    if ($core->isLogin())
    {
        if(isset($_POST["filename"]))
        {
            if($core->check_base_root($_POST["filename"]))
            {
                $remove = new filemanager_backups();
                $remove->remove_this_backup_file( $_POST["filename"] );
            }
            else
            {
                echo "F2";
            }
        }
    }
    else
    {
        header("Status: 404 Not Found");
    }
}
else
{
	header("Status: 404 Not Found");
}