<?php
if (!isset($core))
{
	require_once '../filemanager_user_core.php';
	$core = new filemanager_user_core();
    $core->userInfo();
    require_once '../filemanager_language_user.php';
}
$extra_num = 0;
function set_new_name_for_file($path, $name)
{
    global $extra_num;
    $extra_num++;
    $new_ext = explode(".", $name);
    $ext = end($new_ext);
    unset($new_ext[count($new_ext) - 1]);
    $new_name = implode($new_ext, ".");
    $new_name .= $extra_num.".".$ext;
    if(file_exists($path.$new_name))
    {
        return set_new_name_for_file($path, $name);
    }
    return $new_name;
}
if ($core->isLogin())
{
	if(isset($_POST["uploadDir"]) and isset($_FILES["file"]))
	{
        $_POST["uploadDir"] = $core->name_filter($_POST["uploadDir"]."/");
        if($core->check_base_root($_POST["uploadDir"]))
        {
            $_FILES['datafile'] = $_FILES['file'];
            $allowedExts = $core->get_option("allow_uploads_".$core->user_id);
            $size = $core->get_option("user_upload_limit_".$core->user_id);
            $mim_types = $core->get_mime_type();
            // application/x-zip-compressed
            // application/msword
            // application/vnd.ms-powerpoint
            // application/vnd.ms-excel
            if( isset( $mim_types->zip ) ) {
                array_push( $mim_types->zip, "application/x-zip-compressed" );
            }
            if( isset( $mim_types->docx ) ) {
                array_push( $mim_types->docx, "application/msword" );
            }
            if( isset( $mim_types->xlsx ) ) {
                array_push( $mim_types->xlsx, "application/vnd.ms-excel" );
            }
            if( isset( $mim_types->pptx ) ) {
                array_push( $mim_types->pptx, "application/vnd.ms-powerpoint" );
            }
            if(class_exists('finfo'))
            {
                $finfo = new finfo(FILEINFO_MIME);
            }
            $size = ($size * 1024) * 1024;

            $ret["status"] = 'Error';
            $ret["msg"] = "";

            $temp = explode(".", $_FILES["datafile"]["name"]);
            $extension = end($temp);
            $extension = strtolower($extension);
            $mim_type = strtolower($_FILES["datafile"]["type"]);
            if(class_exists('finfo'))
            {
                $mim_type = $finfo->file($_FILES["datafile"]["tmp_name"]);
                if(strpos($mim_type, ";"))
                {
                    $mim_type = explode(";", $mim_type);
                    $mim_type = $mim_type[0];
                }
            }
            if (in_array($extension, $allowedExts))
            {
                if(isset($mim_types->$extension))
                {
                    if (in_array($mim_type, $mim_types->$extension))
                    {
                        if($_FILES["datafile"]["size"] > $size)
                        {
                            $ret["msg"] = language_filter("Size Error", true).': ' . $_FILES["datafile"]["name"];
                        }
                        else if ($_FILES["datafile"]["error"] > 0)
                        {
                            $ret["msg"] = language_filter("Return Code", true).': ' . $_FILES["datafile"]["error"];
                        }
                        else
                        {
                            $name = $_FILES["datafile"]["name"];
                            if (file_exists($_POST["uploadDir"] . $_FILES["datafile"]["name"]))
                            {
                                $name = set_new_name_for_file($_POST["uploadDir"], $name);
                            }
                            if(move_uploaded_file($_FILES["datafile"]["tmp_name"], $_POST["uploadDir"] . $name))
                            {
                                $ret["status"] = "Success";
                                $ret["msg"] = language_filter("has been uploaded.", true);
                                array_push($_SESSION["lift_file_manager_list_of_files"], $_FILES["datafile"]["name"]);
                            }
                            else
                            {
                                $ret["msg"] = language_filter("Can not upload", true).' '.$name;
                            }
                        }
                    }
                    else
                    {
                        $ret["msg"] = language_filter("Invalid file", true).': '.$_FILES["datafile"]["name"];
                    }
                }
                else
                {
                    if($_FILES["datafile"]["size"] > $size)
                    {
                        $ret["msg"] = language_filter("Size Error", true).': ' . $_FILES["datafile"]["name"];
                    }
                    else if ($_FILES["datafile"]["error"] > 0)
                    {
                        $ret["msg"] = language_filter("Return Code", true).': ' . $_FILES["datafile"]["error"];
                    }
                    else
                    {
                        $name = $_FILES["datafile"]["name"];
                        if (file_exists($_POST["uploadDir"] . $_FILES["datafile"]["name"]))
                        {
                            $name = set_new_name_for_file($_POST["uploadDir"], $name);
                        }
                        if(move_uploaded_file($_FILES["datafile"]["tmp_name"], $_POST["uploadDir"] . $name))
                        {
                            $ret["status"] = "Success";
                            $ret["msg"] = language_filter("has been uploaded.", true);
                            array_push($_SESSION["lift_file_manager_list_of_files"], $_FILES["datafile"]["name"]);
                        }
                        else
                        {
                            $ret["msg"] = language_filter("Can not upload", true).' '.$name;
                        }
                    }
                }
            }
            else
            {
                echo $ret["msg"] = language_filter("Invalid file", true).': '.$_FILES["datafile"]["name"];
            }
            echo $core->_encode( $ret );
            die();
        }
        else {
            $custom_error = array();
            $custom_error['status'] = "error";
            $custom_error['msg'] = language_filter("Can not upload", true);
            echo $core->_encode($custom_error);
            die();
        }
	}
    if(isset($_POST["uploadDir"]) and isset($_FILES["datafile"])) {
        $_POST["uploadDir"] = $core->name_filter($_POST["uploadDir"]."/");
        if($core->check_base_root($_POST["uploadDir"]))
        {
            $allowedExts = $core->get_option("allow_uploads_".$core->user_id);
            $size = $core->get_option("user_upload_limit_".$core->user_id);
            $mim_types = $core->get_mime_type();
            // application/x-zip-compressed
            // application/msword
            // application/vnd.ms-powerpoint
            // application/vnd.ms-excel
            if( isset( $mim_types->zip ) ) {
                array_push( $mim_types->zip, "application/x-zip-compressed" );
            }
            if( isset( $mim_types->docx ) ) {
                array_push( $mim_types->docx, "application/msword" );
            }
            if( isset( $mim_types->xlsx ) ) {
                array_push( $mim_types->xlsx, "application/vnd.ms-excel" );
            }
            if( isset( $mim_types->pptx ) ) {
                array_push( $mim_types->pptx, "application/vnd.ms-powerpoint" );
            }
            if(class_exists('finfo'))
            {
                $finfo = new finfo(FILEINFO_MIME);
            }
            $size = ($size * 1024) * 1024;
            $count = 0;

            $notification_info["fullname"] = $core->user_firstname." ".$core->user_lastname;
            $notification_info["username"] = $core->user_username;
            $notification_info["email"] = $core->user_email;
            $notification_info["date"] = date("Y-m-d H:i:s");
            $notification_info["folder"] = $_POST["uploadDir"];
            $notification_info["list_of_files"] = array();
            $notification_info["id"] = $core->user_id;

            foreach ($_FILES['datafile']['name'] as $filename)
            {
                $temp = explode(".", $_FILES["datafile"]["name"][$count]);
                $extension = end($temp);
                $extension = strtolower($extension);
                $mim_type = strtolower($_FILES["datafile"]["type"][$count]);
                if(class_exists('finfo'))
                {
                    $mim_type = $finfo->file($_FILES["datafile"]["tmp_name"][$count]);
                    if(strpos($mim_type, ";"))
                    {
                        $mim_type = explode(";", $mim_type);
                        $mim_type = $mim_type[0];
                    }
                }
                if (in_array($extension, $allowedExts))
                {
                    if(isset($mim_types->$extension))
                    {
                        if (in_array($mim_type, $mim_types->$extension))
                        {
                            if($_FILES["datafile"]["size"][$count] > $size)
                            {
                                echo '<div class="alert alert-danger" style="text-align: center;">'.language_filter("Size Error", true).': ' . $_FILES["datafile"]["name"][$count].'</div>';
                            }
                            else if ($_FILES["datafile"]["error"][$count] > 0)
                            {
                                echo '<div class="alert alert-danger" style="text-align: center;">'.language_filter("Return Code", true).': ' . $_FILES["datafile"]["error"][$count].'</div>';
                            }
                            else
                            {
                                $name = $_FILES["datafile"]["name"][$count];
                                if (file_exists($_POST["uploadDir"] . $_FILES["datafile"]["name"][$count]))
                                {
                                    $name = set_new_name_for_file($_POST["uploadDir"], $name);
                                }
                                if(move_uploaded_file($_FILES["datafile"]["tmp_name"][$count], $_POST["uploadDir"] . $name))
                                {
                                    echo '<div class="alert alert-success" style="text-align: center;">'.$name.' '.language_filter("has been uploaded.", true).'</div>';
                                    array_push($notification_info["list_of_files"], $_FILES["datafile"]["name"][$count]);
                                }
                                else
                                {
                                    echo '<div class="alert alert-success" style="text-align: center;">'.language_filter("Can not upload", true).' '.$name.'.</div>';
                                }
                            }
                        }
                        else
                        {
                            echo '<div class="alert alert-danger" style="text-align: center;">'.language_filter("Invalid file", true).': '.$_FILES["datafile"]["name"][$count].'</div>';
                        }
                    }
                    else
                    {
                        if($_FILES["datafile"]["size"][$count] > $size)
                        {
                            echo '<div class="alert alert-danger" style="text-align: center;">'.language_filter("Size Error", true).': ' . $_FILES["datafile"]["name"][$count].'</div>';
                        }
                        else if ($_FILES["datafile"]["error"][$count] > 0)
                        {
                            echo '<div class="alert alert-danger" style="text-align: center;">'.language_filter("Return Code", true).': ' . $_FILES["datafile"]["error"][$count].'</div>';
                        }
                        else
                        {
                            $name = $_FILES["datafile"]["name"][$count];
                            if (file_exists($_POST["uploadDir"] . $_FILES["datafile"]["name"][$count]))
                            {
                                $name = set_new_name_for_file($_POST["uploadDir"], $name);
                            }
                            if(move_uploaded_file($_FILES["datafile"]["tmp_name"][$count], $_POST["uploadDir"] . $name))
                            {
                                echo '<div class="alert alert-success" style="text-align: center;">'.$name.' '.language_filter("has been uploaded.", true).'</div>';
                                array_push($notification_info["list_of_files"], $_FILES["datafile"]["name"][$count]);
                            }
                            else
                            {
                                echo '<div class="alert alert-success" style="text-align: center;">'.language_filter("Can not upload", true).' '.$name.'.</div>';
                            }
                        }
                    }
                }
                else
                {
                    echo '<div class="alert alert-danger" style="text-align: center;">'.language_filter("Invalid file", true).': '.$_FILES["datafile"]["name"][$count].'</div>';
                }
                $count++;
            }
            if(!empty($notification_info["list_of_files"]))
            {
                require_once "../filemanager_assets/send_notify.php";
                $send = new send_notifications();
                $send->send_mails($notification_info);
            }
        }
    }
    if( isset( $_POST["uploadedDirectory"] ) and isset( $_SESSION["lift_file_manager_list_of_files"] ) ) {
        if( !empty( $_SESSION["lift_file_manager_list_of_files"] ) ) {
            $notification_info["fullname"] = $core->user_firstname." ".$core->user_lastname;
            $notification_info["username"] = $core->user_username;
            $notification_info["email"] = $core->user_email;
            $notification_info["date"] = date("Y-m-d H:i:s");
            $notification_info["folder"] = $core->name_filter( str_replace( $core->system_root_dir, "", $_POST["uploadedDirectory"] ) );
            $notification_info["list_of_files"] = $_SESSION["lift_file_manager_list_of_files"];
            $notification_info["id"] = $core->user_id;
            $_SESSION["lift_file_manager_list_of_files"] = array();
            if(!empty($notification_info["list_of_files"]))
            {
                require_once "../filemanager_assets/send_notify.php";
                $send = new send_notifications();
                $send->send_mails($notification_info);
            }
        }
    }
}
?>
