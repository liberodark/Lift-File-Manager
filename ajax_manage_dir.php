<?php
if (!isset($core))
{
	require_once 'filemanager_core.php';
	$core = new filemanager_core();
    require_once 'filemanager_language.php';
}
if ($core->isLogin())
{
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    {
        if(isset($_POST["filename"]))
        {
            $oldName = ROOT_DIR_PATH.$_POST["filename"];
            $newName = ROOT_DIR_PATH.$_POST["newName"];
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
                    echo "false";
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
                    echo "false";
                }
            }
        }

        if(isset($_POST["dirname"]))
        {
            $oldName = ROOT_DIR_PATH.$_POST["dirname"];
            $newName = ROOT_DIR_PATH.$_POST["newName"];
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

        if(isset($_POST["removeDirName"]))
        {
            $name = ROOT_DIR_PATH.$_POST["removeDirName"];
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

        if(isset($_POST["removeFileName"]))
        {
            $name = ROOT_DIR_PATH.$_POST["removeFileName"];
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

        if(isset($_POST["mkdir_path"]))
        {
            $slash = "/";
            if($_POST["this_place"] == "../")
            {
                $slash = "";
            }
            $pathname = ROOT_DIR_PATH.$_POST["this_place"].$slash.$_POST["mkdir_path"];
            if($core->check_base_root($pathname))
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
                echo "false";
            }
        }

        if(isset($_POST["create_zip"]))
        {
            $dir = ROOT_DIR_PATH.$_POST["this_place"]."/";
            $zip_name = $_POST["zip_name"];
            $realName = $zip_name; // for check backup name

            if(is_file($dir.$zip_name.".zip"))
            {
                $zip_name = $zip_name."_".rand();
                $realName = $zip_name; // for check backup name
            }
            if(is_dir($dir.$zip_name))
            {
                $zip_name = $zip_name."_".rand();
                $realName = $zip_name; // for check backup name
            }

            $zip_name = $dir.$zip_name;
            $files_folders = $_POST["create_zip"];
            if($core->check_base_root($zip_name))
            {
                if(mkdir($zip_name, 0755))
                {
                    foreach ($files_folders as $value)
                    {
                        $filename = basename( $value );
                        if(is_dir($value))
                        {
                            $core->copy_directory($value, $zip_name."/".$filename);
                        }
                        else
                        {
                            copy($value, $zip_name."/".$filename);
                        }
                    }
                    if($core->create_zip($zip_name, $zip_name))
                    {
                        $core->recursiveDelete($zip_name);
                        if(isset($_POST["create_back_up"]))
                        {
                            $backup_dir = realpath( "filemanager_backups" )."/";
                            $new_zip_name = $realName;

                            if(is_file($backup_dir.$new_zip_name.".zip"))
                            {
                                $new_zip_name = $new_zip_name.'_'.rand();
                            }
                            $new_zip_name .= '.zip';
                            $new_zip_name = end(explode("/", $new_zip_name));
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
                echo "false";
            }
        }

        if(isset($_POST["un_zip"]))
        {
            $dir = ROOT_DIR_PATH.$_POST["path_location"];
            $unzip = ROOT_DIR_PATH.$_POST["un_zip"];
            $dir = str_replace( "//", "/", $dir );
            $unzip = str_replace( "//", "/", $unzip );
            if($core->check_base_root($dir))
            {
                if($core->extract_zip($unzip, $dir))
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

        if(isset($_POST["remove_selected"]))
        {
            $files_and_folders = $_POST["remove_selected"];
            $dir = ROOT_DIR_PATH.$_POST["this_path"];
            $dir = str_replace( "//", "/", $dir );
            $flag = true;
            $errors = "";
            if($core->check_base_root($dir))
            {
                foreach ($files_and_folders as $value)
                {
                    $filename = "/".basename( $value );
                    if(is_dir($dir.$filename))
                    {
                        if(!$core->recursiveDelete($dir.$filename))
                        {
                            $flag = false;
                            $errors[] = $filename;
                        }
                    }

                    if(is_file($dir.$filename))
                    {
                        if(!@unlink($dir.$filename))
                        {
                            $flag = false;
                            $errors[] = $filename;
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
                language_filter("Can_not_remove_files_folders");
            }
        }

        if(isset($_POST["move_selected"]))
        {
            $files_and_folders = $_POST["move_selected"];
            $dir = ROOT_DIR_PATH.$_POST["this_path"];
            $newName = ROOT_DIR_PATH.$_POST["move_path"]."/";
            $dir = str_replace( "//", "/", $dir );
            $newName = str_replace( "//", "/", $newName );
            $flag = true;
            $errors = '';
            if($core->check_base_root($newName))
            {
                foreach ($files_and_folders as $value)
                {
                    $filename = basename( $value );
                    if(is_dir($value))
                    {
                        if(!$core->rename_directory($value, $newName.$filename))
                        {
                            $flag = false;
                            $errors[] = $filename;
                        }
                    }

                    if(is_file($value))
                    {
                        if( is_file( $newName.$filename ) ) {
                            $flag = false;
                            $errors[] = $filename;
                        }
                        else {
                            if(!rename($value, $newName.$filename))
                            {
                                $flag = false;
                                $errors[] = $filename;
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
            $dir = ROOT_DIR_PATH.$_POST["this_path"];
            $newName = ROOT_DIR_PATH.$_POST["copy_path"]."/";
            $dir = str_replace( "//", "/", $dir );
            $newName = str_replace( "//", "/", $newName );
            $flag = true;
            $errors = '';
            if($core->check_base_root($newName))
            {
                foreach ($files_and_folders as $value)
                {
                    $filename = basename( $value );
                    if( is_dir( $value ) )
                    {
                        @$core->copy_directory($value, $newName.$filename);
                    }

                    if( is_file( $value ) )
                    {
                        if( @!copy( $value, $newName.$filename ) )
                        {
                            $flag = false;
                            $errors[] = $filename;
                        }
                    }
                }
                if( $flag )
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
            $dir = ROOT_DIR_PATH.$_POST["this_path"];
            $zip_name = date("YmdHis");
            $_file = $zip_name;
            $realName = $zip_name;

            $temp_dir = ROOT_DIR_PATH;

            if(is_file($temp_dir.$zip_name.".zip"))
            {
                $zip_name = $zip_name."_".rand();
                $_file = $zip_name;
            }

            $zip_name = $temp_dir.$zip_name;
            $files_folders = $_POST["download_selected"];
            if( $core->check_base_root($zip_name) )
            {
                if(mkdir($zip_name, 0755))
                {
                    foreach ($files_folders as $value)
                    {
                        $filename = basename( $value );
                        if(is_dir($value))
                        {
                            $core->copy_directory($value, $zip_name."/".$filename);
                        }
                        else
                        {
                            copy($value, $zip_name."/".$filename);
                        }
                    }
                    if($core->create_zip($zip_name, $zip_name))
                    {
                        $core->recursiveDelete($zip_name);
                        $file = $_file.".zip";
                        echo "download.php?filename=".base64_encode(utf8_encode($file));
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

        if(isset($_POST["share_selected"]))
        {
            $dir = ROOT_DIR_PATH.$_POST["this_path"];
            $zip_name = date("YmdHis");
            $realName = $zip_name;

            $temp_dir = ROOT_DIR_PATH;

            if(is_file($temp_dir.$zip_name.".zip"))
            {
                $zip_name = $zip_name."_".rand();
                $realName = $zip_name;
            }

            $zip_name = $temp_dir.$zip_name;
            $files_folders = $_POST["share_selected"];
            if($core->check_base_root($zip_name))
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
                        $filename = basename( $value );
                        if(is_dir($value))
                        {
                            $core->copy_directory($value, $zip_name."/".$filename);
                        }
                        else
                        {
                            copy($value, $zip_name."/".$filename);
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

        if( isset( $_POST["share_system_files"] ) ) {
            if( !empty( $_POST["share_system_files"])) {
                $files = $_POST["share_system_files"];
                $this_place = $_POST["this_place"];
                $description = mysql_real_escape_string( $_POST["description"] );
                $dir = ROOT_DIR_PATH.$_POST["this_place"];
                if($core->check_base_root($dir))
                {
                    $core->adminInfo();
                    if( $core->share_system_files( $_POST["this_place"]."/", $files, $description, $core->admin_id ) ) {
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