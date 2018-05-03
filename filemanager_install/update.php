<?php
if(!isset($_POST["install"]))
{
    require_once '../filemanager_config.php';
    require_once '../filemanager_assets/JSON.php';
}
class UPDATE_V_3_0_0 extends Services_JSON
{
    var $db;
    var $install_flag = false;
    function __construct()
    {
        $this->db = mysql_connect(DB_HOST,DB_USER,DB_PASS);
        mysql_select_db(DB_NAME);
    }

    public function update()
    {
        $table_query = "CREATE TABLE IF NOT EXISTS filemanager_extra_dir(
            id INT NOT NULL AUTO_INCREMENT,
            PRIMARY KEY(id),
            user_id INT,
            dir_path TEXT,
            FOREIGN KEY (user_id) REFERENCES filemanager_users(id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        if( mysql_query( $table_query ) ) {
            $table_query = "CREATE TABLE IF NOT EXISTS filemanager_shares(
            id INT NOT NULL AUTO_INCREMENT,
            PRIMARY KEY(id),
            user_id INT ,
            admin INT ,
            file_path TEXT,
            description TEXT,
            role TEXT,
            date_added DATETIME,
            FOREIGN KEY (user_id) REFERENCES filemanager_users(id)
            ON UPDATE CASCADE
            ON DELETE CASCADE,
            FOREIGN KEY (admin) REFERENCES filemanager_db(id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
            if( mysql_query( $table_query ) ) {
                if(!$this->install_flag)
                {
                    require_once 'set_users_and_share_root.php';
                    $set_root = new set_users_and_share_root();
                    $set_root->change_root_dir();
                    echo '<div class="alert alert-success" style="text-align: center; font-weight: bold;">';
                    echo "DONE: Your system has been updated to new version.";
                    echo '</div>';
                    echo $set_root->msg1;
                    echo $set_root->msg2;
                    echo $set_root->msg3;
                }
            }
            else {
                $this->show_error();
            }
        }
        else {
            $this->show_error();
        }
    }

    private function show_error()
    {
        echo '<div class="alert alert-danger" style="text-align: center; font-weight: bold;">';
        echo "ERROR: ".mysql_error();
        echo '</div>';
    }
}
if(!isset($_POST["install"]))
{
?>
<html>
    <head>
        <meta name="robots" content="noindex">
        <meta name="googlebot" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css"  href="../filemanager_css/bootstrap.css">
    </head>
    <body>
<?php
        $update = new UPDATE_V_3_0_0();
        $update->update();
?>
    </body>
</html>
<?php
}
?>
