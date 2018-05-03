<?php
if (!isset($core))
{
	require_once '../filemanager_user_core.php';
	$core = new filemanager_user_core();
    $core->userInfo();
    require_once '../filemanager_language_user.php';
}
if ($core->isLogin())
{
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    {
        if(isset($_POST["filename"]))
        {
            if($core->user_can_do_it($core->user_id, "rename_folder", $core->user_limitation) or $core->user_can_do_it($core->user_id, "copy_folders", $core->user_limitation) or $core->user_can_do_it($core->user_id, "move_folders", $core->user_limitation))
            {
                $oldName = $core->system_root_dir.ROOT_DIR_PATH.$_POST["filename"];
                $oldName = $core->name_filter($oldName);
                if( isset( $_POST["move_method"] ) ) {
                    if( $_POST["move_method"] == "move" ) {
                        $newName = $core->name_filter($core->user_dir.$_POST["newName"]);
                    }
                    else {
                        $this_dir_path = $core->name_filter($core->system_root_dir.ROOT_DIR_PATH.$_POST["this_dir_path"]);
                        $newName = $core->name_filter($this_dir_path.'/'.$_POST["newName"]);
                    }
                }
                else {
                    $newName = $core->name_filter($core->user_dir."/".$_POST["newName"]);
                }
                if(isset($_POST["copy_this"]))
                {
                    if($core->check_base_root($newName))
                    {
                        if(copy($oldName, $newName))
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
                else
                {
                    if($core->check_base_root($newName))
                    {
                        if( is_file( $newName ) ) {
                            echo 'false';
                        }
                        else {
                            if(rename($oldName, $newName))
                            {
                                echo 'true';
                            }
                            else
                            {
                                echo 'false';
                            }
                        }
                    }
                    else
                    {
                        echo 'false';
                    }
                }
            }
            else
            {
                echo 'false';
            }
        }

        if(isset($_POST["dirname"]))
        {
            if($core->user_can_do_it($core->user_id, "rename_folder", $core->user_limitation) or $core->user_can_do_it($core->user_id, "copy_folders", $core->user_limitation) or $core->user_can_do_it($core->user_id, "move_folders", $core->user_limitation))
            {
                $oldName = $core->name_filter($core->system_root_dir.ROOT_DIR_PATH.$_POST["dirname"]);
                if( isset( $_POST["move_method"] ) ) {
                    if( $_POST["move_method"] == "move" ) {
                        $newName = $core->name_filter($core->user_dir.$_POST["newName"]);
                    }
                    else {
                        $this_dir_path = $core->name_filter($core->system_root_dir.ROOT_DIR_PATH.$_POST["this_dir_path"]);
                        $newName = $core->name_filter($this_dir_path.'/'.$_POST["newName"]);
                    }
                }
                else {
                    $newName = $core->name_filter($core->user_dir."/".$_POST["newName"]);
                }
                if(isset($_POST["copy_this"]))
                {
                    if($core->check_base_root($newName))
                    {
                        $core->copy_directory($oldName, $newName);
                        echo 'true';
                    }
                    else
                    {
                        echo 'false';
                    }
                }
                else
                {
                    if($core->check_base_root($newName))
                    {
                        if($core->rename_directory($oldName, $newName))
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
            }
            else
            {
                echo 'false';
            }
        }

        if(isset($_POST["removeDirName"])) // done
        {
            if($core->user_can_do_it($core->user_id, "remove_folders"))
            {
                $name = $core->system_root_dir.ROOT_DIR_PATH.$_POST["removeDirName"];
                $name = $core->name_filter($name);
                if($core->check_base_root($name))
                {
                    if($core->recursiveDelete($name))
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
            else
            {
                echo 'false';
            }
        }

        if(isset($_POST["removeFileName"])) // done
        {
            if($core->user_can_do_it($core->user_id, "remove_folders"))
            {
                $name = $core->system_root_dir.ROOT_DIR_PATH.$_POST["removeFileName"];
                $name = $core->name_filter($name);
                if($core->check_base_root($name))
                {
                    if(@unlink($name))
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
            else
            {
                echo 'false';
            }
        }

        if(isset($_POST["mkdir_path"]))
        {
            if($core->user_can_do_it($core->user_id, "create_folder", $core->user_limitation))
            {
                $str_pos = strpos($_POST["mkdir_path"], "../");
                if($str_pos !== false)
                {
                    echo 'false';
                    exit;
                }
                $pathname = $core->system_root_dir.ROOT_DIR_PATH.$_POST["this_place"]."/".$_POST["mkdir_path"];
                $pathname = $core->name_filter($pathname);
                if($core->check_base_root( $pathname ))
                {
                    if(mkdir($pathname, 0755, true))
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
            else
            {
                echo 'false';
            }
        }

        if(isset($_POST["create_zip"])) // done
        {
            if($core->user_can_do_it($core->user_id, "zip_folders", $core->user_limitation) or $core->user_can_do_it($core->user_id, "backup_folders", $core->user_limitation))
            {
                $dir = $core->system_root_dir.ROOT_DIR_PATH.$_POST["this_place"];
                if($dir != "../")
                    $dir .= "/";
                $zip_name = $_POST["zip_name"];
                $realName = $zip_name; // for check backup name

                if(is_file($dir.$zip_name.".zip"))
                {
                    $zip_name = $zip_name."_".rand();
                }
                if(is_dir($dir.$zip_name))
                {
                    $zip_name = $zip_name."_".rand();
                }

                $zip_name = $dir.$zip_name;
                $files_folders = $_POST["create_zip"];
                if($core->check_base_root($zip_name))
                {
                    if(mkdir($zip_name, 0755))
                    {
                        foreach ($files_folders as $value)
                        {
                            if(is_dir($dir.$value))
                            {
                                $core->copy_directory($dir.$value, $zip_name."/".$value);
                            }
                            else
                            {
                                copy($dir.$value, $zip_name."/".$value);
                            }
                        }
                        if($core->create_zip($zip_name, $zip_name))
                        {
                            $core->recursiveDelete($zip_name);
                            if(isset($_POST["create_back_up"]))
                            {
                                $backup_dir = realpath( "../".ROOT_DIR_PATH."/filemanager_backups/" )."/";
                                $new_zip_name = $realName;
                                //$new_zip_name = str_replace("../", "", $new_zip_name);

                                if(is_file($backup_dir.$new_zip_name.".zip"))
                                {
                                    $new_zip_name = $new_zip_name.'_'.rand();
                                }
                                $username = $core->user_username;
                                $new_zip_name .= '.zip';
                                $new_zip_name = end(explode("/", $new_zip_name));
                                $new_zip_name = $username."_".$new_zip_name;
                                if (rename($zip_name.'.zip', $backup_dir.$new_zip_name))
                                    echo 'true';
                                else
                                    echo 'false';
                            }
                            else
                            {
                                echo "true";
                            }
                        }
                        else
                        {
                            $core->recursiveDelete($zip_name);
                            echo "false";
                        }
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
            else
            {
                echo 'false';
            }
        }

        if(isset($_POST["un_zip"])) // done
        {
            $path_location = $core->system_root_dir.ROOT_DIR_PATH.$_POST["path_location"];
            $path_location = $core->name_filter( $path_location );
            $un_zip = $core->system_root_dir.ROOT_DIR_PATH.$_POST["un_zip"];
            $un_zip = $core->name_filter( $un_zip );
            if($core->user_can_do_it($core->user_id, "unzip_files", $core->user_limitation))
            {
                if($core->check_base_root($path_location))
                {
                    if($core->extract_zip($un_zip, $path_location))
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
            else
            {
                echo 'false';
            }
        }

        if(isset($_POST["remove_selected"])) // done
        {
            if($core->user_can_do_it($core->user_id, "remove_folders"))
            {
                $files_and_folders = $_POST["remove_selected"];
                $dir = $core->system_root_dir.ROOT_DIR_PATH.$_POST["this_path"];
                if($dir != "../")
                    $dir .= "/";

                $flag = true;
                $errors = "";
                if($core->check_base_root($dir))
                {
                    foreach ($files_and_folders as $value)
                    {
                        if(is_dir($dir.$value))
                        {
                            if(!$core->recursiveDelete($dir.$value))
                            {
                                $flag = false;
                                $errors[] = $value;
                            }
                        }

                        if(is_file($dir.$value))
                        {
                            if(!@unlink($dir.$value))
                            {
                                $flag = false;
                                $errors[] = $value;
                            }
                        }
                    }

                    if($flag)
                        echo 'true';
                    else
                        echo implode(", ", $errors);
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

        if(isset($_POST["move_selected"]))
        {
            $files_and_folders = $_POST["move_selected"];
            $dir = $core->system_root_dir.ROOT_DIR_PATH.$_POST["this_path"];
            $newName = $_POST["move_path"]."/";
            if($dir != "../")
                $dir .= "/";
            $flag = true;
            $errors = '';
            if($core->check_base_root($newName))
            {
                foreach ($files_and_folders as $value)
                {
                    if(is_dir($dir.$value))
                    {
                        if(!$core->rename_directory($dir.$value, $newName.$value))
                        {
                            $flag = false;
                            $errors[] = $value;
                        }
                    }

                    if(is_file($dir.$value))
                    {
                        if( is_file( $newName.$value ) ) {
                            $flag = false;
                            $errors[] = $value;
                        }
                        else {
                            if(!rename($dir.$value, $newName.$value))
                            {
                                $flag = false;
                                $errors[] = $value;
                            }
                        }
                    }
                }
                if($flag)
                {
                    echo 'true';
                }
                else
                {
                    echo implode(", ", $errors);
                }
            }
            else
            {
                language_filter("Can_not_move_files_folders");
            }
        }

        if(isset($_POST["copy_selected"]))
        {
            $files_and_folders = $_POST["copy_selected"];
            $dir = $core->name_filter( $core->system_root_dir.ROOT_DIR_PATH.$_POST["this_path"] );
            $newName = $_POST["copy_path"]."/";
            if($dir != "../")
                $dir .= "/";
            $flag = true;
            $errors = '';
            if($core->check_base_root($newName))
            {
                foreach ($files_and_folders as $value)
                {
                    if(is_dir($dir.$value))
                    {
                        $core->copy_directory($dir.$value, $newName.$value);
                    }

                    if(is_file($dir.$value))
                    {
                        if(!copy($dir.$value, $newName.$value))
                        {
                            $flag = false;
                            $errors[] = $value;
                        }
                    }
                }
                if($flag)
                {
                    echo 'true';
                }
                else
                {
                    echo implode(", ", $errors);
                }
            }
            else
            {
                language_filter("Can_not_copy_files_folders");
            }
        }

        if(isset($_POST["download_selected"]))
        {
            $dir = $core->name_filter( $core->system_root_dir.ROOT_DIR_PATH.$_POST["this_path"]."/" );

            $zip_name = date("YmdHis");
            $real_zip_name = $zip_name;
            $realName = $zip_name;
            $temp_dir = $core->user_dir."/";

            if(is_file($temp_dir.$zip_name.".zip"))
            {
                $zip_name = $zip_name."_".rand();
                $real_zip_name = $zip_name;
            }

            $zip_name = $temp_dir.$zip_name;
            $files_folders = $_POST["download_selected"];

            if($core->check_base_root($dir))
            {
                if(mkdir($zip_name, 0755))
                {
                    foreach ($files_folders as $value)
                    {
                        if(is_dir($dir.$value))
                        {
                            $core->copy_directory($dir.$value, $zip_name."/".$value);
                        }
                        else
                        {
                            copy($dir.$value, $zip_name."/".$value);
                        }
                    }

                    if($core->create_zip($zip_name, $zip_name))
                    {
                        $core->recursiveDelete($zip_name);
                        $file = $zip_name.".zip";
                        if( isset( $_POST["extra_dir_show"] ) and @$_POST["extra_dir_show"] != 0 ) {
                            echo "filemanager_user/download.php?filename=".base64_encode(utf8_encode($file))."&dir=".base64_encode(utf8_encode($dir))."&switch=".(int) mysql_real_escape_string( $_POST["extra_dir_show"] );
                        }
                        else {
                            echo "filemanager_user/download.php?filename=".base64_encode(utf8_encode($file))."&dir=".base64_encode(utf8_encode($dir));
                        }
                        exit;
                    }
                    else
                    {
                        $core->recursiveDelete($zip_name);
                        echo "false";
                    }
                }
                else
                {
                    echo "false";
                }
            }
            else
            {
                echo 'false';
            }
        }

        if(isset($_POST["share_selected"]))
        {
            $settings = $core->get_option("settings");
            if($settings->share == "on")
            {
                $dir = $core->system_root_dir.ROOT_DIR_PATH.$_POST["this_path"];
                if($dir != "../")
                    $dir .= "/";
                $zip_name = date("YmdHis");
                $realName = $zip_name;

                $temp_dir = $core->user_dir."/";

                if(is_file($temp_dir.$zip_name.".zip"))
                {
                    $zip_name = $zip_name."_".rand();
                }

                $zip_name = $temp_dir.$zip_name;
                $files_folders = $_POST["share_selected"];
                if($core->check_base_root($dir))
                {
                    $send_to = $_POST["send_to"];
                    $from = $_POST["from"];
                    $subject = $_POST["subject"];
                    $message = $_POST["message"];
                    $emails = $_POST["emails"];
                    if(mkdir($zip_name, 0755))
                    {
                        foreach ($files_folders as $value)
                        {
                            if(is_dir($dir.$value))
                            {
                                $core->copy_directory($dir.$value, $zip_name."/".$value);
                            }
                            else
                            {
                                copy($dir.$value, $zip_name."/".$value);
                            }
                        }
                        if($core->create_zip($zip_name, $zip_name))
                        {
                            $core->recursiveDelete($zip_name);
                            $file = $zip_name.".zip";
                            $core->share_files($send_to, $emails, $subject, $from, $message, $file);
                            sleep(1);
                            @unlink($file);
                            exit;
                        }
                        else
                        {
                            $core->recursiveDelete($zip_name);
                            echo "false";
                        }
                    }
                    else
                    {
                        echo "false";
                    }
                }
                else
                {
                    echo "false";
                }
            }
            else
            {
                echo "false";
            }
        }

        if( isset( $_POST["share_system_files"] ) ) {
            if( !empty( $_POST["share_system_files"])) {
                $files = $_POST["share_system_files"];
                $this_place = $_POST["this_place"];
                $description = mysql_real_escape_string( $_POST["description"] );
                $dir = $core->system_root_dir.ROOT_DIR_PATH.$_POST["this_place"]."/";
                if($core->check_base_root($dir))
                {
                    if( $core->share_system_files( $_POST["this_place"]."/", $files, $description, $core->user_id ) ) {
                        echo 'true';
                    }
                    else {
                        echo 'false';
                    }
                }
                else {
                    echo 'false';
                }
            }
            else {
                echo 'false';
            }
            exit();
        }

    }
}
else
{
	header("Status: 404 Not Found");
}
?>