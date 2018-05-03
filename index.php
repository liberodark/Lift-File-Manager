<?php
require_once 'filemanager_core.php';
$core = new filemanager_core();
if( $core->isLogin() ) {
    if( $core->role == "admin" ) {
        if( !function_exists( "language_filter" ) )
        {
            require_once 'filemanager_language.php';
        }
        require_once 'header.php';
        require_once 'menu.php';
        require_once 'content.php';
        require_once 'footer.php';
    }
    else if( $core->role == "user" ) {
        chdir("filemanager_user");
        require_once '../filemanager_user_core.php';
        $core = new filemanager_user_core();
        if( !function_exists( "language_filter" ) )
        {
            require_once '../filemanager_language_user.php';
        }
        $core->userInfo();
        $core->create_user_panel($core->user_id);
        require_once 'header.php';
        require_once 'menu.php';
        require_once 'content.php';
        require_once 'footer.php';
    }
    else {
        if( !function_exists( "language_filter" ) )
        {
            require_once 'filemanager_language.php';
        }
        require_once 'login.php';
    }
}
else {
    if( !function_exists( "language_filter" ) )
    {
        require_once 'filemanager_language.php';
    }
    require_once 'login.php';
}
