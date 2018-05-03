<?php
if( !defined( "DB_HOST" ) ) {
    require_once '../filemanager_config.php';
}
class set_users_and_share_root
{
    var $db;
    var $msg1;
    var $msg2;
    var $msg3;
    function __construct()
    {
        $this->db = mysql_connect(DB_HOST,DB_USER,DB_PASS);
        mysql_select_db(DB_NAME);
    }

    public function change_root_dir()
    {
        if( CHANGE_ROOT ) {
            $last = urlencode( LAST_ROOT_DIR_PATH );
            $new = urlencode( ROOT_DIR_PATH );

            $update_users = mysql_query( "UPDATE filemanager_users
                       SET dir_path=REPLACE( dir_path, '$last', '$new' )" );

            $update_share = mysql_query( "UPDATE filemanager_shares
                       SET file_path=REPLACE( file_path, '$last', '$new' )" );

            $update_extra_dir = mysql_query( "UPDATE filemanager_extra_dir
                       SET dir_path=REPLACE( dir_path, '$last', '$new' )" );

            if( $update_users ) {
                $this->msg1 = '<div class="alert alert-success text-center" style="font-weight: bold;">Root Directory has been set for users.</div>';
            }
            else {
                $this->msg1 = '<div class="alert alert-danger text-center" style="font-weight: bold;">Root Directory has not been set for users.</div>';
            }

            if( $update_share ) {
                $this->msg2 = '<div class="alert alert-success text-center" style="font-weight: bold;">Root Directory has been set for share files</div>';
            }
            else {
                $this->msg2 = '<div class="alert alert-danger text-center" style="font-weight: bold;">Root Directory has not been set for share files</div>';
            }

            if( $update_extra_dir ) {
                $this->msg3 = '<div class="alert alert-success text-center" style="font-weight: bold;">Root Directory has been set for extra directories</div>';
            }
            else {
                $this->msg3 = '<div class="alert alert-danger text-center" style="font-weight: bold;">Root Directory has not been set for extra directories</div>';
            }
        }
    }
}