<?php
session_start();
error_reporting(-1);
ini_set('log_errors',TRUE);
ini_set('html_errors',TRUE);
ini_set('error_log','filemanager_error_log.txt');
ini_set('display_errors',TRUE);
include 'filemanager_config.php';
require_once 'filemanager_assets/JSON.php';
class filemanager_core extends Services_JSON
{
    var $db;
    public $admin_firstname = "";
    public $admin_lastname = "";
    public $admin_email = "";
    public $admin_id = "";
    public $admin_username = "";
    public $pageCount = 1;
    public $start; // for loop start
    public $end; // for loop end
    public $role;
    public $share_users = array();
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

    private function encode_me($txt)
    {
        $txt = strip_tags($txt);
        $txt = $this->quote($txt);
        $txt = urlencode($txt);
        return $txt;
    }

    private function decode_me($txt, $share = false)
    {
        $txt = urldecode($txt);
        if( $share ) {
            $txt = str_replace("\\n", "<br />", $txt);
            $txt = str_replace("\\r", "       ", $txt);
        }
        $txt = stripslashes($txt);
        return $txt;
    }
    public function filter_txt( $txt, $deny = false )
    {
        if( $deny ) {
            $root = realpath( ROOT_DIR_PATH );
            $txt = str_replace( $root, ROOT_DIR_PATH, $txt );
            $txt = str_replace( "\\", "/", $txt );
            $txt = str_replace( "\t", "t", $txt );
            $txt = str_replace( "\n", "n", $txt );
            $txt = str_replace( "//", "/", $txt );
        }
        else {
            $txt = str_replace( "\t", "&#92;t", $txt );
            $txt = str_replace( "\n", "&#92;n", $txt );
        }
        return $txt;
    }
    public function isLogin()
    {
        if(isset($_SESSION['filemanager_admin']))
        {
            // $ck_id = $_SESSION['filemanager_admin'];
            $ck_id = "1";
            $query = "SELECT is_login,ck_id FROM filemanager_db WHERE is_login='1' AND ck_id='$ck_id' LIMIT 1";
            if($select = $this->mysql_request($query))
            {
                $result = $select->fetchAll();
                if($result["ck_id"] == $ck_id and $result["is_login"] == "1")
                {
                    $this->role = "admin";
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else if(isset($_SESSION['filemanager_user']))
        {
            $ck_id = $_SESSION['filemanager_user'];
            $id = $_SESSION["filemanager_who_is_it"];
            $query = "SELECT id, is_login, ck_id FROM filemanager_users WHERE is_login='1' AND ck_id='$ck_id' AND id='$id' LIMIT 1";
            if($select = $this->mysql_request($query))
            {
                $result = $select->fetchAll();
                if($result["ck_id"] == $ck_id and $result["is_login"] == "1" and $result["id"] == $id)
                {
                    $this->role = "user";
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function login($username,$password)
    {
        $password = md5($password);
        $username = $this->encode_me($username);
        $return["status"] = false;
        $return["msg"] = "";
        $select_query = "SELECT id,is_login,email,username,password,ck_id,luck_count,luck_time FROM filemanager_db WHERE username='$username' OR email='$username'";
        if($select = $this->mysql_request($select_query))
        {
            $username = $this->decode_me($username);
            while ($result = $select->fetchAll())
            {
                if($result["luck_count"] >= 4)
                {
                    $check_luck = $this->check_luck_login($result["id"], $result["luck_time"]);
                    if($check_luck)
                    {
                        $return["msg"] = "blocked";
                        return $return;
                    }
                }
                if(($this->decode_me($result["username"]) == $username or $this->decode_me($result["email"]) == $username) and $result["password"] == $password)
                {
                    $login = true;
                    $date = date("YmdHis");
                    $ck_id = md5($result["email"].rand());
                    $_SESSION["filemanager_admin"] = $ck_id;
                    $id = $result["id"];
                    $username = $this->encode_me($username);
                    $update_query = "UPDATE filemanager_db SET is_login='1', ck_id='$ck_id', luck_count=0 WHERE username='$username' OR email='$username' AND id='$id'";
                    if($this->mysql_request($update_query))
                    {
                        $return["status"] = true;
                    }
                    else
                    {
                        $return["msg"] = "check";
                    }
                }
                else
                {
                    if(($this->decode_me($result["username"]) == $username or $this->decode_me($result["email"]) == $username) and $result["password"] != $password)
                    {
                        $check_luck = $this->luck_this_user($result["id"], $result["luck_count"]);
                        if($check_luck)
                        {
                            $return["msg"] = "blocked";
                            return $return;
                        }
                    }
                    $login = false;
                }
            }
            if(@$login != true)
            {
                $return["msg"] = "check";
            }
        }
        else
        {
            $return["msg"] = "check";
        }
        return $return;
    }
    private function check_luck_login($id, $time)
    {
        $time = date_parse($time);
        $now = date_parse(date("YmdHis"));
        if($time["year"] == $now["year"] and $time["month"] == $now["month"] and $time["day"] == $now["day"] and $time["hour"] == $now["hour"])
        {
            if($time["minute"] + 5 > $now["minute"])
            {
                return true;
            }
        }
        return false;
    }
    private function luck_this_user($id, $count)
    {
        if($count == "" or $count == null)
        {
            $count = 0;
        }
        if($count < 4)
        {
            $count++;
            $date = date("YmdHis");
            $update = $this->mysql_request("UPDATE filemanager_db SET luck_count='$count', luck_time='$date' WHERE id='$id'");
            return false;
        }
        else
        {
            return true;
        }
    }
    public function forgotPassword($email) // must be add
    {
        $result["status"] = false;
        $result["msg"] = "Forgot_Pass_Error_1";
        $check = $this->encode_me($email);
        $select = $this->mysql_request("SELECT id, firstname, lastname, email, password FROM filemanager_db WHERE email='$check'");
        $num = $select->rowCount();
        if($num > 0)
        {
            while($row = $select->fetchAll())
            {
                if($row["email"] == $check)
                {
                    $newPass = substr($row["password"], 1, 6);
                    $newPass = $newPass.rand();
                    $newPass_save = md5($newPass);
                    $id = $row["id"];
                    $update = $this->mysql_request("UPDATE filemanager_db SET password='$newPass_save' WHERE id='$id'");
                    if($update)
                    {
                        $to = $email;
                        $subject = "Forgot Password";
                        $firstname = $this->decode_me($row["firstname"]);
                        $lastname = $this->decode_me($row["lastname"]);
                        $fullname = $this->decode_me($firstname." ".$lastname);
                        $filename = basename($_SERVER["PHP_SELF"]);
                        $this_file_path = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                        $link = str_replace($filename, "", $this_file_path);
                        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https' : 'http';
                        preg_match("/^(".$protocol.":\/\/www\.)?([^\/]+)/i",
                            $_SERVER['SERVER_NAME'], $matches);
                        $host = $matches[2];
                        preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
                        $host = "noreply@".$host;
                        $link = $protocol."://".$link;
                        //$headers = "From: " . $host . "\r\n";
                        //$headers .= "MIME-Version: 1.0\r\n";
                        //$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                        $message = "Hi ".$fullname."; <br> This is your new password: ".$newPass." you can <a href=\"".$link."\">click here</a> to log in.<br> Please do not reply to this email.";
                        require_once 'filemanager_assets/PHPMailer/class.phpmailer.php';
                        $phpMailer = new PHPMailer();
                        if( defined( "IS_SMTP_USE" ) )
                        {
                            if( IS_SMTP_USE )
                            {
                                $phpMailer->SMTPAuth = SMTPAuth;
                                $phpMailer->SMTPSecure = SMTPSecure;
                                $phpMailer->Host = SMTPHost;
                                $phpMailer->Mailer = "smtp";
                                $phpMailer->Port = SMTPPort;
                                $phpMailer->Username = SMTPUsername;
                                $phpMailer->Password = SMTPPassword;
                                if( SMTPFromSMTPUsername == true ) {
                                    $host = SMTPUsername;
                                }
                            }
                        }
                        $phpMailer->CharSet = 'UTF-8';
                        $phpMailer->From = $host;
                        $phpMailer->FromName = $host;
                        $phpMailer->AddAddress($to);
                        $phpMailer->Subject = $subject;
                        $phpMailer->IsHTML(true);
                        $phpMailer->Body = $message;
                        if( $phpMailer->Send() )
                        {
                            $result["status"] = true;
                            $result["msg"] = "done";
                        }
                        else
                        {
                            $result["status"] = 'email';
                            $result["msg"] = "Forgot_Pass_Error_3";
                        }
                    }
                    else
                    {
                        $result["msg"] = "Forgot_Pass_Error_2";
                    }
                }
            }
        }
        return $result;
    }
    public function logout()
    {
        $check_id = $_SESSION["filemanager_admin"];
        $select_query = "SELECT is_login, ck_id FROM filemanager_db WHERE is_login='1' AND ck_id='$check_id'";
        if($select = $this->mysql_request($select_query))
        {
            while ($result = $select->fetchAll())
            {
                if($result["is_login"] == "1" and $result["ck_id"] == $check_id)
                {
                    $date = date("YmdHis");
                    $update_query = "UPDATE filemanager_db SET is_login='0', ck_id='' WHERE ck_id='$check_id'";
                    if($this->mysql_request($update_query))
                    {
                        $_SESSION["filemanager_admin"] = "logout";
                        unset($_SESSION["filemanager_admin"]);
                        $loggout = true;
                        return $loggout;
                    }
                    else
                    {
                        $loggout = false;
                        return $loggout;
                    }
                }
                else
                {
                    $loggout = false;
                }
            }
            if(@$loggout != true)
            {
                return $loggout;
            }
        }
        else
        {
            return false;
        }
    }
    public function adminInfo()
    {
        if(isset($_SESSION["filemanager_admin"]))
        {
            $ck_id = $_SESSION["filemanager_admin"];
            $query = $this->mysql_request("SELECT * FROM filemanager_db WHERE is_login='1' AND ck_id='$ck_id'");
            while ($row = $query->fetchAll())
            {
                if($row["ck_id"] == $ck_id)
                {
                    $this->admin_username = $this->decode_me($row["username"]);
                    $this->admin_firstname = $this->decode_me($row["firstname"]);
                    $this->admin_lastname = $this->decode_me($row["lastname"]);
                    $this->admin_email = $this->decode_me($row["email"]);
                    $this->admin_id = $row["id"];
                }
            }
        }
    }
    public function change_date_format($date)
    {
        $_date = $date;
        $new_date = date("Y-m-d H:i:s");
        $date = date_parse($date);
        $new_date = date_parse($new_date);
        $years_ago = $new_date["year"] - $date["year"];
        if($years_ago != 0)
        {
            if($years_ago == 1)
            {
                return $years_ago." year ago";
                exit();
            }
            else
            {
                return $years_ago." years ago";
                exit();
            }
        }
        if($new_date["month"] == $date["month"] and $new_date["day"] == $date["day"] and $new_date["hour"] == $date["hour"] and $new_date["minute"] <= ($date["minute"] + 1))
        {
            return "Just now";
            exit();
        }
        $min_ago = $new_date["minute"] - $date["minute"];
        if($new_date["month"] == $date["month"] and $new_date["day"] == $date["day"] and $new_date["hour"] == $date["hour"])
        {
            return $min_ago." min ago";
            exit();
        }
        $hour_ago = $new_date["hour"] - $date["hour"];
        if($new_date["month"] == $date["month"] and $new_date["day"] == $date["day"])
        {
            if($hour_ago == 1)
            {
                return $hour_ago." hr ago";
                exit();
            }
            else
            {
                return $hour_ago." hrs ago";
                exit();
            }
        }
        $day_ago = $new_date["day"] - $date["day"];
        if($new_date["month"] == $date["month"] and $day_ago <= 10)
        {
            if($day_ago == 1)
            {
                return $day_ago." day ago";
                exit();
            }
            else
            {
                return $day_ago." days ago";
                exit();
            }
        }
        $dateModified = strtotime($_date);
        $dateModified = date("M j, Y", $dateModified);
        return $dateModified;
        exit();
    }
    public function editProfile($id, $username, $firstname, $lastname, $email)
    {
        $select = $this->mysql_request("SELECT id FROM filemanager_users WHERE (username='$username' OR email='$email') AND id<>'$id'");
        $num = $select->rowCount();
        if($num > 0)
        {
            echo "null";
            exit;
        }
        if($this->isLogin())
        {
            $update = $this->mysql_request("UPDATE filemanager_db SET username='$username', firstname='$firstname', lastname='$lastname', email='$email' WHERE id='$id'");
            if ($update)
            {
                echo 'true';
            }
            else
            {
                echo 'false';
            }
        }
        else
        {
            echo 'false';
        }
    }
    public function editPassword($id, $new)
    {
        if($this->isLogin())
        {
            $new = md5($new);
            $update = $this->mysql_request("UPDATE filemanager_db SET password='$new' WHERE id='$id'");
            if ($update)
            {
                echo 'true';
            }
            else
            {
                echo 'false';
            }
        }
        else
        {
            echo "false";
        }
    }
    public function recursiveDelete($directory)
    {
        // if the path is not valid or is not a directory ...
        if(!file_exists($directory) || !is_dir($directory))
        {
            return false;
        }
        elseif(!is_readable($directory))// ... if the path is not readable
        {
            return false;
        }
        else // ... else if the path is readable
        {
            // open the directory
            $handle = opendir($directory);
            // and scan through the items inside
            while (false !== ($item = readdir($handle)))
            {
                // if the filepointer is not the current directory
                // or the parent directory
                if($item != '.' && $item != '..')
                {
                    // we build the new path to delete
                    $path = $directory.'/'.$item;
                    // if the new path is a directory
                    if(is_dir($path))
                    {
                        // we call this function with the new path
                        self::recursiveDelete($path);
                    }
                    else // if the new path is a file
                    {
                        // remove the file
                        if(!is_writable($path))
                        {
                            chmod($path, 0644);
                        }
                        @unlink($path);
                    }
                }
            }
            // close the directory
            closedir($handle);
            // try to delete the now empty directory
            if(@!rmdir($directory))
            {
                return false;
            }
            return true;
        }
    }
    public function copy_directory( $source, $destination, $check = false )
    {
        if ( is_dir( $source ) )
        {
            @mkdir( $destination );
            $directory = dir( $source );
            while ( FALSE !== ( $readdirectory = $directory->read() ) )
            {
                if ( $readdirectory == '.' || $readdirectory == '..' )
                {
                    continue;
                }
                $PathDir = $source . '/' . $readdirectory;
                if ( is_dir( $PathDir ) )
                {
                    self::copy_directory( $PathDir, $destination . '/' . $readdirectory );
                    continue;
                }
                $flag = false;
                if(!is_writable($PathDir))
                {
                    $flag = true;
                    chmod($PathDir, 0644);
                }
                copy( $PathDir, $destination . '/' . $readdirectory );
                if($flag)
                {
                    $flag = false;
                    chmod($PathDir, 0444);
                    chmod($destination . '/' . $readdirectory, 0444);
                }
            }
            $directory->close();
        }
        else
        {
            $flag = false;
            if(!is_writable($source))
            {
                $flag = true;
                chmod($source, 0644);
            }
            copy( $source, $destination );
            if($flag)
            {
                $flag = false;
                chmod($source, 0444);
                chmod($destination, 0444);
            }
        }
    }
    public function rename_directory($oldName, $newName)
    {
        if(is_dir($newName))
        {
            return false;
        }
        if(mkdir($newName))
        {
            $this->copy_directory($oldName, $newName);
            if(is_dir($newName))
            {
                $delete_old_dir = $this->recursiveDelete($oldName);
                if(is_dir($oldName))
                {
                    @chmod($oldName, 777);
                    rmdir($oldName);
                }
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function create_zip($folderName, $zipFileName)
    {
        $zip = new ZipArchive();
        if(is_dir($folderName))
        {
            $zip_archive = $zip->open($zipFileName.".zip",ZIPARCHIVE::CREATE);
            if($zip_archive === true)
            {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderName));
                foreach ($iterator as $key => $value)
                {
                    $check = substr($key, -2);
                    if( $check != ".." and $check != "/." ) {
                        $_key = str_replace("../", "", $key);
                        $_key = str_replace("./", "", $_key);
                        @$zip->addFile(realpath($key), $_key);
                    }
                }
                $zip->close();
                if(file_exists($zipFileName.".zip"))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            return false;
        }
    }
    public function extract_zip($zipFileName,$pasteLocation)
    {
        if(!is_dir($pasteLocation))
        {
            mkdir($pasteLocation);
        }
        $zip = new ZipArchive();
        if ($zip->open($zipFileName) === TRUE)
        {
            for($i = 0; $i < $zip->numFiles; $i++)
            {
                @$zip->extractTo($pasteLocation, array($zip->getNameIndex($i)));
            }
            $zip->close();
            if(is_dir($pasteLocation) or is_file($pasteLocation))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function get_allow_uploads()
    {
        $content = array();
        $select = $this->mysql_request("SELECT * FROM filemanager_options WHERE option_name='allow_uploads'");
        while($row = mysql_fetch_array($select))
        {
            if($row["option_name"] == "allow_uploads")
            {
                $content = $this->decode($row["option_content"]);
            }
        }
        return $content;
    }
    public function get_mime_type()
    {
        $content = array();
        $select = $this->mysql_request("SELECT * FROM filemanager_options WHERE option_name='allow_uploads_mime_type'");
        while($row = mysql_fetch_array($select))
        {
            if($row["option_name"] == "allow_uploads_mime_type")
            {
                $content = $this->decode($row["option_content"]);
            }
        }
        return $content;
    }
    public function check_username_email_of_user($username, $email, $user_id)
    {
        $username = $this->encode_me($username);
        $email = $this->encode_me($email);
        if($user_id == 0)
        {
            $select = $this->mysql_request("SELECT username, email, id FROM filemanager_users WHERE username='$username' OR email='$email'");
        }
        else
        {
            $select = $this->mysql_request("SELECT username, email, id FROM filemanager_users WHERE (username='$username' OR email='$email') AND id<>'$user_id'");
        }
        while($row = mysql_fetch_array($select))
        {
            if($row["username"] == $username and $row["id"] != $user_id)
            {
                echo "username";
                exit();
            }
            if($row["email"] == $email and $row["id"] != $user_id)
            {
                echo "email";
                exit();
            }
        }
        $select = $this->mysql_request("SELECT username, email FROM filemanager_db WHERE username='$username' OR email='$email'");
        while($row = mysql_fetch_array($select))
        {
            if($row["username"] == $username)
            {
                echo "username";
                exit();
            }
            if($row["email"] == $email)
            {
                echo "email";
                exit();
            }
        }
        echo "done";
        exit();
    }
    public function add_new_user($username, $email, $firstname, $lastname, $password, $send_pass, $user_dir, $limitation, $upload_limitation, $deny_files, $extra_dirs, $user_perm, $user_ext, $user_up)
    {
        $username = $this->encode_me($username);
        $email = $this->encode_me($email);
        $firstname = $this->encode_me($firstname);
        $lastname = $this->encode_me($lastname);
        $email_pass = $password;
        $password = md5($password);
        $user_dir = $this->encode_me($user_dir);
        $date = date("YmdHis");
        if($deny_files == "")
        {
            $deny_files = array();
        }
        else
        {
            $deny_files = explode(", ", $deny_files);
            foreach( $deny_files as $key => $value ) {
                $deny_files[$key] = realpath( $value );
            }
        }
        $insert = $this->mysql_request("INSERT INTO filemanager_users (firstname, lastname, username, email, password, is_login, is_block, dir_path, date_added) VALUES ('$firstname', '$lastname', '$username', '$email', '$password', 0, 0, '$user_dir', '$date')");
        if($insert)
        {
            $user_id = mysql_insert_id();
            if( $extra_dirs != "" ) {
                $extra_dirs = explode( ", ", $extra_dirs );
                $e_c = count( $extra_dirs );
                $insert_dirs = "INSERT INTO filemanager_extra_dir ( user_id, dir_path ) VALUES ";
                for( $i = 0; $i < $e_c; $i++ ) {
                    $value = $this->encode_me( $extra_dirs[$i] );
                    if( $i == $e_c -1 ) {
                        $insert_dirs .= "( '$user_id', '$value' ) ";
                    }
                    else {
                        $insert_dirs .= "( '$user_id', '$value' ), ";
                    }
                }
                if( !$this->mysql_request( $insert_dirs ) ) {
                    $this->delete_user($user_id);
                    return false;
                }
            }
            require_once 'option_class.php';
            $option = new option_class();
            $name = "allow_extensions_".$user_id;
            if($option->add_option($name, $user_ext))
            {
                $name = "allow_uploads_".$user_id;
                if($option->add_option($name, $user_up))
                {
                    $name = "permission_for_".$user_id;
                    if($option->add_option($name, $user_perm))
                    {
                        $name = "deny_folders_".$user_id;
                        if($option->add_option($name, $deny_files))
                        {
                            $name = "user_limit_".$user_id;
                            if($option->add_option($name, $limitation))
                            {
                                $name = "user_upload_limit_".$user_id;
                                if($option->add_option($name, $upload_limitation))
                                {
                                    if($send_pass == "send")
                                    {
                                        $to = $this->decode_me($email);
                                        $username = $this->decode_me($username);
                                        $subject = "Account Info";
                                        $fullname = $this->decode_me($firstname." ".$lastname);
                                        $filename = basename($_SERVER["PHP_SELF"]);
                                        $this_file_path = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                                        $link = str_replace("".$filename, "filemanager_user/", $this_file_path);
                                        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https' : 'http';
                                        preg_match("/^(".$protocol.":\/\/www\.)?([^\/]+)/i",
                                            $_SERVER['SERVER_NAME'], $matches);
                                        $host = $matches[2];
                                        preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
                                        $host = "noreply@".$host;
                                        $link = $protocol."://".$link;
                                        //$headers = "From: " . $host . "\r\n";
                                        //$headers .= "MIME-Version: 1.0\r\n";
                                        //$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                                        $message = "Hi ".$fullname."; <br> This is your File Manager <br /> username: ".$username." <br /> password: ".$email_pass." <br/> You can <a href=\"".$link."\">click here</a> to log in.<br> Please do not reply to this email.";
                                        require_once 'filemanager_assets/PHPMailer/class.phpmailer.php';
                                        $phpMailer = new PHPMailer();
                                        if( defined( "IS_SMTP_USE" ) )
                                        {
                                            if( IS_SMTP_USE )
                                            {
                                                $phpMailer->SMTPAuth = SMTPAuth;
                                                $phpMailer->SMTPSecure = SMTPSecure;
                                                $phpMailer->Host = SMTPHost;
                                                $phpMailer->Mailer = "smtp";
                                                $phpMailer->Port = SMTPPort;
                                                $phpMailer->Username = SMTPUsername;
                                                $phpMailer->Password = SMTPPassword;
                                                if( SMTPFromSMTPUsername == true ) {
                                                    $host = SMTPUsername;
                                                }
                                            }
                                        }
                                        $phpMailer->CharSet = 'UTF-8';
                                        $phpMailer->From = $host;
                                        $phpMailer->FromName = $host;
                                        $phpMailer->AddAddress($to);
                                        $phpMailer->Subject = $subject;
                                        $phpMailer->IsHTML(true);
                                        $phpMailer->Body = $message;
                                        if( !$phpMailer->Send() )
                                        {
                                            return null;
                                        }
                                        return true;
                                    }
                                    else
                                    {
                                        return true;
                                    }
                                }
                                else
                                {
                                    $this->delete_user($user_id);
                                    return false;
                                }
                            }
                            else
                            {
                                $this->delete_user($user_id);
                                return false;
                            }
                        }
                        else
                        {
                            $this->delete_user($user_id);
                            return false;
                        }
                    }
                    else
                    {
                        $this->delete_user($user_id);
                        return false;
                    }
                }
                else
                {
                    $this->delete_user($user_id);
                    return false;
                }
            }
            else
            {
                $this->delete_user($user_id);
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function delete_user($user_id)
    {
        $delete = $this->mysql_request("DELETE FROM filemanager_users WHERE id='$user_id'");
        if($delete)
        {
            require_once 'option_class.php';
            $option = new option_class();
            $name = "allow_extensions_".$user_id;
            $option->delete_option($name);
            $name = "allow_uploads_".$user_id;
            $option->delete_option($name);
            $name = "permission_for_".$user_id;
            $option->delete_option($name);
            $name = "deny_folders_".$user_id;
            $option->delete_option($name);
            $name = "user_limit_".$user_id;
            $option->delete_option($name);
            $name = "user_upload_limit_".$user_id;
            $option->delete_option($name);
            $this->delete_tickets_of_user($user_id);
            return true;
        }
        else
        {
            return false;
        }
    }
    private function delete_tickets_of_user($userId)
    {
        $select = $this->mysql_request("SELECT id FROM filemanager_tickets WHERE userId='$userId'");
        $num = mysql_num_rows($select);
        if($num > 0)
        {
            while($row = mysql_fetch_array($select))
            {
                $id = $row["id"];
                $this->mysql_request("DELETE FROM filemanager_tickets WHERE id='$id' OR parentId='$id'");
            }
        }
    }
    public function edit_user($username, $email, $firstname, $lastname, $password, $send_pass, $user_dir, $limitation, $upload_limitation, $deny_files, $extra_dirs, $user_perm, $user_ext, $user_up, $user_id)
    {
        $username = $this->encode_me($username);
        $email = $this->encode_me($email);
        $firstname = $this->encode_me($firstname);
        $lastname = $this->encode_me($lastname);
        $email_pass = "Your recent password";
        if($password != "")
        {
            $email_pass = $password;
            $password = md5($password);
        }
        $user_dir = $this->encode_me($user_dir);
        if($deny_files == "")
        {
            $deny_files = array();
        }
        else
        {
            $deny_files = explode(", ", $deny_files);
            foreach( $deny_files as $key => $value ) {
                $deny_files[$key] = realpath( $value );
            }
        }
        if($password != "")
        {
            $update = $this->mysql_request("UPDATE filemanager_users SET firstname='$firstname', lastname='$lastname', username='$username', email='$email', password='$password', dir_path='$user_dir' WHERE id='$user_id'");
        }
        else
        {
            $update = $this->mysql_request("UPDATE filemanager_users SET firstname='$firstname', lastname='$lastname', username='$username', email='$email', dir_path='$user_dir' WHERE id='$user_id'");
        }
        if($update)
        {
            if( $extra_dirs != "" ) {
                $remove_last_dirs = $this->mysql_request( "DELETE FROM filemanager_extra_dir WHERE user_id='$user_id'" );
                $extra_dirs = explode( ", ", $extra_dirs );
                $e_c = count( $extra_dirs );
                $insert_dirs = "INSERT INTO filemanager_extra_dir ( user_id, dir_path ) VALUES ";
                for( $i = 0; $i < $e_c; $i++ ) {
                    $value = $this->encode_me( $extra_dirs[$i] );
                    if( $i == $e_c -1 ) {
                        $insert_dirs .= "( '$user_id', '$value' ) ";
                    }
                    else {
                        $insert_dirs .= "( '$user_id', '$value' ), ";
                    }
                }
                if( !$this->mysql_request( $insert_dirs ) ) {
                    return false;
                }
            }
            else {
                $remove_last_dirs = $this->mysql_request( "DELETE FROM filemanager_extra_dir WHERE user_id='$user_id'" );
            }
            require_once 'option_class.php';
            $option = new option_class();
            $name = "allow_extensions_".$user_id;
            if($option->update_option($name, $user_ext))
            {
                $name = "allow_uploads_".$user_id;
                if($option->update_option($name, $user_up))
                {
                    $name = "permission_for_".$user_id;
                    if($option->update_option($name, $user_perm))
                    {
                        $name = "deny_folders_".$user_id;
                        if($option->update_option($name, $deny_files))
                        {
                            $name = "user_limit_".$user_id;
                            if($option->update_option($name, $limitation))
                            {
                                $name = "user_upload_limit_".$user_id;
                                if($option->update_option($name, $upload_limitation))
                                {
                                    if($send_pass == "send")
                                    {
                                        $to = $this->decode_me($email);
                                        $username = $this->decode_me($username);
                                        $subject = "New Account Info";
                                        $fullname = $this->decode_me($firstname." ".$lastname);
                                        preg_match("/^(http:\/\/)?([^\/]+)/i",
                                            $_SERVER['SERVER_NAME'], $matches);
                                        $host = $matches[2];
                                        preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
                                        $host = "noreply@".$host;
                                        //$headers = "From: " . $host . "\r\n";
                                        //$headers .= "MIME-Version: 1.0\r\n";
                                        //$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                                        $message = "Hi ".$fullname."; <br> This is your new username: ".$username." and password: ".$email_pass.". <br>Please do not reply.";
                                        require_once 'filemanager_assets/PHPMailer/class.phpmailer.php';
                                        $phpMailer = new PHPMailer();
                                        if( defined( "IS_SMTP_USE" ) )
                                        {
                                            if( IS_SMTP_USE )
                                            {
                                                $phpMailer->SMTPAuth = SMTPAuth;
                                                $phpMailer->SMTPSecure = SMTPSecure;
                                                $phpMailer->Host = SMTPHost;
                                                $phpMailer->Mailer = "smtp";
                                                $phpMailer->Port = SMTPPort;
                                                $phpMailer->Username = SMTPUsername;
                                                $phpMailer->Password = SMTPPassword;
                                                if( SMTPFromSMTPUsername == true ) {
                                                    $host = SMTPUsername;
                                                }
                                            }
                                        }
                                        $phpMailer->CharSet = 'UTF-8';
                                        $phpMailer->From = $host;
                                        $phpMailer->FromName = $host;
                                        $phpMailer->AddAddress($to);
                                        $phpMailer->Subject = $subject;
                                        $phpMailer->IsHTML(true);
                                        $phpMailer->Body = $message;
                                        if( !$phpMailer->Send() )
                                        {
                                            return null;
                                        }
                                        return true;
                                    }
                                    else
                                    {
                                        return true;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }
                            else
                            {
                                return false;
                            }
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function get_users()
    {
        $users = "";
        $select = $this->mysql_request("SELECT * FROM filemanager_users ORDER BY date_added DESC");
        if($select)
        {
            require_once 'option_class.php';
            $option = new option_class();
            while($row = mysql_fetch_array($select))
            {
                $users["id"][] = $row["id"];
                $users["firstname"][] = $this->decode_me($row["firstname"]);
                $users["lastname"][] = $this->decode_me($row["lastname"]);
                $users["username"][] = $this->decode_me($row["username"]);
                $users["email"][] = $this->decode_me($row["email"]);
                $users["is_block"][] = $row["is_block"];
                $users["dir_path"][] = $this->decode_me($row["dir_path"]);
                $users["date_added"][] = $row["date_added"];
                $users["permissions"][] = $this->switch_user_permission($option->get_option("permission_for_".$row["id"]));
                $users["filemanager_ext"][] = $option->get_option("allow_extensions_".$row["id"]);
                $users["uploader_ext"][] = $option->get_option("allow_uploads_".$row["id"]);
                $users["deny_folders"][] = $option->get_option("deny_folders_".$row["id"]);
                $limitation = $option->get_option("user_limit_".$row["id"]);
                $limitation = ($limitation * 1024) * 1024;
                $users["limitation"][] = $this->set_limitation($this->decode_me($row["dir_path"]), $limitation);
                $users["upload_limitation"][] = $option->get_option("user_upload_limit_".$row["id"]);
            }
        }
        return $users;
    }
    private function set_limitation($directory, $limitation)
    {
        if(is_dir($directory))
        {
            $size = 0;
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file)
            {
                $size += $file->getSize();
            }
            $limit = ($size / $limitation) * 100;
        }
        else
        {
            $limit = 0;
        }
        return $limit;
    }
    private function switch_user_permission($array)
    {
        foreach($array as $key => $value)
        {
            switch($value)
            {
                case "edit_profile":
                    $array[$key] = "Edit Profile";
                    break;
                case "create_folder":
                    $array[$key] = "Create Folder";
                    break;
                case "rename_folder":
                    $array[$key] = "Rename Files And Folders";
                    break;
                case "copy_folders":
                    $array[$key] = "Copy Files And Folders";
                    break;
                case "move_folders":
                    $array[$key] = "Move Files And Folders";
                    break;
                case "remove_folders":
                    $array[$key] = "Remove Folders";
                    break;
                case "zip_folders":
                    $array[$key] = "Zip Files And Folders";
                    break;
                case "upload_folders":
                    $array[$key] = "Upload Files";
                    break;
                case "backup_folders":
                    $array[$key] = "Create Backup";
                    break;
                case "edit_files":
                    $array[$key] = "Edit Text Files";
                    break;
                case "edit_img":
                    $array[$key] = "Edit Images";
                    break;
                case "unzip_files":
                    $array[$key] = "Extract Zip Files";
                    break;
            }
        }
        return $array;
    }
    public function get_user($id)
    {
        $users = "";
        $select = $this->mysql_request("SELECT * FROM filemanager_users WHERE id='$id'");
        if($select)
        {
            require_once 'option_class.php';
            $option = new option_class();
            $row = mysql_fetch_array( $select, MYSQL_ASSOC );
            $users["id"] = $row["id"];
            $users["firstname"] = $this->decode_me($row["firstname"]);
            $users["lastname"] = $this->decode_me($row["lastname"]);
            $users["username"] = $this->decode_me($row["username"]);
            $users["email"] = $this->decode_me($row["email"]);
            $users["dir_path"] = $this->decode_me($row["dir_path"]);
            $users["permissions"] = $option->get_option("permission_for_".$row["id"]);
            $users["filemanager_ext"] = $option->get_option("allow_extensions_".$row["id"]);
            $users["uploader_ext"] = $option->get_option("allow_uploads_".$row["id"]);
            $users["deny_folders"] = $option->get_option("deny_folders_".$row["id"]);
            $users["limitation"] = $option->get_option("user_limit_".$row["id"]);
            $users["upload_limitation"] = $option->get_option("user_upload_limit_".$row["id"]);
            $users["extra_dirs"] = $this->get_user_extra_dirs( $users["id"] );
        }
        return $users;
    }
    public function get_user_extra_dirs( $user_id )
    {
        $select = $this->mysql_request( "SELECT dir_path FROM filemanager_extra_dir WHERE user_id='$user_id'" );
        $dirs = array();
        if( $select ) {
            while( $row = mysql_fetch_array( $select ) ) {
                $dirs[] = $this->decode_me( $row["dir_path"] );
            }
        }
        return $dirs;
    }
    public function block_user($user_id, $method)
    {
        if($method == 0)
        {
            $update = $this->mysql_request("UPDATE filemanager_users SET is_block=1 WHERE id='$user_id'");
            if($update)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $update = $this->mysql_request("UPDATE filemanager_users SET is_block=0 WHERE id='$user_id'");
            if($update)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    public function page($page, $fullCount, $count_show)
    {
        if($count_show == "all")
        {
            $this->start = 0;
            $this->end = $fullCount;
            $this->pageCount = 1;
            return null;
        }
        $this->pageCount = self::_numpage($fullCount, $count_show);
        if($page > $this->pageCount)
        {
            $page = $this->pageCount;
        }
        if($page == 0 or $page < 0)
        {
            $page = 1;
        }
        if($page == 1 and $fullCount > $count_show)
        {
            $this->start = 0;
            $this->end = $count_show;
        }
        elseif($page == 1 and $fullCount < $count_show)
        {
            $this->start = 0;
            $this->end = $fullCount;
        }
        else
        {
            $count = $page * $count_show;
            $this->start = $count - $count_show;
            if ($fullCount < $count)
            {
                $this->end = $fullCount;
            }
            else
            {
                $this->end = $count;
            }
        }
    }
    private function _numpage($co_tot,$co)
    {
        if ($co_tot == 0)
        {
            $page = 1;
            return $page;
        }
        if($co > $co_tot)
        {
            $co_tot = $co;
            $page = $co_tot / $co;
            return $page;
        }
        else
        {
            $page = $co_tot / $co;
            if ($page > 0 and $page < 1)
            {
                $page = 2;
                return $page;
            }
            else if ($page > 1 and $page < 2)
            {
                $page = 2;
                return $page;
            }
            return ceil($page);
        }
    }
    public function get_base_root()
    {
        return realpath(ROOT_DIR_PATH);
    }
    public function check_base_root($newName)
    {
        if($newName != ROOT_DIR_PATH)
        {
            $check_address = explode("/", $newName);
            $count = count($check_address);
            for($i = 0; $i < $count; $i++)
            {
                if($i == $count - 1)
                {
                    unset($check_address[$i]);
                    break;
                }
            }
            $check_address = realpath(implode("/", $check_address));
        }
        else
        {
            $check_address = realpath($newName);
        }
        $check_root = $this->get_base_root();
        if(strpos($check_address, $check_root) === FALSE)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    public function share_files($send_to, $emails, $subject, $from, $message, $file)
    {
        require_once 'filemanager_assets/PHPMailer/class.phpmailer.php';
        $phpMailer = new PHPMailer();
        if( defined( "IS_SMTP_USE" ) )
        {
            if( IS_SMTP_USE )
            {
                $phpMailer->SMTPAuth = SMTPAuth;
                $phpMailer->SMTPSecure = SMTPSecure;
                $phpMailer->Host = SMTPHost;
                $phpMailer->Mailer = "smtp";
                $phpMailer->Port = SMTPPort;
                $phpMailer->Username = SMTPUsername;
                $phpMailer->Password = SMTPPassword;
                if( SMTPFromSMTPUsername == true ) {
                    $from = SMTPUsername;
                }
            }
        }
        $phpMailer->CharSet = 'UTF-8';
        $phpMailer->From = $from;
        $phpMailer->FromName = $from;
        $phpMailer->AddAddress($send_to);
        $phpMailer->Subject = $subject;
        $phpMailer->IsHTML(true);
        $phpMailer->Body = $message;
        $phpMailer->AddAttachment($file);
        if($phpMailer->Send())
        {
            if($emails != "")
            {
                $phpMailer->ClearAddresses();
                $emails = explode(", ", $emails);
                foreach($emails as $to)
                {
                    if($to != "")
                    {
                        $phpMailer->CharSet = 'UTF-8';
                        $phpMailer->AddAddress($to);
                        $phpMailer->Subject = $subject;
                        $phpMailer->IsHTML(true);
                        $phpMailer->Body = $message;
                        $phpMailer->AddAttachment($file);
                        $phpMailer->Send();
                        $phpMailer->ClearAddresses();
                    }
                }
            }
            echo "true";
        }
        else
        {
            echo "false";
        }
    }
    public function gravatar_src( $email, $size = 82 )
    {
        $email = trim($email);
        $email = strtolower($email);
        $email_hash = md5($email);
        $custom_avatar = "";
        if( defined( "ADMIN_DEFAULT_AVATAR" ) ) {
            if( ADMIN_DEFAULT_AVATAR != "" ) {
                $custom_avatar = "&d=".urlencode( ADMIN_DEFAULT_AVATAR );
            }
        }
        return "http://www.gravatar.com/avatar/".$email_hash."?s=".$size.$custom_avatar;
    }
    public function create_breadcrumb( $path )
    {
        $path = realpath( $path );
        $home = realpath( ROOT_DIR_PATH );
        $breadcrumb = '<ol class="breadcrumb">';
        if( $path == $home ) {
            $breadcrumb .= '<li class="active">'.language_filter("Home", true).'</li>';
        }
        else {
            $slash = $this->get_server_os();
            $new_path = str_replace( $home, "", $path );
            $new_path = explode( $slash, $new_path );
            $count = count( $new_path );
            $breadcrumb .= '<li><a href="javascript:;" onclick="loading_from_file = false; showFileManager(\'\')">'.language_filter("Home", true).'</a></li>';
            $back_link = '';
            for( $i = 0; $i < $count; $i++ ) {
                $value = $new_path[$i];
                if( $value != "" ) {
                    $back_link .= "/".$value;
                    if( $i == $count - 1 ) {
                        $breadcrumb .= '<li class="active">'.$value.'</li>';
                    }
                    else {
                        $breadcrumb .= '<li><a href="javascript:;" onclick="loading_from_file = false; showFileManager(\''.$back_link.'\')">'.$value.'</a></li>';
                    }
                }
            }
        }
        $breadcrumb .= '</ol>';
        echo $breadcrumb;
    }
    private function get_server_os()
    {
        if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ) {
            return '\\';
        }
        else {
            return '/';
        }
    }
    public function core_get_support_ext()
    {
        $content = array();
        $select = $this->mysql_request("SELECT * FROM filemanager_options WHERE option_name='allow_extensions'");
        while($row = mysql_fetch_array( $select ) )
        {
            if($row["option_name"] == "allow_extensions")
            {
                $content = $this->decode($row["option_content"]);
            }
        }
        return $content;
    }
    /* SHARE SYSTEM */
    public function share_system_files( $path, $files, $desc, $user_id )
    {
        $check_path = ROOT_DIR_PATH.$path;
        $date = date( "YmdHis" );
        $desc = $this->encode_me( $desc );
        $insert = "INSERT INTO filemanager_shares (admin, file_path, description, role, date_added) VALUES ";
        $count = count( $files );
        for( $i = 0; $i < $count; $i++ ) {
            $filename = basename( $files[$i] );
            if( is_file( $check_path.$filename ) ) {
                $file_path = $this->encode_me( ROOT_DIR_PATH.$path.$filename );
                if( $i == $count - 1 ) {
                    $insert .= " ( '$user_id', '$file_path', '$desc', 'admin', '$date' ) ";
                }
                else {
                    $insert .= " ( '$user_id', '$file_path', '$desc', 'admin', '$date' ), ";
                }
            }
        }
        if( $this->mysql_request( $insert ) ) {
            return true;
        }
        return false;
    }
    public function get_shared_files( $page, $user = "all", $role = 'admin' )
    {
        function pages( $co_tot, $co )
        {
            if ($co_tot == 0)
            {
                $page = 1;
                return $page;
            }
            if($co > $co_tot)
            {
                $co_tot = $co;
                $page = $co_tot / $co;
                return $page;
            }
            else
            {
                $page = $co_tot / $co;
                if ($page > 0 and $page < 1)
                {
                    $page = 2;
                    return $page;
                }
                else if ($page > 1 and $page < 2)
                {
                    $page = 2;
                    return $page;
                }
                return ceil($page);
            }
        }
        $per_page = 10;
        $page = (int) mysql_real_escape_string( $page );
        $start = ( $page - 1 ) * $per_page;
        $end = $per_page;
        if( $user != "all" ) {
            $user = (int) mysql_real_escape_string( $user );
            if( $role == 'user' ) {
                $select = $this->mysql_request( "SELECT *, (SELECT COUNT(*) FROM filemanager_shares WHERE user_id='$user') AS total FROM filemanager_shares WHERE user_id='$user' ORDER BY date_added DESC LIMIT {$start}, {$end}" );
            }
            else {
                $select = $this->mysql_request( "SELECT *, (SELECT COUNT(*) FROM filemanager_shares WHERE admin='$user') AS total FROM filemanager_shares WHERE admin='$user' ORDER BY date_added DESC LIMIT {$start}, {$end}" );
            }
        }
        else {
            $select = $this->mysql_request( "SELECT *, (SELECT COUNT(*) FROM filemanager_shares) AS total FROM filemanager_shares ORDER BY date_added DESC LIMIT {$start}, {$end}" );
        }
        $result = "";
        if( $select ) {
            $total = 0;
            while( $row = mysql_fetch_array( $select ) ) {
                $result["id"][] = $row["id"];
                $result["role"][] = $row["role"];
                if( $row["role"] == "user" ) {
                    $user_id = $row["user_id"];
                    $result["user_id"][] = $user_id;
                    $this->get_share_user_info( $user_id );
                    $result["fullname"][] = $this->share_users[$user_id]["fullname"];
                    $result["gravatar"][] = $this->share_users[$user_id]["gravatar"];
                    $result["username"][] = $this->share_users[$user_id]["username"];
                    $result["email"][] = $this->share_users[$user_id]["email"];
                }
                else {
                    $result["user_id"][] = $row["admin"];
                    $result["fullname"][] = $this->admin_firstname." ".$this->admin_lastname;
                    $result["gravatar"][] = $this->gravatar_src( $this->admin_email );
                    $result["username"][] = $this->admin_username;
                    $result["email"][] = $this->admin_email;
                }
                $file_path = $this->decode_me( $row["file_path"] );
                $result["file_path"][] = $file_path;
                $temp = explode( "/", $file_path );
                $result["file_name"][] = end( $temp );
                $result["description"][] = stripslashes( $this->decode_me( $row["description"], true ) );
                $result["date_added"][] = $this->decode_me( $row["date_added"], true );
                $total = $row["total"];
            }
            $result["pages"] = pages( $total, $per_page );
        }
        return $result;
    }
    private function get_share_user_info( $id ) {
        if( !isset( $this->share_users[$id]["fullname"] ) ) {
            $select = $this->mysql_request( "SELECT firstname, lastname, username, email FROM filemanager_users WHERE id='$id'" );
            $row = mysql_fetch_array( $select, MYSQL_ASSOC );
            $this->share_users[$id]["fullname"] = $this->decode_me( $row["firstname"]." ".$row["lastname"] );
            $this->share_users[$id]["username"] = $this->decode_me( $row["username"] );
            $this->share_users[$id]["email"] = $this->decode_me( $row["email"] );
            $this->share_users[$id]["gravatar"] = $this->gravatar_src( $this->share_users[$id]["email"] );
        }
    }
    public function remove_share_file( $id )
    {
        $id = (int) mysql_real_escape_string( $id );
        $delete = $this->mysql_request( "DELETE FROM filemanager_shares WHERE id='$id'" );
        if( $delete ) {
            return true;
        }
        return false;
    }
    public function download_share_file( $id )
    {
        $id = (int) mysql_real_escape_string( $id );
        $select = $this->mysql_request( "SELECT file_path FROM filemanager_shares WHERE id='$id'" );
        if( $select ) {
            $row = mysql_fetch_array( $select, MYSQL_ASSOC );
            $file = $this->decode_me( $row["file_path"] );
            if( is_file( $file ) ) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                @ob_clean();
                @flush();
                readfile($file);
                exit;
            }
            else {
                $file = ROOT_DIR_PATH."filemanager_assets/error.txt";
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($file));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                @ob_clean();
                @flush();
                readfile($file);
                exit;
            }
        }
    }
    /* USER REGISTER */
    private function register_user_dir($dir, $username)
    {
        $filter = array("'", "\"", "\\", "/", " ", "~", "!", "@", "#", "\$", "%", "^", "&", "*", "(", ")", "-", "+", "=", "|", "{", "}", "[", "]", ",", ".", "?", "<", ">", "`");
        foreach($filter as $value)
        {
            $username = str_replace($value, "_", $username);
        }
        $new_dir = $dir.$username."/";
        if(!is_dir($new_dir))
        {
            return $new_dir;
        }
        else
        {
            $username .= rand();
            return $this->register_user_dir($dir, $username);
        }
    }
    public function register_this_user($username, $email, $firstname, $lastname)
    {
        require_once 'option_class.php';
        $option = new option_class();
        $username = $this->encode_me($username);
        $email = $this->encode_me($email);
        $firstname = $this->encode_me($firstname);
        $lastname = $this->encode_me($lastname);
        $password = rand()."*".substr(md5($username), 1, 6);
        $email_pass = $password;
        $password = md5($password);
        $register_settings = $option->get_option("register_settings");
        $slash = "";
        if(substr($register_settings->users_dir, -1) != "/")
        {
            $slash = "/";
        }
        $user_dir = $register_settings->users_dir.$slash;
        $user_dir = $this->register_user_dir($user_dir, $username);
        $user_ext = (array) $register_settings->allow_ext;
        $user_up = (array) $register_settings->allow_upload;
        $user_perm = (array) $register_settings->permissions;
        $limitation = $register_settings->size_limitation;
        $upload_limitation = $register_settings->upload_limitation;
        $user_dir = $this->encode_me($user_dir);
        $date = date("YmdHis");
        $deny_files = array();
        $activation_code = md5($username.$email.rand());
        $insert = $this->mysql_request("INSERT INTO filemanager_users (firstname, lastname, username, email, password, is_login, activation_key, is_block, dir_path, date_added) VALUES ('$firstname', '$lastname', '$username', '$email', '$password', 0, '$activation_code', 1, '$user_dir', '$date')");
        if($insert)
        {
            $user_id = mysql_insert_id();
            $name = "allow_extensions_".$user_id;
            if($option->add_option($name, $user_ext))
            {
                $name = "allow_uploads_".$user_id;
                if($option->add_option($name, $user_up))
                {
                    $name = "permission_for_".$user_id;
                    if($option->add_option($name, $user_perm))
                    {
                        $name = "deny_folders_".$user_id;
                        if($option->add_option($name, $deny_files))
                        {
                            $name = "user_limit_".$user_id;
                            if($option->add_option($name, $limitation))
                            {
                                $name = "user_upload_limit_".$user_id;
                                if($option->add_option($name, $upload_limitation))
                                {
                                    $to = $this->decode_me($email);
                                    $username = $this->decode_me($username);
                                    $subject = "Filemanager Registration";
                                    $fullname = $this->decode_me($firstname." ".$lastname);
                                    $filename = basename($_SERVER["PHP_SELF"]);
                                    $this_file_path = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                                    $link = str_replace("filemanager_user/".$filename, "", $this_file_path);
                                    $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https' : 'http';
                                    preg_match("/^(".$protocol.":\/\/www\.)?([^\/]+)/i",
                                        $_SERVER['SERVER_NAME'], $matches);
                                    $host = $matches[2];
                                    preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
                                    $host = "noreply@".$host;
                                    $link = $protocol."://".$link."?activation_code=".$activation_code."&info=".md5($user_id);
                                    $headers = "From: " . $host . "\r\n";
                                    $headers .= "MIME-Version: 1.0\r\n";
                                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                                    $message = "Hi ".$fullname."; <br> This is your File Manager <br /> username: ".$username." <br /> password: ".$email_pass." <br/> You can <a href=\"".$link."\">click here ( ".$link." )</a> to activate your account.<br> Please do not reply to this email.";
                                    require_once 'filemanager_assets/PHPMailer/class.phpmailer.php';
                                    $phpMailer = new PHPMailer();
                                    if( defined( "IS_SMTP_USE" ) )
                                    {
                                        if( IS_SMTP_USE )
                                        {
                                            $phpMailer->SMTPAuth = SMTPAuth;
                                            $phpMailer->SMTPSecure = SMTPSecure;
                                            $phpMailer->Host = SMTPHost;
                                            $phpMailer->Mailer = "smtp";
                                            $phpMailer->Port = SMTPPort;
                                            $phpMailer->Username = SMTPUsername;
                                            $phpMailer->Password = SMTPPassword;
                                            if( SMTPFromSMTPUsername == true ) {
                                                $host = SMTPUsername;
                                            }
                                        }
                                    }
                                    $phpMailer->CharSet = 'UTF-8';
                                    $phpMailer->From = $host;
                                    $phpMailer->FromName = $host;
                                    $phpMailer->AddAddress($to);
                                    $phpMailer->Subject = $subject;
                                    $phpMailer->IsHTML(true);
                                    $phpMailer->Body = $message;
                                    if( $phpMailer->Send() )
                                    {
                                        return true;
                                    }
                                    else
                                    {
                                        $this->delete_user($user_id);
                                        return false;
                                    }
                                }
                                else
                                {
                                    $this->delete_user($user_id);
                                    return false;
                                }
                            }
                            else
                            {
                                $this->delete_user($user_id);
                                return false;
                            }
                        }
                        else
                        {
                            $this->delete_user($user_id);
                            return false;
                        }
                    }
                    else
                    {
                        $this->delete_user($user_id);
                        return false;
                    }
                }
                else
                {
                    $this->delete_user($user_id);
                    return false;
                }
            }
            else
            {
                $this->delete_user($user_id);
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function activate_user($key, $id)
    {
        $key = $this->encode_me($key);
        $id = $this->encode_me($id);
        $select = $this->mysql_request("SELECT id, dir_path, username, activation_key FROM filemanager_users WHERE MD5(id)='$id' AND activation_key='$key'");
        $num = mysql_num_rows($select);
        if($num <= 0)
        {
            return null;
        }
        while($row = mysql_fetch_array($select))
        {
            if($row["activation_key"] == $key and md5($row["id"]) == $id)
            {
                $user_id = $row["id"];
                $user_dir = $this->decode_me($row["dir_path"]);
                $username = $this->decode_me($row["username"]);
                $mkdir = false;
                if(!is_dir($user_dir))
                {
                    if(@mkdir($user_dir))
                    {
                        $mkdir = true;
                    }
                }
                else
                {
                    $user_dir = explode("/", $user_dir);
                    $count = count($user_dir);
                    for($i = 0; $i < $count; $i++)
                    {
                        if($i == $count - 2)
                        {
                            unset($user_dir[$i]);
                            break;
                        }
                    }
                    $user_dir = implode("/", $user_dir)."/";
                    $user_dir = $this->register_user_dir($user_dir, $username);
                    if(@mkdir($user_dir))
                    {
                        $mkdir = true;
                    }
                }
                if($mkdir)
                {
                    $update = "UPDATE filemanager_users SET activation_key='', is_block=0 WHERE id='$user_id'";
                    if($this->mysql_request($update))
                    {
                        return true;
                    }
                    else
                    {
                        @rmdir($user_dir);
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
        }
        return null;
    }
}
class filemanager extends Services_JSON
{
    private $root;
    public $support_ext;
    private $sort;
    private $search;
    public $ignored;
    public $show_files_folders;
    function __construct( $path = "", $sort = "", $search = "" )
    {
        if( $path == "" and $sort == "" and $search == "" ) {
            $this->support_ext = $this->get_support_ext();
            $this->set_ignored();
            $this->set_root_dir();
        }
        else {
            $this->set_root_dir();
            $this->search = $search;
            if( $this->check_path( $path ) ) {
                $path = realpath( $path );
                $this->support_ext = $this->get_support_ext();
                $this->set_ignored();
                $this->sort = $sort;
                if($this->search != '') {
                    $this->findFiles( $path, $this->support_ext );
                }
                else {
                    foreach ( scandir( $path ) as $file ) {
                        if ( in_array( $file, $this->ignored ) ) continue;
                        if( is_file( $path . '/' . $file ) ) {
                            $ext = pathinfo($path . '/' . $file, PATHINFO_EXTENSION);
                            $ext = strtolower($ext);
                            if( !in_array( $ext, $this->support_ext ) ) continue;
                        }
                        $realpath = realpath( $path . '/' . $file );
                        $this->show_files_folders[ $realpath ] = filemtime( $realpath );
                    }
                    @arsort( $this->show_files_folders );
                    if($this->sort != 'date') {
                        @$this->show_files_folders = $this->sort_with_name( $this->show_files_folders );
                    }
                    @$this->show_files_folders = array_keys( $this->show_files_folders );
                }
            }
        }
    }
    private function find_all_files($dir, $extensions, $search)
    {
        $root = scandir($dir);
        foreach($root as $value)
        {
            if($value === '.' or $value === '..') continue;
            if( in_array( $value, $this->ignored ) ) continue;
            if(is_file($dir."/".$value))
            {
                $ext = strtolower(end(explode(".", $value)));
                if(in_array($ext, $extensions))
                {
                    $s_value = strtolower($value); //str_replace($ext, "", $value)
                    $s_search = strtolower($search);
                    if(strpos($s_value, $s_search) !== FALSE)
                    {
                        $file = $dir."/".$value;
                        $filename = $file;//str_replace("..//", "", $file);
                        $this->show_files_folders[$filename] = filemtime($file);
                    }
                }
            }
            else
            {
                $s_value = strtolower($value); //str_replace($ext, "", $value)
                $s_search = strtolower($search);
                if(strpos($s_value, $s_search) !== FALSE)
                {
                    $file = $dir."/".$value;
                    $filename = $file;//str_replace(ROOT_DIR_NAME."/", "", $file);
                    $this->show_files_folders[$filename] = filemtime($file);
                }
            }
        }
    }
    private function findFiles($directory, $extensions = array())
    {
        if($this->check_path( $directory ) )
        {
            $search = $this->filter_search_str($this->search);
            $directories = "";
            function glob_recursive($directory, &$directories = array(), $search)
            {
                foreach(glob($directory, GLOB_ONLYDIR | GLOB_NOSORT) as $folder)
                {
                    $directories[] = $folder;
                    glob_recursive("{$folder}/*", $directories);
                }
            }
            @glob_recursive($directory, $directories, $search);
            $files = array ();
            foreach($directories as $directory)
            {
                $slashes = realpath( ROOT_DIR_PATH );
                if(strpos($directory, $slashes."/") !== FALSE)
                {
                    $slashes = $slashes."/";
                }
                if (!in_array(str_replace($slashes, "", $directory), $this->ignored)) {
                    $this->find_all_files($directory, $extensions, $search);
                }
            }
            @arsort($this->show_files_folders);
            if($this->sort != 'date')
            {
                @$this->show_files_folders = $this->sort_with_name($this->show_files_folders);
            }
        }
        @$this->show_files_folders = array_keys($this->show_files_folders);
    }
    private function filter_search_str($txt)
    {
        $txt = str_replace("../", "", $txt);
        $txt = str_replace("/", "", $txt);
        $txt = str_replace(".", "", $txt);
        return $txt;
    }
    public function sort_with_name( $array )
    {
        $new_arr = "";
        foreach($array as $key => $value) {
            $first_char = strtolower( substr( end( explode( $this->get_server_os(), $key ) ), 0, 1 ) );
            $new_arr[$key] = $first_char;
        }
        asort( $new_arr );
        foreach( $new_arr as $key => $value ) {
            $new_arr[$key] = $array[$key];
        }
        return $new_arr;
    }
    public function curPageURL()
    {
        $pageURL = 'http';
        if(isset($_SERVER["HTTPS"]))
        {
            if ($_SERVER["HTTPS"] == "on")
            {
                $pageURL .= "s";
            }
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80")
        {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else
        {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
    public function formatBytes($path)
    {
        if( is_dir( $path ) )
        {
            $bytes = $this->dirSize($path);
        }
        else
        {
            $bytes = sprintf('%u', filesize($path));
        }
        if ($bytes > 0)
        {
            $unit = intval(log($bytes, 1024));
            $units = array('B', 'KB', 'MB', 'GB');
            if (array_key_exists($unit, $units) === true)
            {
                return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
            }
        }
        return $bytes;
    }
    public function dirSize($directory)
    {
        $size = 0;
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
            $size += $file->getSize();
        }
        return $size;
    }
    protected function set_root_dir()
    {
        $this->root = realpath( ROOT_DIR_PATH );
    }
    protected function check_path( $path )
    {
        $path = realpath( $path );
        if( strpos( $path, $this->root ) === FALSE ) {
            return false;
        }
        else {
            return true;
        }
    }
    protected function set_ignored()
    {
        $ignored = array(
            '.',
            '..',
            'filemanager_assets',
            'filemanager_assets/lng',
            'filemanager_assets/PHPMailer',
            'filemanager_assets/PHPMailer/docs',
            'filemanager_assets/PHPMailer/extras',
            'filemanager_assets/PHPMailer/language',
            'filemanager_assets/securimage',
            'filemanager_assets/securimage/backgrounds',
            'filemanager_assets/securimage/images',
            'filemanager_assets/securimage/words',
            'filemanager_assets/uploader',
            'filemanager_assets/uploader/src',
            'filemanager_assets/vakata-jstree',
            'filemanager_assets/vakata-jstree/dist',
            'filemanager_assets/vakata-jstree/dist/libs',
            'filemanager_assets/vakata-jstree/dist/themes/default',
            'filemanager_assets/vakata-jstree/src',
            'filemanager_assets/vakata-jstree/src/themes',
            'filemanager_assets/vakata-jstree/src/themes/default',
            'filemanager_assets/vakata-jstree/dist/themes',
            'filemanager_assets/vakata-jstree/libs',
            'filemanager_backups',
            'filemanager_css',
            'filemanager_fonts',
            'filemanager_img',
            'filemanager_img/fancy',
            'filemanager_img/pattern',
            'filemanager_install',
            'filemanager_js',
            'filemanager_temp',
            'filemanager_user',
            'filemanager_user/filemanager_error_log.txt',
            'filemanager_user/ajax_check_username.php',
            'filemanager_user/ajax_manage_dir.php',
            'filemanager_user/ajax_manage_tickets.php',
            'filemanager_user/ajax_manager_share.php',
            'filemanager_user/ajax_show_filemanager.php',
            'filemanager_user/ajax_show_profile.php',
            'filemanager_user/ajax_show_shared.php',
            'filemanager_user/ajax_ticket_show.php',
            'filemanager_user/ajax_tickets_show.php',
            'filemanager_user/ajax_update_profile.php',
            'filemanager_user/content.php',
            'filemanager_user/download.php',
            'filemanager_user/edit_file.php',
            'filemanager_user/filemanager_pdfShow.php',
            'filemanager_user/filemanager_siteMap.php',
            'filemanager_user/filemanager_uploader.php',
            'filemanager_user/footer.php',
            'filemanager_user/header.php',
            'filemanager_user/img.php',
            'filemanager_user/index.php',
            'filemanager_user/jqueryFileTree.php',
            'filemanager_user/menu.php',
            'filemanager_user/modals.php',
            'filemanager_user/navigate.php',
            'filemanager_user/option_class.php',
            'filemanager_user/upload.php',
            'filemanager_error_log.txt',
            'ajax_add_user.php',
            'ajax_check_user.php',
            'ajax_edit_user.php',
            'ajax_manage_dir.php',
            'ajax_manage_tickets.php',
            'ajax_manager_share.php',
            'ajax_remove_file.php',
            'ajax_show_filemanager.php',
            'ajax_show_home.php',
            'ajax_show_profile.php',
            'ajax_show_setting.php',
            'ajax_show_shared.php',
            'ajax_show_users.php',
            'ajax_ticket_show.php',
            'ajax_tickets_show.php',
            'ajax_update_profile.php',
            'content.php',
            'download.php',
            'edit_file.php',
            'filemanager_config.php',
            'filemanager_core.php',
            'filemanager_language.php',
            'filemanager_language_user.php',
            'filemanager_pdfShow.php',
            'filemanager_siteMap.php',
            'filemanager_uploader.php',
            'filemanager_user_core.php',
            'footer.php',
            'header.php',
            'img.php',
            'index.php',
            'jqueryFileTree.php',
            'login.php',
            'logout.php',
            'menu.php',
            'modals.php',
            'navigate.php',
            'option_class.php',
            'upload.php',
        );
        $this->ignored = $ignored;
    }
    public function get_server_os()
    {
        if ( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ) {
            return '\\';
        }
        else {
            return '/';
        }
    }
    public function get_support_ext()
    {
        mysql_connect( DB_HOST, DB_USER, DB_PASS );
        mysql_select_db( DB_NAME );
        $content = array();
        $select = $this->mysql_request("SELECT * FROM filemanager_options WHERE option_name='allow_extensions'");
        while($row = mysql_fetch_array( $select ) )
        {
            if($row["option_name"] == "allow_extensions")
            {
                $content = $this->decode($row["option_content"]);
            }
        }
        return $content;
    }
}
class filemanager_backups extends filemanager_core
{
    private $backup_dir = "filemanager_backups";
    public $backup_dir_files = NULL;
    function __construct()
    {
        $ignored = array('.', '..', 'backups.php', '.htaccess');
        foreach (scandir($this->backup_dir) as $file)
        {
            if (in_array($file, $ignored)) continue;
            $ext = pathinfo($this->backup_dir . '/' . $file, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            if( $ext == "zip" ) {
                $this->backup_dir_files[$file] = filemtime($this->backup_dir . '/' . $file);
            }
        }
        @arsort($this->backup_dir_files);
        @$this->backup_dir_files = array_keys($this->backup_dir_files);
    }
    public function formatBytes($path)
    {
        if(is_dir($path))
        {
            $bytes = $this->dirSize($path);
        }
        else
        {
            $bytes = sprintf('%u', filesize($path));
        }
        if ($bytes > 0)
        {
            $unit = intval(log($bytes, 1024));
            $units = array('B', 'KB', 'MB', 'GB');
            if (array_key_exists($unit, $units) === true)
            {
                return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
            }
        }
        else
            return $bytes;
    }
    function dirSize($directory)
    {
        $size = 0;
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
            $size+=$file->getSize();
        }
        return $size;
    }
    public function remove_this_backup_file($name)
    {
        $filename = $this->backup_dir."/".$name;
        if (is_file($filename))
        {
            if(@unlink($filename))
            {
                echo "T";
            }
            else
            {
                echo "F1";
            }
        }
        else
        {
            echo "F2";
        }
    }
}
class fs extends filemanager
{
    protected $base = null;
    protected function real($path) {
        $temp = false;
        if( $this->check_path( $path ) ) {
            $temp = realpath($path);
            if(!$temp) { throw new Exception('Path does not exist: ' . $path); }
            if($this->base && strlen($this->base)) {
                if(strpos($temp, $this->base) !== 0) { throw new Exception('Path is not inside base ('.$this->base.'): ' . $temp); }
            }
            return $temp;
        }
        return $temp;
    }
    protected function path($id) {
        $id = str_replace('/', DIRECTORY_SEPARATOR, $id);
        $id = trim($id, DIRECTORY_SEPARATOR);
        $id = $this->real($this->base . DIRECTORY_SEPARATOR . $id);
        return $id;
    }
    protected function id($path) {
        $path = $this->real($path);
        $path = substr($path, strlen($this->base));
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $path = trim($path, '/');
        return strlen($path) ? $path : '/';
    }
    public function __construct($base) {
        parent::__construct();
        $this->base = $this->real($base);
        if(!$this->base) { throw new Exception('Base directory does not exist'); }
    }
    public function lst($id, $with_root = false) {
        $dir = $this->path($id);
        $lst = @scandir($dir);
        if(!$lst) { throw new Exception('Could not list path: ' . $dir); }
        $res = array();
        foreach($lst as $item) {
            if($item == '.' || $item == '..' || $item === null || in_array( $item, $this->ignored ) ) { continue; }
            $tmp = preg_match('([^ a-zа-я-_0-9.]+)ui', $item);
            if($tmp === false || $tmp === 1) { continue; }
            if(is_dir($dir . DIRECTORY_SEPARATOR . $item)) {
                if( in_array( $item, $this->ignored ) ) continue;
                $res[] = array('text' => $item, 'children' => true,  'id' => $this->id($dir . DIRECTORY_SEPARATOR . $item), 'icon' => 'folder');
            }
            else {
                $ext = substr($item, strrpos($item,'.') + 1);
                if( !in_array( strtolower( $ext ), $this->support_ext ) ) { continue; }
                $res[] = array('text' => $item, 'children' => false, 'id' => $this->id($dir . DIRECTORY_SEPARATOR . $item), 'type' => 'file', 'icon' => 'file file-'.$ext);
            }
        }
        if($with_root && $this->id($dir) === '/') {
            $res = array(array('text' => basename($this->base), 'children' => $res, 'id' => '/', 'icon'=>'folder', 'state' => array('opened' => true, 'disabled' => true)));
        }
        return $res;
    }
    public function data($id) {
        if(strpos($id, ":")) {
            $id = array_map(array($this, 'id'), explode(':', $id));
            return array('type'=>'multiple', 'content'=> 'Multiple selected: ' . implode(' ', $id));
        }
        $dir = $this->path($id);
        if(is_dir($dir)) {
            return array('type'=>'folder', 'content'=> $id);
        }
        if(is_file($dir)) {
            $ext = strpos($dir, '.') !== FALSE ? substr($dir, strrpos($dir, '.') + 1) : '';
            $dat = array('type' => $ext, 'content' => '');
            switch($ext) {
                case 'txt':
                case 'text':
                case 'md':
                case 'js':
                case 'json':
                case 'css':
                case 'html':
                case 'htm':
                case 'xml':
                case 'c':
                case 'cpp':
                case 'h':
                case 'sql':
                case 'log':
                case 'py':
                case 'rb':
                case 'htaccess':
                case 'php':
                    $dat['content'] = file_get_contents($dir);
                    break;
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'png':
                case 'bmp':
                    if( class_exists( 'finfo_file' ) )
                        $dat['content'] = 'data:'.finfo_file(finfo_open(FILEINFO_MIME_TYPE), $dir).';base64,'.base64_encode(file_get_contents($dir));
                    break;
                default:
                    $dat['content'] = 'File not recognized: '.$this->id($dir);
                    break;
            }
            return $dat;
        }
        throw new Exception('Not a valid selection: ' . $dir);
    }
    public function create($id, $name, $mkdir = false) {
        $dir = $this->path($id);
        if(preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !strlen($name)) {
            throw new Exception('Invalid name: ' . $name);
        }
        if($mkdir) {
            mkdir($dir . DIRECTORY_SEPARATOR . $name);
        }
        else {
            file_put_contents($dir . DIRECTORY_SEPARATOR . $name, '');
        }
        return array('id' => $this->id($dir . DIRECTORY_SEPARATOR . $name));
    }
    public function rename($id, $name) {
        $dir = $this->path($id);
        if($dir === $this->base) {
            throw new Exception('Cannot rename root');
        }
        if(preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !strlen($name)) {
            throw new Exception('Invalid name: ' . $name);
        }
        $new = explode(DIRECTORY_SEPARATOR, $dir);
        array_pop($new);
        array_push($new, $name);
        $new = implode(DIRECTORY_SEPARATOR, $new);
        if($dir !== $new) {
            if(is_file($new) || is_dir($new)) { throw new Exception('Path already exists: ' . $new); }
            rename($dir, $new);
        }
        return array('id' => $this->id($new));
    }
    public function remove($id) {
        $dir = $this->path($id);
        if($dir === $this->base) {
            throw new Exception('Cannot remove root');
        }
        if(is_dir($dir)) {
            foreach(array_diff(scandir($dir), array(".", "..")) as $f) {
                $this->remove($this->id($dir . DIRECTORY_SEPARATOR . $f));
            }
            rmdir($dir);
        }
        if(is_file($dir)) {
            unlink($dir);
        }
        return array('status' => 'OK');
    }
    public function move($id, $par) {
        $dir = $this->path($id);
        $par = $this->path($par);
        $new = explode(DIRECTORY_SEPARATOR, $dir);
        $new = array_pop($new);
        $new = $par . DIRECTORY_SEPARATOR . $new;
        rename($dir, $new);
        return array('id' => $this->id($new));
    }
    public function copy($id, $par) {
        $dir = $this->path($id);
        $par = $this->path($par);
        $new = explode(DIRECTORY_SEPARATOR, $dir);
        $new = array_pop($new);
        $new = $par . DIRECTORY_SEPARATOR . $new;
        if(is_file($new) || is_dir($new)) { throw new Exception('Path already exists: ' . $new); }
        if(is_dir($dir)) {
            mkdir($new);
            foreach(array_diff(scandir($dir), array(".", "..")) as $f) {
                $this->copy($this->id($dir . DIRECTORY_SEPARATOR . $f), $this->id($new));
            }
        }
        if(is_file($dir)) {
            copy($dir, $new);
        }
        return array('id' => $this->id($new));
    }
}
