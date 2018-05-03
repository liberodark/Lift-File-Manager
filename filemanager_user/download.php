<?php
require_once '../filemanager_user_core.php';
$core = new filemanager_user_core();
if ($core->isLogin() and !isset( $_GET["fid"] ) )
{
    if(isset($_GET["filename"]) and isset($_GET["dir"]))
    {
        $file = utf8_decode(base64_decode($_GET["filename"]));
        $info = $file;
        if (file_exists($file))
        {
            $dir = utf8_decode(base64_decode($_GET["dir"]));
            if($core->check_base_root($dir))
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
        $file = utf8_decode(base64_decode($_GET["show"]));
        $file = $core->system_root_dir.ROOT_DIR_PATH.$file;
        $info = $file;
        if (file_exists($file))
        {
            if($core->check_base_root($info))
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
                exit;
            }
            else
            {
                header("Status: 404 Not Found");
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
    if( isset( $_GET["fid"] ) ) {
        require_once 'option_class.php';
        $option = new option_class();
        $settings = $option->get_option( "settings" );
        if( isset( $settings->download_link ) ) {
            if( $settings->download_link == "on" ) {
                $file = utf8_decode( base64_decode( $_GET["fid"] ) );
                $file = $core->system_root_dir.ROOT_DIR_PATH.$file;
                $file = str_replace( "//", "/", $file );
                if( is_file( $file ) ) {
                    if( $core->check_download_base_root( $file ) ) {
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
    header("Status: 404 Not Found");
}