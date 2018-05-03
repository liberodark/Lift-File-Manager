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
        $core->adminInfo();
        if( isset( $_POST["share"] ) ) {
            if( @$_POST["admin"] == $core->admin_id ) {
                if( $core->remove_share_file( $_POST["share"] ) ) {
                    echo 'true';
                }
                else {
                    echo 'false';
                }
                exit();
            }
        }
    }
}

if( isset( $_GET["fid"] ) and isset( $_GET["uid"] ) ) {
    if( $core->isLogin() ) {
        $uid = (int) mysql_real_escape_string( $_GET["uid"] );
        $core->adminInfo();
        if( $core->admin_id == $uid ) {
            $core->download_share_file( $_GET["fid"] );
        }
    }
}