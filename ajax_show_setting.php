<?php
if (!isset($core))
{
    require_once 'filemanager_core.php';
    require_once 'option_class.php';
    $core = new filemanager_core();
    $option = new option_class();
    require_once 'filemanager_language.php';
}
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
{
    if ($core->isLogin())
    {
        if (isset($_POST["showSetting"]))
        {
            $core->adminInfo();
            if ($_POST["showSetting"] == $core->admin_id)
            {
                $result = "";
                $status = 0;
                if(isset($_POST["remove_ext"]))
                {
                    $method = $_POST["method_ext"];
                    $ext_name = $_POST["ext_name"];
                    if($method == "filemanager")
                    {
                        $allow_ext_filemanager = $option->get_option("allow_extensions");
                        foreach($allow_ext_filemanager as $key => $value)
                        {
                            if($value == $ext_name)
                            {
                                unset($allow_ext_filemanager[$key]);
                            }
                        }
                        $content = array_values($allow_ext_filemanager);
                        if($option->update_option("allow_extensions", $content))
                        {
                            $status = 1;
                            $result = language_filter("Your file manager extensions has been updated.", true);
                        }
                        else
                        {
                            $result = language_filter("Your file manager extensions has not been updated.", true);
                        }
                    }

                    if($method == "uploader")
                    {
                        $allow_ext_uploader = $option->get_option("allow_uploads");
                        foreach($allow_ext_uploader as $key => $value)
                        {
                            if($value == $ext_name)
                            {
                                unset($allow_ext_uploader[$key]);
                            }
                        }
                        $content = array_values($allow_ext_uploader);
                        if($option->update_option("allow_uploads", $content))
                        {
                            $status = 1;
                            $result = language_filter("Your uploader extensions has been updated.", true);
                        }
                        else
                        {
                            $result = language_filter("Your uploader extensions has not been updated.", true);
                        }
                    }
                }
                if(isset($_POST["add_ext"]))
                {
                    $method = $_POST["method_ext"];
                    $ext_name = $_POST["ext_name"];
                    $flag = false;
                    if($method == "filemanager")
                    {
                        $allow_ext_filemanager = $option->get_option("allow_extensions");
                        foreach($allow_ext_filemanager as $key => $value)
                        {
                            if($value == $ext_name)
                            {
                                $flag = true;
                                break;
                            }
                        }
                        if($flag == false)
                        {
                            $count = count($allow_ext_filemanager);
                            $allow_ext_filemanager[$count] = $ext_name;
                            $content = array_values($allow_ext_filemanager);
                            if($option->update_option("allow_extensions", $content))
                            {
                                $status = 1;
                                $result = language_filter("Your file manager extensions has been updated.", true);
                            }
                            else
                            {
                                $result = language_filter("Your file manager extensions has not been updated.", true);
                            }
                        }
                        else
                        {
                            $result = language_filter("Extension exists", true);
                        }
                    }

                    if($method == "uploader")
                    {
                        $allow_ext_uploader = $option->get_option("allow_uploads");
                        foreach($allow_ext_uploader as $key => $value)
                        {
                            if($value == $ext_name)
                            {
                                $flag = true;
                                break;
                            }
                        }
                        if($flag == false)
                        {
                            $count = count($allow_ext_uploader);
                            $allow_ext_uploader[$count] = $ext_name;
                            $content = array_values($allow_ext_uploader);
                            if($option->update_option("allow_uploads", $content))
                            {
                                $status = 1;
                                $result = language_filter("Your uploader extensions has been updated.", true);
                            }
                            else
                            {
                                $result = language_filter("Your uploader extensions has not been updated.", true);
                            }
                        }
                        else
                        {
                            $result = language_filter("Extension exists", true);
                        }
                    }
                }
                if(isset($_POST["change_status"]))
                {
                    $change_status = $_POST["change_status"];
                    if($change_status == "ticket")
                    {
                        $change = $_POST["status"];
                        $settings = $option->get_option("settings");
                        $settings->ticket = $change;
                        $settings = (array) $settings;
                        if($option->update_option("settings", $settings))
                        {
                            $status = 1;
                            $result = language_filter("ticket_status_done_alert", true);
                        }
                        else
                        {
                            $status = 0;
                            $result = language_filter("ticket_status_error_alert", true);
                        }
                    }

                    if($change_status == "share")
                    {
                        $change = $_POST["status"];
                        $settings = $option->get_option("settings");
                        $settings->share = $change;
                        $settings = (array) $settings;
                        if($option->update_option("settings", $settings))
                        {
                            $status = 1;
                            $result = language_filter("share_status_done_alert", true);
                        }
                        else
                        {
                            $status = 0;
                            $result = language_filter("share_status_error_alert", true);
                        }
                    }

                    if( $change_status == "download_link")
                    {
                        $change = $_POST["status"];
                        $settings = $option->get_option("settings");
                        @$settings->download_link = $change;
                        $settings = (array) $settings;
                        if($option->update_option("settings", $settings))
                        {
                            $status = 1;
                            $result = language_filter("download_status_done_alert", true);
                        }
                        else
                        {
                            $status = 0;
                            $result = language_filter("download_status_error_alert", true);
                        }
                    }

                    if( $change_status == "system_share" )
                    {
                        $change = $_POST["status"];
                        $settings = $option->get_option("settings");
                        @$settings->system_share = $change;
                        $settings = (array) $settings;
                        if($option->update_option("settings", $settings))
                        {
                            $status = 1;
                            $result = language_filter("system_share_status_done_alert", true);
                        }
                        else
                        {
                            $status = 0;
                            $result = language_filter("system_share_status_error_alert", true);
                        }
                    }

                    if($change_status == "admin_notification")
                    {
                        $change = $_POST["status"];
                        $settings = $option->get_option("settings");
                        $settings->admin_notification = $change;
                        $settings = (array) $settings;
                        if($option->update_option("settings", $settings))
                        {
                            $status = 1;
                            $result = language_filter("admin_notification_status_done_alert", true);
                        }
                        else
                        {
                            $status = 0;
                            $result = language_filter("admin_notification_status_error_alert", true);
                        }
                    }

                    if($change_status == "user_notification")
                    {
                        $change = $_POST["status"];
                        $settings = $option->get_option("settings");
                        $settings->user_notification = $change;
                        $settings = (array) $settings;
                        if($option->update_option("settings", $settings))
                        {
                            $status = 1;
                            $result = language_filter("user_notification_status_done_alert", true);
                        }
                        else
                        {
                            $status = 0;
                            $result = language_filter("user_notification_status_error_alert", true);
                        }
                    }

                    if($change_status == "user_registration")
                    {
                        $change_reg_status = $_POST['change_reg_status'];
                        $change = $_POST["status"];
                        $upload_limitation = (int) $_POST["upload_limitation"];
                        $userExtensions = $_POST["userExtensions"];
                        $userPermission = $_POST["userPermission"];
                        $userUploader = $_POST["userUploader"];
                        $userDir = $_POST["userDir"];
                        $user_limitation = $_POST["user_limitation"];
                        if($change_reg_status == "true")
                        {
                            $settings = $option->get_option("settings");
                            $settings->register = $change;
                            $settings = (array) $settings;
                            if($option->update_option("settings", $settings))
                            {
                                $register_settings = $option->get_option("register_settings");
                                $register_settings->permissions = $userPermission;
                                $register_settings->allow_ext = $userExtensions;
                                $register_settings->allow_upload = $userUploader;
                                $register_settings->upload_limitation = $upload_limitation;
                                $register_settings->users_dir = $userDir;
                                $register_settings->size_limitation = $user_limitation;
                                $register_settings = (array) $register_settings;
                                $option->update_option("register_settings", $register_settings);
                                $status = 1;
                                $result = language_filter("user_registration_status_done_alert", true);
                            }
                            else
                            {
                                $status = 0;
                                $result = language_filter("user_registration_status_error_alert", true);
                            }
                        }
                        else
                        {
                            $register_settings = $option->get_option("register_settings");
                            $register_settings->permissions = $userPermission;
                            $register_settings->allow_ext = $userExtensions;
                            $register_settings->allow_upload = $userUploader;
                            $register_settings->upload_limitation = $upload_limitation;
                            $register_settings->users_dir = $userDir;
                            $register_settings->size_limitation = $user_limitation;
                            $register_settings = (array) $register_settings;
                            if($option->update_option("register_settings", $register_settings))
                            {
                                $status = 1;
                                $result = language_filter("user_registration_status_done_alert", true);
                            }
                            else
                            {
                                $status = 0;
                                $result = language_filter("user_registration_status_error_alert", true);
                            }
                        }
                    }
                }
                $allow_ext_filemanager = $option->get_option("allow_extensions");
                $allow_ext_uploader = $option->get_option("allow_uploads");
                $settings = $option->get_option("settings");
                $register_settings = $option->get_option("register_settings");
?>
    <script src="filemanager_js/jqueryFileTree.js"></script>
    <div class="row">
        <nav class="navbar navbar-default navbar-xs setting-navbar" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="setting-navbar-brand navbar-brand" href="javascript:;"><b><?php language_filter("Settings");?></b></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="#" data-toggle="modal" data-target="#ticket" class="label <?php if($settings->ticket == "on") echo 'label-success'; else echo 'label-warning';?>"><?php if($settings->ticket == "on") language_filter("Ticket system is on"); else language_filter("Ticket system is off");?></a></li>
                    <li><a href="#" data-toggle="modal" data-target="#share" class="label <?php if($settings->share == "on") echo 'label-success'; else echo 'label-warning';?>"><?php if($settings->share == "on") language_filter("File sharing is on"); else language_filter("File sharing is off");?></a></li>
                    <li><a href="#" data-toggle="modal" data-target="#user_registration" class="label <?php if($settings->register == "on") echo 'label-success'; else echo 'label-warning';?>"><?php if($settings->register == "on") language_filter("User registration is on"); else language_filter("User registration is off");?></a></li>
                    <li><a href="#" data-toggle="modal" data-target="#download" class="label <?php if(@$settings->download_link == "on") echo 'label-success'; else echo 'label-warning';?>"><?php if(@$settings->download_link == "on") language_filter("Download link is on"); else language_filter("Download link is off");?></a></li>
                    <li><a href="#" data-toggle="modal" data-target="#system_sharing" class="label <?php if(@$settings->system_share == "on") echo 'label-success'; else echo 'label-warning';?>"><?php if(@$settings->system_share == "on") language_filter("system_share_is_on"); else language_filter("system_share_is_off");?></a></li>
                    <li><a href="#" data-toggle="modal" data-target="#admin_notification" class="label <?php if($settings->admin_notification == "on") echo 'label-success'; else echo 'label-warning';?>"><?php if($settings->admin_notification == "on") language_filter("Admin notification is on"); else language_filter("Admin notification is off");?></a></li>
                    <li><a href="#" data-toggle="modal" data-target="#user_notification" class="label <?php if($settings->user_notification == "on") echo 'label-success'; else echo 'label-warning';?>"><?php if($settings->user_notification == "on") language_filter("User notification is on"); else language_filter("User notification is off");?></a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </div>



    <div class="modal fade" id="user_registration" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="register_myModalLabel"><?php language_filter("Settings");?></h4>
                </div>
                <div class="modal-body">
                    <div class="radio">
                        <label>
                            <input type="radio" name="registration_status" id="registration_on"  <?php if($settings->register == "on") echo 'checked="checked"';?>>
                            <?php language_filter("on");?>
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="registration_status" id="registration_off" <?php if($settings->register == "off") echo 'checked="checked"';?>>
                            <?php language_filter("off");?>
                        </label>
                    </div>
                    <div class="form-group">
                        <div class="alert alert-info"><?php language_filter("register_alert");?></div>
                        <label for="user_dir"><?php language_filter("Select User Folder")?> <button type="button" class="btn btn-default" onclick="showTree()"><?php language_filter("Site Map")?></button></label>
                        <input type="text" name="user_dir" value="<?php echo addslashes($register_settings->users_dir);?>" class="form-control" id="user_dir" required="required">
                    </div>
                    <div class="form-group">
                        <label for="limitation"><?php language_filter("Set Size Limitation (MB)")?></label>
                        <input type="text" value="<?php echo $register_settings->size_limitation?>" name="limitation" id="limitation" class="form-control" required="required">
                    </div>
                    <div class="form-group">
                        <label for="user_edit_upload"><?php language_filter("Set Upload Size Limitation (MB)")?></label>
                        <input type="text" name="upload_limitation" id="upload_limitation" class="form-control" value="<?php echo $register_settings->upload_limitation;?>">
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-12">
                            <h4><?php language_filter("Registered user permissions");?> <span class="glyphicon glyphicon-sort" style="cursor: pointer;" onclick="$('#pemissions_register').slideToggle();"></span></h4>
                            <div class="table-responsive" id="pemissions_register">
                                <table class="table">

                                    <tr>
                                        <td><?php language_filter("Edit Profile")?></td>
                                        <td>
                                            <input type="checkbox" id="edit_profile" onclick="set_user_permissions(this.id)" <?php if(in_array("edit_profile", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Create Folder")?></td>
                                        <td>
                                            <input type="checkbox" id="create_folder" onclick="set_user_permissions(this.id)" <?php if(in_array("create_folder", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Rename Files And Folders")?></td>
                                        <td>
                                            <input type="checkbox" id="rename_folder" onclick="set_user_permissions(this.id)" <?php if(in_array("rename_folder", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Copy Files And Folders")?></td>
                                        <td>
                                            <input type="checkbox" id="copy_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("copy_folders", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Move Files And Folders")?></td>
                                        <td>
                                            <input type="checkbox" id="move_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("move_folders", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Remove Files And Folders")?></td>
                                        <td>
                                            <input type="checkbox" id="remove_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("remove_folders", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Create Zip From Files And Folders")?></td>
                                        <td>
                                            <input type="checkbox" id="zip_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("zip_folders", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Upload Files")?></td>
                                        <td>
                                            <input type="checkbox" id="upload_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("upload_folders", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Create Backup")?></td>
                                        <td>
                                            <input type="checkbox" id="backup_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("backup_folders", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Edit Text Files")?></td>
                                        <td>
                                            <input type="checkbox" id="edit_files" onclick="set_user_permissions(this.id)" <?php if(in_array("edit_files", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Edit Images")?></td>
                                        <td>
                                            <input type="checkbox" id="edit_img" onclick="set_user_permissions(this.id)" <?php if(in_array("edit_img", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?php language_filter("Unzip Zip Files")?></td>
                                        <td>
                                            <input type="checkbox" id="unzip_files" onclick="set_user_permissions(this.id)" <?php if(in_array("unzip_files", $register_settings->permissions)) echo 'checked="checked"';?>>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h4><?php language_filter("Allowed extensions for user in file manager")?> <span class="glyphicon glyphicon-sort" style="cursor: pointer;" onclick="$('#manager_ext_register').slideToggle();"></span></h4>
                            <div class="table-responsive" id="manager_ext_register">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th style="text-align: center">
                                            <button type="button" class="btn btn-default btn-xs" id="select_manager" onclick="select_all_ext('manager_')"><?php language_filter("Select All")?></button>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        for($i = 0; $i < count($allow_ext_filemanager); $i++)
                                        {
                                            ?>
                                        <tr>
                                            <td><?php echo $allow_ext_filemanager[$i];?></td>
                                            <td style="text-align: center">
                                                <input type="checkbox" id="manager_<?php echo $allow_ext_filemanager[$i];?>" onclick="set_filemanager_ext('<?php echo $allow_ext_filemanager[$i];?>')" <?php if(in_array($allow_ext_filemanager[$i], $register_settings->allow_ext)) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h4><?php language_filter("Allowed extensions for user in uploader");?> <span class="glyphicon glyphicon-sort" style="cursor: pointer;" onclick="$('#manager_up_register').slideToggle();"></span></h4>
                            <div class="table-responsive" id="manager_up_register">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th style="text-align: center">
                                            <button type="button" class="btn btn-default btn-xs" id="select_uploader" onclick="select_all_ext('uploader_')"><?php language_filter("Select All")?></button>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        for($i = 0; $i < count($allow_ext_uploader); $i++)
                                        {
                                        ?>
                                        <tr>
                                            <td><?php echo $allow_ext_uploader[$i];?></td>
                                            <td style="text-align: center">
                                                <input type="checkbox" id="uploader_<?php echo $allow_ext_uploader[$i];?>" onclick="set_uploader_ext('<?php echo $allow_ext_uploader[$i];?>');" <?php if(in_array($allow_ext_uploader[$i], $register_settings->allow_upload)) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Close")?></button>
                    <button type="button" class="btn btn-primary" onclick="change_user_registration_setting();"><?php language_filter("Apply");?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="user_notification" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="user_myModalLabel"><?php language_filter("Settings");?></h4>
                </div>
                <div class="modal-body">
                    <p><?php if($settings->user_notification == "on") echo language_filter("user_notification_off_q"); else language_filter("user_notification_on_q");?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Close")?></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="change_user_notification_status();"><?php language_filter("Apply");?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="admin_notification" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="admin_myModalLabel"><?php language_filter("Settings");?></h4>
                </div>
                <div class="modal-body">
                    <p><?php if($settings->admin_notification == "on") echo language_filter("admin_notification_off_q"); else language_filter("admin_notification_on_q");?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Close")?></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="change_admin_notification_status();"><?php language_filter("Apply");?></button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="share" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="share_myModalLabel"><?php language_filter("Settings");?></h4>
                </div>
                <div class="modal-body">
                    <p><?php if($settings->share == "on") echo language_filter("share_off_q"); else language_filter("share_on_q");?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Close")?></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="change_share_system_status();"><?php language_filter("Apply");?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="download" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="download_myModalLabel"><?php language_filter("Settings");?></h4>
                </div>
                <div class="modal-body">
                    <p><?php if(@$settings->download_link == "on") echo language_filter("download_off_q"); else language_filter("download_on_q");?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Close")?></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="change_download_system_status();"><?php language_filter("Apply");?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="system_sharing" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="system_share_myModalLabel"><?php language_filter("Settings");?></h4>
                </div>
                <div class="modal-body">
                    <p><?php if(@$settings->system_share == "on") echo language_filter("system_share_off_q"); else language_filter("system_share_on_q");?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Close")?></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="change_system_share_status();"><?php language_filter("Apply");?></button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="ticket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="ticket_myModalLabel"><?php language_filter("Settings");?></h4>
                </div>
                <div class="modal-body">
                    <p><?php if($settings->ticket == "on") echo language_filter("ticket_off_q"); else language_filter("ticket_on_q");?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Close")?></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="change_ticket_system_status();"><?php language_filter("Apply");?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-danger">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">

                            <form action="javascript:;" method="post" name="AddNewExt_File">
                                <div class="form-group">
                                    <label for="AddNewExtFile" style="margin-left: 15px"><?php language_filter("Add new extension that you want")?></label>
                                    <div class="col-xs-9">
                                        <input class="form-control" id="AddNewExtFile" type="text" name="AddNewExtFile" onchange="filemanager_ext_add = this.value;">
                                    </div>
                                    <div class="col-xs-3">
                                        <button class="btn btn-default" type="button" onclick="if(filemanager_ext_add == '') alert('<?php language_filter("Please write new extension.", false, true)?>'); else add_ext(filemanager_ext_add, 'filemanager');"><?php language_filter("Add");?></button>
                                    </div>
                                </div>
                            </form>
                            <div style="margin-bottom: 50px"></div>
                            <thead>
                            <tr>
                                <th><?php language_filter("Allowed Extensions On File Manager")?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
        <?php
                            for($i = 0; $i < count($allow_ext_filemanager); $i++)
                            {
        ?>
                                <tr>
                                    <td><?php echo $allow_ext_filemanager[$i];?></td>
                                    <td style="text-align: center;"><button class="btn btn-mini btn-danger" type="button" onclick="remove_ext('<?php echo $allow_ext_filemanager[$i];?>', 'filemanager', 'alert');"><?php language_filter("Remove")?></button></td>
                                </tr>
        <?php
                            }
        ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-danger">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">

                            <form action="javascript:;" method="post" name="AddNewExt_up">
                                <div class="form-group">
                                    <label for="AddNewExtUpload" style="margin-left: 15px"><?php language_filter("Add new extension that you want");?></label>
                                    <div class="col-xs-9">
                                        <input class="form-control" id="AddNewExtUpload" type="text" name="AddNewExtUp" onchange="uploader_ext_add = this.value;">
                                    </div>
                                    <div class="col-xs-3">
                                        <button class="btn btn-default" type="button" onclick="if(uploader_ext_add == '') alert('<?php language_filter("Please write new extension.", false, true)?>'); else add_ext(uploader_ext_add, 'uploader');"><?php language_filter("Add")?></button>
                                    </div>
                                </div>
                            </form>
                            <div style="margin-bottom: 50px"></div>
                            <thead>
                            <tr>
                                <th><?php language_filter("Allowed Extensions On Uploader");?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
        <?php
                                for($i = 0; $i < count($allow_ext_uploader); $i++)
                                {
        ?>
                                    <tr>
                                        <td><?php echo $allow_ext_uploader[$i];?></td>
                                        <td style="text-align: center;"><button class="btn btn-mini btn-danger" type="button" onclick="remove_ext('<?php echo $allow_ext_uploader[$i];?>', 'uploader', 'alert');"><?php language_filter("Remove")?></button></td>
                                    </tr>
        <?php
                                }
        ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        userPermission = new Array(<?php if(!empty($register_settings->permissions)) {for($i = 0; $i < count($register_settings->permissions); $i++){if($i == (count($register_settings->permissions) - 1)) echo "'".$register_settings->permissions[$i]."'"; else echo "'".$register_settings->permissions[$i]."', "; } }?>); // send
            <?php
            if(count($register_settings->permissions) == 1)
            {
                echo 'userPermission = new Array(); userPermission.push(\''.$register_settings->permissions[0].'\');';
            }
            ?>
        userExtensions = new Array(<?php if(!empty($register_settings->allow_ext)) {for($i = 0; $i < count($register_settings->allow_ext); $i++){if($i == (count($register_settings->allow_ext) - 1)) echo "'".$register_settings->allow_ext[$i]."'"; else echo "'".$register_settings->allow_ext[$i]."', "; } }?>); // send
            <?php
            if(count($register_settings->allow_ext) == 1)
            {
                echo 'userExtensions = new Array(); userExtensions.push(\''.$register_settings->allow_ext[0].'\');';
            }
            ?>
        userUploader = new Array(<?php if(!empty($register_settings->allow_upload)) {for($i = 0; $i < count($register_settings->allow_upload); $i++){if($i == (count($register_settings->allow_upload) - 1)) echo "'".$register_settings->allow_upload[$i]."'"; else echo "'".$register_settings->allow_upload[$i]."', "; } }?>); // send
            <?php
            if(count($register_settings->allow_upload) == 1)
            {
                echo 'userUploader = new Array(); userUploader.push(\''.$register_settings->allow_upload[0].'\');';
            }
            ?>
        selected_perms = userPermission;
        uploader_ext_add = "";
        filemanager_ext_add = "";
        permissions = new Array(
                "edit_profile",
                "create_folder",
                "rename_folder",
                "copy_folders",
                "move_folders",
                "remove_folders",
                "zip_folders",
                "upload_folders",
                "backup_folders",
                "edit_files",
                "edit_img",
                "unzip_files"
        );
        all_uploader = new Array(<?php for($i = 0; $i < count($allow_ext_uploader); $i++){if($i == (count($allow_ext_uploader) - 1)) echo "'".$allow_ext_uploader[$i]."'"; else echo "'".$allow_ext_uploader[$i]."', "; }?>);
        all_filemanager = new Array(<?php for($i = 0; $i < count($allow_ext_filemanager); $i++){if($i == (count($allow_ext_filemanager) - 1)) echo "'".$allow_ext_filemanager[$i]."'"; else echo "'".$allow_ext_filemanager[$i]."', "; }?>);
        $('#siteMap').on('hidden.bs.modal', function (e) {
            $("#user_registration").modal( 'show' );
        });
        function change_user_registration_setting()
        {
            var upload_limitation = $("#upload_limitation").val();
            var status = "<?php echo $settings->register;?>";
            var change_reg_status = "false";
            var new_status = "off";
            var users_dir = $("#user_dir").val();
            var user_limitation = $("#limitation").val();
            if(user_limitation == "" || user_limitation == null || user_limitation <= 0)
            {
                alert("<?php language_filter("Please write limitation size.", false, true)?>");
                return false;
            }
            if(users_dir == "" || users_dir == null)
            {
                alert("<?php language_filter("Please select a directory for user.", false, true)?>");
                return false;
            }
            if(document.getElementById("registration_on").checked == true)
            {
                new_status = "on";
            }
            if(new_status != status)
            {
                if(status == "on")
                {
                    status = "off";
                }
                else
                {
                    status = "on";
                }
                change_reg_status = "true";
            }

            if(userExtensions.length <= 0)
            {
                alert("<?php language_filter("Please select file manager extensions for user.", false, true)?>");
                return false;
            }
            if(in_array(userPermission, "upload_folders"))
            {
                if(userUploader.length <= 0)
                {
                    alert("<?php language_filter("Please select uploader extensions for user.", false, true)?>");
                    return false;
                }

                if(upload_limitation == '' || upload_limitation == null || upload_limitation <= 0)
                {
                    alert("<?php language_filter("Please write upload limitation size", false, true)?>");
                    return false;
                }
            }
            $("#user_registration").modal('hide');
            show_preloader();
            setTimeout(function(){
                $.post("ajax_show_setting.php",
                {
                    showSetting:<?php echo $core->admin_id;?>,
                    change_status:"user_registration",
                    change_reg_status:change_reg_status,
                    status:status,
                    upload_limitation:upload_limitation,
                    userExtensions:userExtensions,
                    userPermission:userPermission,
                    userUploader:userUploader,
                    userDir:users_dir,
                    user_limitation:user_limitation
                },
                function(data,status)
                {
                    if(status == "success")
                    {
                        $('#content_show').html('');
                        $('.bar').addClass('bar-success');
                        $('li').removeClass();
                        $('#setting').addClass('active');
                        $('#content_show').fadeIn(1000);
                        $('#content_show').html(data);
                    }
                    else
                    {
                        $('.bar').width("30%");
                        $('.bar').width("50%");
                        $('.bar').width("80%");
                        $('.bar').width("100%");
                        $('.bar').addClass('bar-danger');
                        $('.bar').html("<center>Can not load page, click to exit. SERVER STATUS: "+status+"</center>");
                    }

                });
            }, 1000);

        }

        function showTree()
        {
            $("#user_registration").modal( 'hide' );
            $('#container_id').fileTree({
                root: '<?php echo ROOT_DIR_PATH?>',
                script: 'jqueryFileTree.php',
                expandSpeed: 500,
                collapseSpeed: 500,
                multiFolder: false
            }, function(file) {
                if( file == '..') file = "<?php echo ROOT_DIR_PATH;?>";
                $("#user_dir").val(file);
                $("#folder_selected_path").html(file);
            });
            $("#siteMap").modal("show");
        }

        function set_user_permissions(method)
        {
            if(method == "public")
            {
                userPermission = new Array();
            }
            else if(method == "full")
            {
                userPermission = permissions;
            }
            else
            {
                if(!in_array(selected_perms, method))
                {
                    selected_perms.push(method);
                }
                else
                {
                    removeItem(selected_perms, method);
                }
                userPermission = selected_perms;
            }
        }

        function set_filemanager_ext(ext)
        {
            if(!in_array(userExtensions, ext))
            {
                userExtensions.push(ext);
            }
            else
            {
                removeItem(userExtensions, ext);
            }
        }
        function set_uploader_ext(ext)
        {
            if(!in_array(userUploader, ext))
            {
                userUploader.push(ext);
            }
            else
            {
                removeItem(userUploader, ext);
            }
        }

        function select_all_ext(method)
        {
            if(method == "uploader_")
            {
                var select_method = $("#select_uploader").html();
                if(select_method == "<?php language_filter("Select All")?>")
                {
                    for(var i in all_uploader)
                    {
                        document.getElementById(method+all_uploader[i]).checked = true;
                    }
                    userUploader = all_uploader;
                    $("#select_uploader").html("<?php language_filter("Unselect All")?>");
                }
                else
                {
                    for(var i in all_uploader)
                    {
                        document.getElementById(method+all_uploader[i]).checked = false;
                    }
                    userUploader = new Array();
                    $("#select_uploader").html("<?php language_filter("Select All")?>");
                }
            }
            else
            {
                var select_method = $("#select_manager").html();
                if(select_method == "<?php language_filter("Select All")?>")
                {
                    for(var i in all_filemanager)
                    {
                        document.getElementById(method+all_filemanager[i]).checked = true;
                    }
                    userExtensions = all_filemanager;
                    $("#select_manager").html("<?php language_filter("Unselect All")?>");
                }
                else
                {
                    for(var i in all_filemanager)
                    {
                        document.getElementById(method+all_filemanager[i]).checked = false;
                    }
                    userExtensions = new Array();
                    $("#select_manager").html("<?php language_filter("Select All")?>");
                }
            }
        }

        function change_user_notification_status()
        {
            var status = "<?php echo $settings->user_notification;?>";
            if(status == "on")
            {
                status = "off";
            }
            else
            {
                status = "on";
            }
            show_preloader();
            setTimeout(function(){
                $.post("ajax_show_setting.php",
                {
                    showSetting:<?php echo $core->admin_id;?>,
                    change_status:"user_notification",
                    status:status
                },
                function(data,status)
                {
                    if(status == "success")
                    {
                        $('#content_show').html('');
                        $('.bar').addClass('bar-success');
                        $('li').removeClass();
                        $('#setting').addClass('active');
                        $('#content_show').fadeIn(1000);
                        $('#content_show').html(data);
                    }
                    else
                    {
                        $('.bar').width("30%");
                        $('.bar').width("50%");
                        $('.bar').width("80%");
                        $('.bar').width("100%");
                        $('.bar').addClass('bar-danger');
                        $('.bar').html("<center>Can not load page, click to exit. SERVER STATUS: "+status+"</center>");
                    }

                });
            }, 1000);
        }

        function change_admin_notification_status()
        {
            var status = "<?php echo $settings->admin_notification;?>";
            if(status == "on")
            {
                status = "off";
            }
            else
            {
                status = "on";
            }
            show_preloader();
            setTimeout(function(){
                $.post("ajax_show_setting.php",
                {
                    showSetting:<?php echo $core->admin_id;?>,
                    change_status:"admin_notification",
                    status:status
                },
                function(data,status)
                {
                    if(status == "success")
                    {
                        $('#content_show').html('');
                        $('.bar').addClass('bar-success');
                        $('li').removeClass();
                        $('#setting').addClass('active');
                        $('#content_show').fadeIn(1000);
                        $('#content_show').html(data);
                    }
                    else
                    {
                        $('.bar').width("30%");
                        $('.bar').width("50%");
                        $('.bar').width("80%");
                        $('.bar').width("100%");
                        $('.bar').addClass('bar-danger');
                        $('.bar').html("<center>Can not load page, click to exit. SERVER STATUS: "+status+"</center>");
                    }

                });
            }, 1000);
        }

        function change_share_system_status()
        {
            var status = "<?php echo $settings->share;?>";
            if(status == "on")
            {
                status = "off";
            }
            else
            {
                status = "on";
            }
            show_preloader();
            setTimeout(function(){
                $.post("ajax_show_setting.php",
                {
                    showSetting:<?php echo $core->admin_id;?>,
                    change_status:"share",
                    status:status
                },
                function(data,status)
                {
                    if(status == "success")
                    {
                        $('#content_show').html('');
                        $('.bar').addClass('bar-success');
                        $('li').removeClass();
                        $('#setting').addClass('active');
                        $('#content_show').fadeIn(1000);
                        $('#content_show').html(data);
                    }
                    else
                    {
                        $('.bar').width("30%");
                        $('.bar').width("50%");
                        $('.bar').width("80%");
                        $('.bar').width("100%");
                        $('.bar').addClass('bar-danger');
                        $('.bar').html("<center>Can not load page, click to exit. SERVER STATUS: "+status+"</center>");
                    }

                });
            }, 1000);
        }

        function change_download_system_status()
        {
            var status = "<?php echo @$settings->download_link;?>";
            if(status == "on")
            {
                status = "off";
            }
            else
            {
                status = "on";
            }
            show_preloader();
            setTimeout(function(){
                $.post("ajax_show_setting.php",
                {
                    showSetting:<?php echo $core->admin_id;?>,
                    change_status:"download_link",
                    status:status
                },
                function(data,status)
                {
                    if(status == "success")
                    {
                        $('#content_show').html('');
                        $('.bar').addClass('bar-success');
                        $('li').removeClass();
                        $('#setting').addClass('active');
                        $('#content_show').fadeIn(1000);
                        $('#content_show').html(data);
                    }
                    else
                    {
                        $('.bar').width("30%");
                        $('.bar').width("50%");
                        $('.bar').width("80%");
                        $('.bar').width("100%");
                        $('.bar').addClass('bar-danger');
                        $('.bar').html("<center>Can not load page, click to exit. SERVER STATUS: "+status+"</center>");
                    }

                });
            }, 1000);
        }

        function change_system_share_status() // system share
        {
            var status = "<?php echo @$settings->system_share;?>";
            if(status == "on")
            {
                status = "off";
            }
            else
            {
                status = "on";
            }
            show_preloader();
            setTimeout(function(){
                $.post("ajax_show_setting.php",
                {
                    showSetting:<?php echo $core->admin_id;?>,
                    change_status:"system_share",
                    status:status
                },
                function(data,status)
                {
                    if(status == "success")
                    {
                        $('#content_show').html('');
                        $('.bar').addClass('bar-success');
                        $('li').removeClass();
                        $('#setting').addClass('active');
                        $('#content_show').fadeIn(1000);
                        $('#content_show').html(data);
                    }
                    else
                    {
                        $('.bar').width("30%");
                        $('.bar').width("50%");
                        $('.bar').width("80%");
                        $('.bar').width("100%");
                        $('.bar').addClass('bar-danger');
                        $('.bar').html("<center>Can not load page, click to exit. SERVER STATUS: "+status+"</center>");
                    }

                });
            }, 1000);
        }

        function change_ticket_system_status()
        {
            var status = "<?php echo $settings->ticket;?>";
            if(status == "on")
            {
                status = "off";
            }
            else
            {
                status = "on";
            }
            show_preloader();
            setTimeout(function(){
                $.post("ajax_show_setting.php",
                {
                    showSetting:<?php echo $core->admin_id;?>,
                    change_status:"ticket",
                    status:status
                },
                function(data,status)
                {
                    if(status == "success")
                    {
                        $('#content_show').html('');
                        $('.bar').addClass('bar-success');
                        $('li').removeClass();
                        $('#setting').addClass('active');
                        $('#content_show').fadeIn(1000);
                        $('#content_show').html(data);
                    }
                    else
                    {
                        $('.bar').width("30%");
                        $('.bar').width("50%");
                        $('.bar').width("80%");
                        $('.bar').width("100%");
                        $('.bar').addClass('bar-danger');
                        $('.bar').html("<center>Can not load page, click to exit. SERVER STATUS: "+status+"</center>");
                    }

                });
            }, 1000);
        }

        function remove_ext(name, method, time)
        {
            var from = "Uploader";
            if(method == "filemanager")
            {
                from = "Filemanager";
            }
            if(time == "alert")
            {
                $("#myModalLabel_ext").html('<?php language_filter("Remove extension", false, true);?>');
                $("#modal_body_ext").html('<?php language_filter("Do you want to remove this extension", false, true);?>');
                document.getElementById("remove_btn_ext").setAttribute("onclick", "remove_ext('"+name+"', '"+method+"', 'move')");
                $("#remove_extension").modal("show");
            }
            else
            {
                $("#remove_extension").modal("hide");
                show_preloader();
                setTimeout(function(){
                    $.post("ajax_show_setting.php",
                    {
                        showSetting:<?php echo $core->admin_id;?>,
                        remove_ext:"Yes",
                        method_ext:method,
                        ext_name:name
                    },
                    function(data,status)
                    {
                        if(status == "success")
                        {
                            $('#content_show').html('');
                            $('.bar').addClass('bar-success');
                            $('li').removeClass();
                            $('#setting').addClass('active');
                            $('#content_show').fadeIn(1000);
                            $('#content_show').html(data);
                        }
                        else
                        {
                            $('.bar').width("30%");
                            $('.bar').width("50%");
                            $('.bar').width("80%");
                            $('.bar').width("100%");
                            $('.bar').addClass('bar-danger');
                            $('.bar').html("<center>Can not load page, click to exit. SERVER STATUS: "+status+"</center>");
                        }

                    });
                }, 1000);
            }
        }

        function add_ext(name, method)
        {
            filemanager_ext_add = "";
            uploader_ext_add = "";
            show_preloader();
            setTimeout(function(){
                $.post("ajax_show_setting.php",
                {
                    showSetting:<?php echo $core->admin_id;?>,
                    add_ext:"Yes",
                    method_ext:method,
                    ext_name:name
                },
                function(data,status)
                {
                    if(status == "success")
                    {
                        $('#content_show').html('');
                        $('.bar').addClass('bar-success');
                        $('li').removeClass();
                        $('#setting').addClass('active');
                        $('#content_show').fadeIn(1000);
                        $('#content_show').html(data);
                    }
                    else
                    {
                        $('.bar').width("30%");
                        $('.bar').width("50%");
                        $('.bar').width("80%");
                        $('.bar').width("100%");
                        $('.bar').addClass('bar-danger');
                        $('.bar').html("<center>Can not load page, click to exit. SERVER STATUS: "+status+"</center>");
                    }

                });
            }, 1000);

        }

        $('input').bind('keypress', function (event) {
            var regex = new RegExp("^[a-z0-9]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
                event.preventDefault();
                return false;
            }
        });

        function show_status_ext(msg, status)
        {
            if(msg != "")
            {
                var color = "red";
                if(status == 1)
                {
                    color = "green";
                }
                show_errors_on_nav(msg, color);
            }
        }

        function removeItem(array, item)
        {
            for(var i in array)
            {
                if(array[i]==item)
                {
                    array.splice(i,1);
                    break;
                }
            }
        }
        function in_array(array, id)
        {
            for(var i=0;i<array.length;i++)
            {
                if(array[i] === id)
                {
                    return true;
                }
            }
            return false;
        }
        here = "<?php echo ROOT_DIR_PATH;?>";
    </script>

    <div class="modal fade" id="remove_extension" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel_ext">Modal title</h4>
                </div>
                <div class="modal-body">
                    <p id="modal_body_ext"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Close")?></button>
                    <button type="button" class="btn btn-danger" id="remove_btn_ext"><?php language_filter("Remove")?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="siteMap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p id="container_id"></p>
                </div>
                <div class="modal-footer">
                    <span class="pull-left" id="folder_selected_path"></span>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel")?></button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?php language_filter("Select")?></button>
                </div>
            </div>
        </div>
    </div>

<?php
                echo "<script>show_status_ext('".addslashes($result)."', ".$status.");</script>";
            }
        }
    }
}
?>