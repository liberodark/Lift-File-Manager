<?php
require_once 'filemanager_core.php';
$core = new filemanager_core();
if ($core->isLogin() and !isset( $_GET["fid"] ) )
{
    if(isset($_GET["filename"]))
    {
        $file = utf8_decode( base64_decode( $_GET["filename"] ) );
        $info = ROOT_DIR_PATH.$file;
        if($core->check_base_root($info))
        {
            if (file_exists($info))
            {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($info));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($info));
                @ob_clean();
                @flush();
                readfile($info);
                ignore_user_abort(true);
                if (connection_aborted())
                {
                    @unlink($info);
                }
                @unlink($info);
                exit;
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
    }
    else if(isset($_GET["show"]))
    {
        $file = utf8_decode( base64_decode( $_GET["show"] ) );
        $info = ROOT_DIR_PATH.$file;
        $info = str_replace( "//", "/", $info );
        if( $core->check_base_root( $info ) )
        {
            if ( file_exists( $info ) )
            {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename( $info ) );
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize( $info ) );
                @ob_clean();
                @flush();
                readfile( $info );
                exit;
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
    }
    else
    {
        header("Status: 404 Not Found");
    }
}
else
{
    if( isset( $_GET["fid"] ) ) {
        require_once 'option_class.php';
        $option = new option_class();
        $settings = $option->get_option( "settings" );
        if( isset( $settings->download_link ) ) {
            if( $settings->download_link == "on" ) {
                $file = utf8_decode( base64_decode( $_GET["fid"] ) );
                $file = ROOT_DIR_PATH.$file;
                $file = str_replace( "//", "/", $file );
                if( $core->check_base_root( $file ) ) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename='.basename( $file ) );
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize( $file ) );
                    @ob_clean();
                    @flush();
                    readfile( $file );
                    exit();
                }
                else {
                    header("Status: 404 Not Found");
                    exit();
                }
            }
            else {
                header("Status: 404 Not Found");
                exit();
            }
        }
        else {
            header("Status: 404 Not Found");
            exit();
        }
    }
    else {
        header("Status: 404 Not Found");
        exit();
    }
}