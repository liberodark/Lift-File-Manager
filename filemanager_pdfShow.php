<?php
if (!isset($core))
{
    require_once 'filemanager_core.php';
    $core = new filemanager_core();
    require_once 'filemanager_language.php';
}
if ($core->isLogin())
{
    if( isset($_GET["filename"] ) ) {
        $file = utf8_decode(base64_decode($_GET["filename"]));
        $file = str_replace("//", "/", $file);
        $ext = strtolower( end( explode( ".", $file ) ) );
        if( $ext == "pdf" ) {
            if( $core->check_base_root( $file ) ) {
                if( is_file( $file ) ) {
                    $filename = basename( $file );
                    header('Content-type: application/pdf');
                    header('Content-Disposition: inline; filename="' . $filename . '"');
                    header('Content-Transfer-Encoding: binary');
                    header('Accept-Ranges: bytes');
                    @readfile($file);
                }
            }
        }
    }
}