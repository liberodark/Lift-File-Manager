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
        if (isset($_POST["editUser"]))
        {
            $result = "";
            $status = 0;
            $core->adminInfo();
            if ($_POST["editUser"] == $core->admin_id)
            {
                if(isset($_POST["edit_username"]) and isset($_POST["edit_email"]))
                {
                    $userId = $_POST["userId"];
                    $username = $_POST["edit_username"];
                    $email = $_POST["edit_email"];
                    $firstname = $_POST["edit_firstname"];
                    $lastname = $_POST["edit_lastname"];
                    $password = $_POST["edit_password"];
                    $send_pass = $_POST["edit_send_pass"];
                    $user_dir = $_POST["edit_user_dir"];
                    $limitation = $_POST["edit_limitation"];
                    $upload_limitation = $_POST["edit_upload_limitation"];
                    $deny_files = $_POST["edit_deny_files"];
                    $extra_dir = $_POST["edit_user_extra_dir"];
                    $user_perm = array();
                    if(isset($_POST["edit_user_perm"]))
                        $user_perm = $_POST["edit_user_perm"];
                    $user_ext = $_POST["edit_user_ext"];
                    $user_up = array();
                    if(isset($_POST["edit_user_up"]))
                        $user_up = $_POST["edit_user_up"];
                    $edit = $core->edit_user($username, $email, $firstname, $lastname, $password, $send_pass, $user_dir, $limitation, $upload_limitation, $deny_files, $extra_dir, $user_perm, $user_ext, $user_up, $userId);
                    if($edit == true)
                    {
                        $status = 1;
                        $result = language_filter("User has been edited", true);
                    }
                    else if($edit == null)
                    {
                        $status = 1;
                        $result = language_filter("User has been edited but can not send email to user", true);
                    }
                    else
                    {
                        $result = language_filter("User has not been edited", true);
                    }
                }
                $backPage = $_POST["backPage"];
                $userId = $_POST["userId"];
                $allow_ext_filemanager = $option->get_option("allow_extensions");
                $allow_ext_uploader = $option->get_option("allow_uploads");
                $user = $core->get_user($userId);
?>
                <script src="filemanager_js/jqueryFileTree.js"></script>
                <div class="row">
                    <div class="col-md-4" style="">
                    <div class="panel panel-danger">
                        <div class="panel-body">
                        <p class="text-success"><?php language_filter("Account Setting")?></p>
                        <form action="javascript:;" method="post" name="add_user_form">

                            <div class="form-group">
                                <label for="user_edit_firstname"><?php language_filter("First Name")?></label>
                                <input type="text" id="user_edit_firstname" name="firstname" class="form-control" required="required" value="<?php echo $user["firstname"];?>">
                            </div>

                            <div class="form-group">
                                <label for="user_edit_lastname"><?php language_filter("Last Name")?></label>
                                <input type="text" id="user_edit_lastname" name="lastname" class="form-control" required="required" value="<?php echo $user["lastname"];?>">
                            </div>

                            <div class="form-group">
                                <label for="user_edit_email"><?php language_filter("Email Address")?></label>
                                <input type="email" id="user_edit_email" name="email" class="form-control" required="required" value="<?php echo $user["email"];?>">
                            </div>

                            <div class="form-group">
                                <label for="user_edit_username"><?php language_filter("Username")?></label>
                                <input type="text" id="user_edit_username" name="username" class="form-control" required="required" value="<?php echo $user["username"];?>">
                            </div>

                            <div class="form-group">
                                <label><?php language_filter("New Password")?><small><?php language_filter("(Optional)")?></small></label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="send_pass" value="off" onclick="if(this.checked == true) this.value = 'send'; else this.value = 'off';"> <small><?php language_filter("Send username / password for user via email:")?></small>
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="user_dir"><?php language_filter("Select User Folder")?> <button type="button" class="btn btn-default" onclick="showTree( false );"><?php language_filter("Site Map")?></button> <button type="button" class="btn btn-success btn-xs" onclick="showTree( true )"><?php language_filter( "extraFolderBtn" )?></button></label>
                                <input type="text" name="user_dir" class="form-control" id="user_dir" required="required" value="<?php echo $user["dir_path"];?>">
                            </div>

                            <div id="extra_dir_box">

                            </div>

                            <div class="form-group">
                                <label for="user_edit_limit"><?php language_filter("Set Size Limitation (MB)")?></label>
                                <input type="text" id="user_edit_limit" name="limitation" id="limitation" class="form-control" required="required" value="<?php echo $user["limitation"];?>">
                            </div>

                            <div class="form-group">
                                <label for="user_edit_upload"><?php language_filter("Set Upload Size Limitation (MB)")?></label>
                                <input type="text" id="user_edit_upload" name="upload_limitation" class="form-control" value="<?php echo $user["upload_limitation"];?>">
                            </div>

                            <div class="form-group">
                                <label><?php language_filter("denyFolders")?> <button class="btn btn-default" type="button" onclick="showDenyTree();"><?php language_filter("Site Map")?></button></label>
                            </div>

                            <div id="extra_dir_deny">

                            </div>

                            <div class="form-group">
                                <label><?php language_filter("Customize User Permissions")?></label>
                                <a href="#Permissions" role="button" class="btn btn-default" data-toggle="modal" style="text-decoration: none;"><?php language_filter("Select Permission")?></a>
                            </div>
                        </div>
                    </div>
                    </div>

                    <div class="col-md-4" style="">
                    <div class="panel panel-danger">
                        <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                            <p class="text-success"><?php language_filter("Allowed extensions for user in file manager")?></p>
                            <tr>
                                <th style="text-align: center"><?php language_filter("Format")?></th>
                                <th style="text-align: center">
                                    <button type="button" class="btn btn-default btn-xs select-btn-filemanager" id="select_manager" onclick="select_all_ext('manager_')"><?php language_filter("Unselect All")?></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                for($i = 0; $i < count($allow_ext_filemanager); $i++)
                                {
                                    ?>
                                <tr>
                                    <td style="text-align: center"><?php echo $allow_ext_filemanager[$i];?></td>
                                    <td style="text-align: center">
                                        <input type="checkbox" id="manager_<?php echo $allow_ext_filemanager[$i];?>" onclick="set_filemanager_ext('<?php echo $allow_ext_filemanager[$i];?>')" <?php if(in_array($allow_ext_filemanager[$i], $user["filemanager_ext"])) echo 'checked="checked"';?>>
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

                    <div class="col-md-4"  style="">
                    <div class="panel panel-danger">
                        <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                            <p class="text-success"><?php language_filter("Allowed extensions for user in uploader")?></p>
                            <tr>
                                <th style="text-align: center"><?php language_filter("Format")?></th>
                                <th style="text-align: center">
                                    <button type="button" class="btn btn-default btn-xs select-btn-filemanager" id="select_uploader" onclick="select_all_ext('uploader_')"><?php language_filter("Unselect All")?></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                for($i = 0; $i < count($allow_ext_uploader); $i++)
                                {
                                    ?>
                                <tr>
                                    <td style="text-align: center"><?php echo $allow_ext_uploader[$i];?></td>
                                    <td style="text-align: center">
                                        <input type="checkbox" id="uploader_<?php echo $allow_ext_uploader[$i];?>" onclick="set_uploader_ext('<?php echo $allow_ext_uploader[$i];?>');" <?php if(in_array($allow_ext_uploader[$i], $user["uploader_ext"])) echo 'checked="checked"';?>>
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
                <div class="row">
                    <div class="col-md-12">
                            <hr>
                            <input type="button" value="<?php language_filter("Save Changes")?>" class="btn btn-primary pull-right" onclick="check_and_send_user_info();">
                            <button class="btn btn-default pull-right" onclick="back_to_users()" style="margin-right: 20px;"><?php language_filter("Back")?></button>
                        </form>
                    </div>
                </div>

                <div class="modal fade" id="Permissions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel"><?php language_filter("Choose User Permissions")?></h4>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table">

                                        <tr>
                                            <td><?php language_filter("Edit Profile")?></td>
                                            <td>
                                                <input type="checkbox" id="edit_profile" onclick="set_user_permissions(this.id)" <?php if(in_array("edit_profile", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><?php language_filter("Create Folder")?></td>
                                            <td>
                                                <input type="checkbox" id="create_folder" onclick="set_user_permissions(this.id)" <?php if(in_array("create_folder", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><?php language_filter("Rename Files And Folders")?></td>
                                            <td>
                                                <input type="checkbox" id="rename_folder" onclick="set_user_permissions(this.id)" <?php if(in_array("rename_folder", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><?php language_filter("Copy Files And Folders")?></td>
                                            <td>
                                                <input type="checkbox" id="copy_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("copy_folders", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><?php language_filter("Move Files And Folders")?></td>
                                            <td>
                                                <input type="checkbox" id="move_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("move_folders", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><?php language_filter("Remove Files And Folders")?></td>
                                            <td>
                                                <input type="checkbox" id="remove_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("remove_folders", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><?php language_filter("Create Zip From Files And Folders")?></td>
                                            <td>
                                                <input type="checkbox" id="zip_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("zip_folders", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><?php language_filter("Upload Files")?></td>
                                            <td>
                                                <input type="checkbox" id="upload_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("upload_folders", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><?php language_filter("Create Backup")?></td>
                                            <td>
                                                <input type="checkbox" id="backup_folders" onclick="set_user_permissions(this.id)" <?php if(in_array("backup_folders", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><?php language_filter("Edit Text Files")?></td>
                                            <td>
                                                <input type="checkbox" id="edit_files" onclick="set_user_permissions(this.id)" <?php if(in_array("edit_files", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                        <!--<tr>
                                            <td><?php /*language_filter("Edit Images")*/?></td>
                                            <td>
                                                <input type="checkbox" id="edit_img" onclick="set_user_permissions(this.id)" <?php /*if(in_array("edit_img", $user["permissions"])) echo 'checked="checked"';*/?>>
                                            </td>
                                        </tr>-->

                                        <tr>
                                            <td><?php language_filter("Unzip Zip Files")?></td>
                                            <td>
                                                <input type="checkbox" id="unzip_files" onclick="set_user_permissions(this.id)" <?php if(in_array("unzip_files", $user["permissions"])) echo 'checked="checked"';?>>
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-default" data-dismiss="modal" onclick="reset_perms()"><?php language_filter("Reset")?></button>
                                <button class="btn btn-primary" data-dismiss="modal"><?php language_filter("Apply")?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="siteMap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel"><?php language_filter("Site Map")?></h4>
                            </div>
                            <div class="modal-body">
                                <p id="container_id"></p>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" id="select_dir_btn" data-dismiss="modal" onclick=""><?php language_filter("Select")?></button>
                            </div>
                        </div>
                    </div>
                </div>

            <script>
            userPermission = new Array(<?php if(!empty($user["permissions"])) {for($i = 0; $i < count($user["permissions"]); $i++){if($i == (count($user["permissions"]) - 1)) echo "'".$user["permissions"][$i]."'"; else echo "'".$user["permissions"][$i]."', "; } }?>); // send
            <?php
                if(count($user["permissions"]) == 1)
                {
                    echo 'userPermission = new Array(); userPermission.push(\''.$user["permissions"][0].'\');';
                }
            ?>
            userExtensions = new Array(<?php if(!empty($user["filemanager_ext"])) {for($i = 0; $i < count($user["filemanager_ext"]); $i++){if($i == (count($user["filemanager_ext"]) - 1)) echo "'".$user["filemanager_ext"][$i]."'"; else echo "'".$user["filemanager_ext"][$i]."', "; } }?>); // send
            <?php
            if(count($user["filemanager_ext"]) == 1)
            {
                echo 'userExtensions = new Array(); userExtensions.push(\''.$user["filemanager_ext"][0].'\');';
            }
            ?>
            userUploader = new Array(<?php if(!empty($user["uploader_ext"])) {for($i = 0; $i < count($user["uploader_ext"]); $i++){if($i == (count($user["uploader_ext"]) - 1)) echo "'".$user["uploader_ext"][$i]."'"; else echo "'".$user["uploader_ext"][$i]."', "; } }?>); // send
            <?php
            if(count($user["uploader_ext"]) == 1)
            {
                echo 'userUploader = new Array(); userUploader.push(\''.$user["uploader_ext"][0].'\');';
            }
            ?>
            selected_perms = userPermission;

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
                    "unzip_files"
            );
            /* "edit_img", */
            all_uploader = new Array(<?php for($i = 0; $i < count($allow_ext_uploader); $i++){if($i == (count($allow_ext_uploader) - 1)) echo "'".$allow_ext_uploader[$i]."'"; else echo "'".$allow_ext_uploader[$i]."', "; }?>);
            all_filemanager = new Array(<?php for($i = 0; $i < count($allow_ext_filemanager); $i++){if($i == (count($allow_ext_filemanager) - 1)) echo "'".$allow_ext_filemanager[$i]."'"; else echo "'".$allow_ext_filemanager[$i]."', "; }?>);

            var lastSelectedFolder = "";
            var userExtraDirs = new Array();
            var lastSelectedDeny = "";
            var userDenyFiles = new Array();

            function auto_add_deny( path ) {
                var id = "";
                var html = "";
                path = path.split(",");
                for( var i in path ) {
                    id = dir_to_id( path[i], true );
                    userDenyFiles.push( path[i] );
                    html = '<div id="'+id+'"><div class="col-md-10" style="padding: 0"><input type="text" class="form-control" value="'+path[i]+'" ></div><div class="col-md-2" style="padding-top: 8px;"> <i class="glyphicon glyphicon-remove" style="cursor: pointer;" onclick="removeDeny( \''+path[i]+'\', \''+id+'\' )"></i></div><div><br /><br />';
                    $("#extra_dir_deny").append( html );
                }
            }

            function auto_add_extra( path ) {
                var id = "";
                var html = "";
                path = path.split(",");
                for( var i in path ) {
                    id = dir_to_id( path[i], false );
                    userExtraDirs.push( path[i] );
                    html = '<div id="'+id+'"><div class="col-md-10" style="padding: 0"><input type="text" class="form-control" value="'+path[i]+'" ></div><div class="col-md-2" style="padding-top: 8px;"> <i class="glyphicon glyphicon-remove" style="cursor: pointer;" onclick="removeExtraDir( \''+path[i]+'\', \''+id+'\' )"></i></div><div><br /><br />';
                    $("#extra_dir_box").append( html );
                }
            }

            <?php
            if( !empty( $user["deny_folders"] ) ) {
                echo "auto_add_deny(\"".$core->filter_txt( implode(",", $user["deny_folders"]), true )."\");";
            }
            if( !empty( $user["extra_dirs"] ) ) {
                echo "auto_add_extra(\"".$core->filter_txt( implode(",", $user["extra_dirs"]) )."\");";
            }
            ?>

            function back_to_users()
            {
                $('#preloader').modal('show');
                $.post("ajax_show_users.php",
                {
                    showUser:'<?php echo $core->admin_id;?>',
                    page:'<?php echo $backPage?>'
                },
                function(data,status)
                {
                    if(status == "success")
                    {
                        $('#content_show').html('');
                        $('.bar').addClass('bar-success');
                        $('li').removeClass();
                        $('#users').addClass('active');
                        $("#preloader").modal("hide");
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
            }

            function showDenyTree()
            {
                $("#select_dir_btn").attr( "onclick", "addDeny()" );
                $('#container_id').fileTree({
                    root: '<?php echo ROOT_DIR_PATH;?>',
                    script: 'jqueryFileTree.php?showFile=true',
                    expandSpeed: 500,
                    collapseSpeed: 500,
                    multiFolder: false
                }, function(file) {
                    if(file != "..") {
                        if( !in_array( userDenyFiles, file ) ) {
                            lastSelectedDeny = file;
                        }
                    }
                });
                $("#siteMap").modal("show");
            }

            function addDeny()
            {
                if( lastSelectedDeny == "" ) {
                    return false;
                }
                else {
                    var id = dir_to_id( lastSelectedDeny, true );
                    userDenyFiles.push( lastSelectedDeny );
                    var html = '<div id="'+id+'"><div class="col-md-10" style="padding: 0"><input type="text" class="form-control" value="'+lastSelectedDeny+'" ></div><div class="col-md-2" style="padding-top: 8px;"> <i class="glyphicon glyphicon-remove" style="cursor: pointer;" onclick="removeDeny( \''+lastSelectedDeny+'\', \''+id+'\' )"></i></div><div><br /><br />';
                    $("#extra_dir_deny").append( html );
                    lastSelectedDeny = "";
                }
            }

            function removeDeny( deny, id )
            {
                removeItem( userDenyFiles, deny );
                $("#"+id).remove();
            }

            function showTree( extra )
            {
                if( extra ) $("#select_dir_btn").attr( "onclick", "addExtraDir()" );
                else $("#select_dir_btn").attr( "onclick", "" );
                $('#container_id').fileTree({
                    root: '<?php echo ROOT_DIR_PATH;?>',
                    script: 'jqueryFileTree.php',
                    expandSpeed: 500,
                    collapseSpeed: 500,
                    multiFolder: false
                }, function(file) {
                    if(file == "..") file = "<?php echo ROOT_DIR_PATH;?>";
                    if( extra ) {
                        if( file != $("#user_dir").val() && !in_array( userExtraDirs, file ) ) {
                            lastSelectedFolder = file;
                        }
                        else {
                            lastSelectedFolder = "";
                        }
                    }
                    else {
                        if( !in_array( userExtraDirs, file ) ) {
                            $("#user_dir").val(file);
                        }
                    }
                });
                $("#siteMap").modal("show");
            }

            function addExtraDir()
            {
                if( lastSelectedFolder == "" ) {
                    alert( "<?php language_filter("extraDirError", false, true);?>" );
                    return false;
                }
                else {
                    var id = dir_to_id( lastSelectedFolder, false );
                    userExtraDirs.push( lastSelectedFolder );
                    var html = '<div id="'+id+'"><div class="col-md-10" style="padding: 0"><input type="text" class="form-control" value="'+lastSelectedFolder+'" ></div><div class="col-md-2" style="padding-top: 8px;"> <i class="glyphicon glyphicon-remove" style="cursor: pointer;" onclick="removeExtraDir( \''+lastSelectedFolder+'\', \''+id+'\' )"></i></div><div><br /><br />';
                    $("#extra_dir_box").append( html );
                    lastSelectedFolder = "";
                }
            }

            function dir_to_id( dir, deny ) {
                var id = "";
                if( deny ) {
                    id = "LIFT_"+Math.floor((Math.random() * 10000) + 1);
                    if( $('#'+id).length )
                    {
                        return dir_to_id( dir, deny );
                    }
                }
                else {
                    id = "LIFTEX_"+Math.floor((Math.random() * 10000) + 1);
                    if( $('#'+id).length )
                    {
                        return dir_to_id( dir, deny );
                    }
                }
                return id;
            }

            function removeExtraDir( dir, id ) {
                removeItem( userExtraDirs, dir );
                $("#"+id).remove();
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

            function reset_perms()
            {
                userPermission = new Array();
                selected_perms = new Array();
                for(var i in permissions)
                {
                    document.getElementById(permissions[i]).checked = false;
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

            function check_and_send_user_info()
            {
                var firstname = document.forms["add_user_form"]["firstname"].value;
                var lastname = document.forms["add_user_form"]["lastname"].value;
                var email = document.forms["add_user_form"]["email"].value;
                var atpos = email.indexOf("@");
                var dotpos = email.lastIndexOf(".");
                var username = document.forms["add_user_form"]["username"].value;
                var password = document.forms["add_user_form"]["password"].value;
                var send_pass = document.forms["add_user_form"]["send_pass"].value;
                var user_dir = document.forms["add_user_form"]["user_dir"].value;
                var user_extra_dir = "";
                var deny_files_folders = "";
                if( userExtraDirs.length > 0 ) {
                    user_extra_dir = userExtraDirs.join(", ");
                }
                if( userDenyFiles.length > 0 ) {
                    deny_files_folders = userDenyFiles.join(", ");
                }
                var limitation = document.forms["add_user_form"]["limitation"].value;
                var upload_limitation = document.forms["add_user_form"]["upload_limitation"].value;
                if(firstname == "" || firstname == null)
                {
                    alert("<?php language_filter("Please write user first name.", false, true)?>");
                    return false;
                }
                if(lastname == "" || lastname == null)
                {
                    alert("<?php language_filter("Please write user last name.", false, true)?>");
                    return false;
                }
                if(email == "" || email == null)
                {
                    alert("<?php language_filter("Please write user email.", false, true)?>");
                    return false;
                }
                if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
                {
                    alert("<?php language_filter("Not a valid email address.", false, true)?>");
                    return false;
                }
                if(username == "" || username == null)
                {
                    alert("<?php language_filter("Please write username.", false, true)?>");
                    return false;
                }
                /*if(password == "" || password == null)
                {
                    alert("Please write user password.");
                    return false;
                }*/
                if(user_dir == "" || user_dir == null)
                {
                    alert("<?php language_filter("Please select a directory for user.", false, true)?>");
                    return false;
                }
                if(limitation == "" || limitation == null || limitation <= 0)
                {
                    alert("<?php language_filter("Please write limitation size.", false, true)?>");
                    return false;
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

                check_user_email(username, email, firstname, lastname, password, send_pass, user_dir, limitation, upload_limitation, deny_files_folders, user_extra_dir);
            }

            function check_user_email(username, email, firstname, lastname, password, send_pass, user_dir, limitation, upload_limitation, deny_files_folders, user_extra_dir)
            {
                show_preloader();
                setTimeout(function(){
                    $.post("ajax_check_user.php",
                    {
                        username_check:username,
                        email_check:email,
                        dir_check:user_dir,
                        check_id:'<?php echo $userId?>'
                    },
                    function(data,status)
                    {
                        if(status == "success")
                        {
                            if(data == "done")
                            {
                                $.post("ajax_edit_user.php",
                                {
                                    editUser:'<?php echo $core->admin_id;?>',
                                    userId:'<?php echo $userId?>',
                                    backPage:'<?php echo $backPage?>',
                                    edit_username:username,
                                    edit_email:email,
                                    edit_firstname:firstname,
                                    edit_lastname:lastname,
                                    edit_password:password,
                                    edit_send_pass:send_pass,
                                    edit_user_dir:user_dir,
                                    edit_deny_files:deny_files_folders,
                                    edit_user_extra_dir:user_extra_dir,
                                    edit_user_perm:userPermission,
                                    edit_user_ext:userExtensions,
                                    edit_user_up:userUploader,
                                    edit_limitation:limitation,
                                    edit_upload_limitation:upload_limitation
                                },
                                function(data,status)
                                {
                                    if(status == "success")
                                    {
                                        $('#content_show').html('');
                                        $('.bar').addClass('bar-success');
                                        $('li').removeClass();
                                        $('#users').addClass('active');
                                        //$("#preloader").modal("hide");
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
                            }
                            else if(data == "username")
                            {
                                show_errors_on_nav("<?php language_filter("Username already exists.", false, true)?>", "red");
                                return false;
                            }
                            else if(data == "email")
                            {
                                show_errors_on_nav("<?php language_filter("Email already exists.", false, true)?>", "red");
                                return false;
                            }
                            else
                            {
                                show_errors_on_nav("<?php language_filter("User directory not exists.", false, true)?>", "red");
                                return false;
                            }
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
            here = "<?php echo ROOT_DIR_PATH;?>";
            </script>
<?php
                echo "<script>show_status_ext('".addslashes($result)."', ".$status.");</script>";
            }
        }
    }
}
?>