<?php
require_once '../filemanager_config.php';
require_once '../filemanager_assets/JSON.php';
class INSTALL extends Services_JSON
{
	var $db;
    public $status = false;
	function __construct()
	{
        try {
            $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
        } catch (Exception $exception){
            return $exception->getMessage();
        }
	}

    public function mysql_request($query) {
        try {
            $request_response = $this->db->query($query);
            return $request_response;
        } catch (Exception $exception){
            return $exception->getMessage();
        }
    }

    public function quote($txt){
        return $this->db->quote($txt);
    }

    protected function encode_me($txt)
    {
        $txt = strip_tags($txt);
        $txt = $this->quote($txt);
        $txt = urlencode($txt);
        return $txt;
    }

    protected function decode_me($txt, $share = false)
    {
        $txt = urldecode($txt);
        if( $share ) {
            $txt = str_replace("\\n", "<br />", $txt);
            $txt = str_replace("\\r", "       ", $txt);
        }
        $txt = stripslashes($txt);
        return $txt;
    }

	public function _install($firstname,$lastname,$username,$email,$password)
	{
		$table_query = "CREATE TABLE IF NOT EXISTS filemanager_db(
				  id INT NOT NULL AUTO_INCREMENT, 
				  PRIMARY KEY(id),
				  firstname TEXT,
				  lastname TEXT,
				  username TEXT,
				  email TEXT,
				  password TEXT,
				  is_login TINYINT,
				  ck_id TEXT,
				  luck_time DATETIME,
				  luck_count TINYINT,
				  date_added DATETIME
				  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        if($this->mysql_request($table_query))
		{
			$date_added = date("YmdHis");
			$is_login = 0;
			$is_admin = 1;
			$is_block = 0;
            $email_poasword = $password;
			$password = md5($password);
			$insert_query = "INSERT INTO filemanager_db (firstname,lastname,username,email,password,is_login,date_added) VALUES ('$firstname','$lastname','$username','$email','$password','$is_login','$date_added')";
			if($this->mysql_request($insert_query))
			{
                $table_query = "CREATE TABLE IF NOT EXISTS filemanager_options(
				  id INT NOT NULL AUTO_INCREMENT,
				  PRIMARY KEY(id),
				  option_name VARCHAR(30),
				  option_content TEXT
				  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"; // UPDATE V1.0.1
                if($this->mysql_request($table_query))
                {
                    $table_query = "CREATE TABLE IF NOT EXISTS filemanager_users(
                              id INT NOT NULL AUTO_INCREMENT,
                              PRIMARY KEY(id),
                              firstname TEXT,
                              lastname TEXT,
                              username TEXT,
                              email TEXT,
                              password TEXT,
                              is_login TINYINT,
                              ck_id TEXT,
                              luck_time DATETIME,
                              luck_count TINYINT,
                              activation_key TEXT,
                              is_block TINYINT,
                              dir_path TEXT,
                              date_added DATETIME
				          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";// UPDATE V1.0.1
                    if($this->mysql_request($table_query))
                    {
                        $name = "allow_extensions";
                        $content = array('rar','zip','txt','pdf','jpg','jpeg','png','gif','bmp','psd','flv','mp4');
                        $content = json_encode($content);
                        $insert_options = "INSERT INTO filemanager_options (option_name, option_content) VALUES ('$name' , '$content')";
                        if($this->mysql_request($insert_options))
                        {

                            $name = "allow_uploads";
                            $content =  array("gif", "jpeg", "jpg", "png", "txt", "zip", "rar", "psd", "flv");
                            $content = json_encode($content);
                            $insert_options = "INSERT INTO filemanager_options (option_name, option_content) VALUES ('$name' , '$content')";
                            if($this->mysql_request($insert_options))
                            {
                                /*
                                * Mime type of upload extensions
                                */
                                $mime_type = array(
                                    "jpg" => array("image/jpeg", "image/pjpeg", "application/octet-stream"),
                                    "jpeg" => array("image/jpeg", "image/pjpeg", "application/octet-stream"),
                                    "bmp" => array("image/bmp", "application/octet-stream"),
                                    "gif" => array("image/gif", "application/octet-stream"),
                                    "pdf" => array("application/pdf", "application/zip", "application/octet-stream"),
                                    "zip" => array("application/zip", "application/octet-stream", "application/download"),
                                    "rar" => array("application/x-rar-compressed", "application/octet-stream", "application/download", "application/x-rar"),
                                    "doc" => array("application/msword", "text/html", "application/octet-stream"),
                                    "docx" => array("application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/zip", "application/octet-stream"),
                                    "xls" => array("application/vnd.ms-excel", "text/html", "application/octet-stream"),
                                    "xlsx" => array("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/zip", "application/octet-stream"),
                                    "ppt" => array("application/vnd.ms-powerpoint", "text/html", "application/vnd.ms-office", "application/octet-stream"),
                                    "pptx" => array("application/vnd.openxmlformats-officedocument.presentationml.presentation", "application/zip", "application/octet-stream"),
                                    "psd" => array("image/photoshop", "image/x-photoshop", "image/psd", "application/photoshop", "application/psd", "zz-application/zz-winassoc-psd", "application/octet-stream"),
                                    "flv" => array("video/x-flv", "application/octet-stream"),
                                    "mp3" => array("audio/mpeg", "audio/mp3", "audio/mpeg3", "audio/x-mpeg-3", "video/mpeg", "video/x-mpeg", "application/octet-stream", "video/mp4"),
                                    "mp4" => array("video/mp4v-es", "audio/mp4", "application/octet-stream", "video/mp4"),
                                    "wav" => array("audio/wav", "audio/x-wav", "audio/wave", "audio/x-pn-wav", "application/octet-stream"),
                                    "mov" => array("video/quicktime", "video/x-quicktime", "image/mov", "audio/aiff", "audio/x-midi", "audio/x-wav", "video/avi", "application/octet-stream"),
                                    "avi" => array("video/avi", "video/msvideo", "video/x-msvideo", "image/avi", "video/xmpg2", "application/x-troff-msvideo", "audio/aiff", "audio/avi", "application/octet-stream")
                                );

                                $name = "allow_uploads_mime_type";
                                $content = json_encode($mime_type);
                                $insert_options = "INSERT INTO filemanager_options (option_name, option_content) VALUES ('$name' , '$content')";
                                if($this->mysql_request($insert_options))
                                {
                                    $core_folder = ROOT_DIR_PATH;
                                    $core_folder = realpath($core_folder);
                                    $name = "base_root_folder";
                                    $core_folder = base64_encode($core_folder);
                                    $insert_options = "INSERT INTO filemanager_options (option_name, option_content) VALUES ('$name' , '$core_folder')";
                                    if($this->mysql_request($insert_options))
                                    {
                                        $ticket_table = "CREATE TABLE IF NOT EXISTS filemanager_tickets (
                                            id INT NOT NULL AUTO_INCREMENT,
                                            PRIMARY KEY(id),
                                            parentId INT,
                                            userId INT,
                                            role VARCHAR(15),
                                            subject TEXT,
                                            message TEXT,
                                            status VARCHAR(15),
                                            adminTicket SMALLINT,
                                            dateadded DATETIME
                                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
                                        if($this->mysql_request($ticket_table))
                                        {
                                            $name = "settings";
                                            $content = array(
                                                'ticket' => 'off',
                                                'share' => 'off',
                                                'system_share' => 'off',
                                                'download_link' => 'off',
                                                'register' => 'off',
                                                'admin_notification' => 'off',
                                                'user_notification' => 'off'
                                            );
                                            $content = json_encode($content);
                                            $insert_options = "INSERT INTO filemanager_options (option_name, option_content) VALUES ('$name' , '$content')";
                                            if($this->mysql_request($insert_options))
                                            {
                                                $name = "register_settings";
                                                $content = array(
                                                    'permissions' => array(),
                                                    'allow_ext' => array(),
                                                    'allow_upload' => array(),
                                                    'upload_limitation' => 1,
                                                    'size_limitation' => 5,
                                                    'users_dir' => ''
                                                );
                                                $content = json_encode($content);
                                                $insert_options = "INSERT INTO filemanager_options (option_name, option_content) VALUES ('$name' , '$content')";
                                                if($this->mysql_request($insert_options))
                                                {
                                                    $email = urldecode($email);
                                                    $username = urldecode($username);
                                                    $firstname = urldecode($firstname);
                                                    $lastname = urldecode($lastname);

                                                    $subject = "Lift File Manager has been installed.";
                                                    $message = "your username: ".$username." your password: ".$email_poasword." you can login with your username or your email";
                                                    $header = "FROM: noreply@codstack.com";
                                                    if(@mail($email, $subject, $message, $header))
                                                    {
                                                        echo '<div class="alert alert-success"><center>';
                                                        echo "Installing Lift File Manager has been finished and an email has been sent to you. Thank you ".$firstname." ".$lastname;
                                                        echo '</center></div>';
                                                    }
                                                    else
                                                    {
                                                        echo '<div class="alert alert-success"><center>';
                                                        echo "Installing Lift File Manager has been finished. Thank you ".$firstname." ".$lastname;
                                                        echo '</center></div>';
                                                    }
                                                    $this->status = true;
                                                }
                                                else
                                                {
                                                    echo '<div class="alert alert-error"><center>';
                                                    echo "ERROR: ".mysql_error();
                                                    echo '</center></div>';
                                                }
                                            }
                                            else
                                            {
                                                echo '<div class="alert alert-error"><center>';
                                                echo "ERROR: ".mysql_error();
                                                echo '</center></div>';
                                            }
                                        }
                                        else
                                        {
                                            echo '<div class="alert alert-error"><center>';
                                            echo "ERROR: ".mysql_error();
                                            echo '</center></div>';
                                        }
                                    }
                                    else
                                    {
                                        echo '<div class="alert alert-error"><center>';
                                        echo "ERROR: ".mysql_error();
                                        echo '</center></div>';
                                    }
                                }
                                else
                                {
                                    echo '<div class="alert alert-error"><center>';
                                    echo "ERROR: ".mysql_error();
                                    echo '</center></div>';
                                }
                            }
                            else
                            {
                                echo '<div class="alert alert-error"><center>';
                                echo "ERROR: ".mysql_error();
                                echo '</center></div>';
                            }
                        }
                        else
                        {
                            echo '<div class="alert alert-error"><center>';
                            echo "ERROR: ".mysql_error();
                            echo '</center></div>';
                        }
                    }
                    else
                    {
                        echo '<div class="alert alert-error"><center>';
                        echo "ERROR: ".mysql_error();
                        echo '</center></div>';
                    }
                }
                else
                {
                    echo '<div class="alert alert-error"><center>';
                    echo "ERROR: ".mysql_error();
                    echo '</center></div>';
                }
			}
			else
			{
				echo '<div class="alert alert-error"><center>';
				echo "ERROR: ".mysql_error();
				echo '</center></div>';
			}
		}
		else
		{
			echo '<div class="alert alert-error"><center>';
			echo "ERROR: ".mysql_error();
			echo '</center></div>';
		}
	}
}
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
if(isset($_POST["install"]))
{
	$firstname = urlencode($_POST["firstname"]);
	$lastname = urlencode($_POST["lastname"]);
	$username = urlencode($_POST["username"]);
	$email = urlencode($_POST["email"]);
	$password = $_POST["password"];
	$install = new INSTALL();
	$install->_install($firstname, $lastname, $username, $email, $password);
    if($install->status)
    {
        require_once 'update.php';
        $update = new UPDATE_V_3_0_0();
        $update->install_flag = true;
        $update->update();
    }
}
?>
</body>