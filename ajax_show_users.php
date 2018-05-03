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
        if (isset($_POST["showUser"]))
        {
            $result = "";
            $status = 0;
            $core->adminInfo();
            if ($_POST["showUser"] == $core->admin_id)
            {
                if(isset($_POST["block_user"]))
                {
                    if($core->block_user($_POST["block_user"], $_POST["block_method"]))
                    {
                        if($_POST["block_method"] == 0)
                        {
                            $status = 1;
                            $result = language_filter("User has been blocked.", true);
                        }
                        else
                        {
                            $status = 1;
                            $result = language_filter("User has been unblocked.", true);
                        }
                    }
                    else
                    {
                        if($_POST["block_method"] == 0)
                        {
                            $result = language_filter("User has not been blocked.", true);
                        }
                        else
                        {
                            $result = language_filter("User has not been unblocked.", true);
                        }
                    }
                }
                if(isset($_POST["remove_user"]))
                {
                    if($core->delete_user($_POST["remove_user"]))
                    {
                        $status = 1;
                        $result = language_filter("User has been removed.", true);
                    }
                    else
                    {
                        $result = language_filter("User has not been removed.", true);
                    }
                }


                $users = $core->get_users();
?>
     <div class="row">
         <div class="col-md-12">
             <button type="button" class="btn btn-default pull-right" id="addUser" onclick="showAddUser()"><?php language_filter("Add User");?></button>
         </div>
     </div>
<?php
                if($users == "")
                {
                    echo '<br /><div class="alert alert-info" style="text-align: center">'.language_filter("NO USERS", true).'</div>';
                    if(isset($_POST["remove_user"]))
                    {
                        ?>
                             <script>
                                 function show_status_ext_end(msg, status)
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
                             </script>
                        <?php
                        echo "<script>show_status_ext_end('".addslashes($result)."', ".$status.");</script>";
                    }
                    exit;
                }
                $count = count($users["id"]);
                if(isset($_POST["page"]))
                {
                    $page = $_POST["page"];
                }
                else
                {
                    $page = 1;
                }
                $core->page($page, $count, 10);
?>
                <div class="row">
                    <div class="col-md-12">
                <?php
                    if($core->pageCount != 1)
                    {
                ?>
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group btn-group-xs">
                            <?php
                            for ($i = 1; $i <= $core->pageCount; $i++)
                            {
                            ?>
                                <button type="button" class="btn btn-default <?php if ($i == $page) echo "active"?>" onclick="change_page('<?php echo $i?>');"><?php echo $i;?></button>
                            <?php
                            }
                            ?>
                            </div>
                        </div>
                <?php
                    }
                ?>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?php language_filter("Full_Name");?></th>
                                    <th><?php language_filter("Username");?></th>
                                    <th><?php language_filter("Email");?></th>
                                    <!--<th><?php /*language_filter("Date added");*/?></th>-->
                                    <th><?php language_filter("Used Size");?></th>
                                    <th><?php language_filter("Extra");?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                        for ($i = $core->start; $i < $core->end; $i++)
                        {
                        ?>
                            <tr <?php if($users["is_block"][$i] == 1) echo 'class="danger"';?>>
                                <td><?php echo $users["firstname"][$i]." ".$users["lastname"][$i];?></td>
                                <td><?php echo $users["username"][$i];?></td>
                                <td><?php echo $users["email"][$i];?></td>
                                <!--<td><?php /*echo $users["date_added"][$i];*/?></td>-->
                                <td style="width: 30%">
                                    <div class="progress progress-striped">
                                        <div class="progress-bar <?php if($users["limitation"][$i] < 50) echo 'progress-bar-success'; elseif($users["limitation"][$i] > 50 and $users["limitation"][$i] < 90) echo 'progress-bar-warning'; else{ echo 'progress-bar-danger'; }?>" role="progressbar" aria-valuenow="<?php echo $users["limitation"][$i];?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $users["limitation"][$i];?>%">
                                            <span class="" style="color: #000;"><?php if($users["limitation"][$i] < 100) echo intval($users["limitation"][$i])."%"; else echo '100%';?></span>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <div class="tn-group btn-group-xs">
                                        <button type="button" onclick="edit_user('<?php echo $users["id"][$i]?>');" class="btn btn-default tip" data-toggle="tooltip" data-placement="top" title="<?php language_filter("Edit");?>"><span class="glyphicon glyphicon-edit"></span></button>
                                        <button type="button" onclick="block_user('<?php echo $users["id"][$i];?>', '<?php echo $users["is_block"][$i];?>','alert');" class="btn btn-default tip" data-toggle="tooltip" data-placement="top" title="<?php if($users["is_block"][$i] == 0) language_filter("Block"); else language_filter("Unblock");?>"><span class="glyphicon <?php if($users["is_block"][$i] == 0) echo 'glyphicon-ban-circle'; else echo 'glyphicon-retweet';?>"></span></button>
                                        <button type="button" onclick="remove_user('<?php echo $users["id"][$i];?>', 'alert');" class="btn btn-default tip" data-toggle="tooltip" data-placement="top" title="<?php language_filter("Remove");?>"><span class="glyphicon glyphicon-remove-circle"></span></button>
                                        <button type="button" onclick="showTree('<?php echo addslashes($users["dir_path"][$i]);?>', '<?php echo $users["id"][$i]?>')" class="btn btn-default tip" data-toggle="tooltip" data-placement="top" title="<?php language_filter("User Folder");?>"><span class="glyphicon glyphicon-folder-open"></span></button>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-xs"><?php language_filter("More_info");?></button>
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="javascript:;" onclick="show_user_profile('<?php echo addslashes($users["firstname"][$i]." ".$users["lastname"][$i]);?>', '<?php echo addslashes($users["username"][$i]);?>', '<?php echo addslashes($users["email"][$i]);?>', '<?php echo addslashes($users["dir_path"][$i])?>' , '<?php echo addslashes($users["date_added"][$i])?>');"><?php language_filter("User Profile")?></a></li>
                                            <li><a href="javascript:;" onclick="show_user_permissions('<?php echo implode(", ", $users["permissions"][$i]);?>');"><?php language_filter("User Permissions")?></a></li>
                                            <li><a href="javascript:;" onclick="show_user_extension('<?php echo implode(", ", $users["filemanager_ext"][$i]);?>')"><?php language_filter("Allow Extensions")?></a></li>
                                            <li><a href="javascript:;" onclick="show_user_uploader('<?php echo implode(", ", $users["uploader_ext"][$i]);?>', '<?php echo $users["upload_limitation"][$i];?>')"><?php language_filter("Allow Uploader")?></a></li>
                                            <li><a href="javascript:;" onclick="show_user_deny('<?php echo addslashes($core->filter_txt(implode(", ", $users["deny_folders"][$i])));?>')"><?php language_filter("Deny Folders/Files")?></a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                            </tbody>
                        </table>
                    </div>


                <?php
                    if($core->pageCount != 1)
                    {
                    ?>
                        <div class="btn-toolbar" role="toolbar">
                            <div class="btn-group btn-group-xs">
                                <?php
                                for ($i = 1; $i <= $core->pageCount; $i++)
                                {
                                ?>
                                    <button type="button" class="btn btn-default <?php if ($i == $page) echo "active"?>" onclick="change_page('<?php echo $i?>');"><?php echo $i;?></button>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    }
                ?>
                    </div>
                </div>


                <div class="modal fade" id="siteMap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel"><?php language_filter("User Files And Folders")?></h4>
                            </div>
                            <div class="modal-body">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" id="extra_dirs_tabs" role="tablist">

                                </ul>
                                <!-- Tab panes -->
                                <br />
                                <p id="extra_info"class="label label-info"></p>

                                <p id="container_id"></p>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel")?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="alerts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="alert_head"></h4>
                            </div>
                            <div class="modal-body">
                                <p id="alert_body"></p>
                            </div>
                            <div class="modal-footer" id="alert_footer">
                            </div>
                        </div>
                    </div>
                </div>

                <script src="filemanager_js/jqueryFileTree.js"></script>
                <script>
                    $('.tip').tooltip();

                    function name_of_dir( path )
                    {
                        var name = path.split("/");
                        var newName = name[ name.length - 1 ];
                        if( newName == "" ) {
                            newName = name[ name.length - 2 ];
                            if( newName == "." || newName == ".." ) {
                                newName = "<?php language_filter( "ROOT", false, true );?>";
                            }
                        }
                        return newName;
                    }

                    function showTree(path, extra)
                    {
                        $('#container_id').html("<?php language_filter('Loading...', false, true)?>");
                        $("#extra_dirs_tabs").empty();
                        $.post(
                            'ajax_check_user.php',
                            {
                                check_extra_dir: extra
                            },
                            function( data, status ) {
                                if( status == "success" ) {
                                    var newName = name_of_dir( path );
                                    var li = '<li class="active"><a href="#home" class="liftTabs" onclick="show_extra_dir($(this));" data-href="'+path+'" role="tab" data-toggle="tab">'+newName+'</a></li>';
                                    $("#extra_dirs_tabs").append( li );
                                    if( data != "" ) {
                                        data = data.split(", ");
                                        for( var i in data ) {
                                            newName = name_of_dir( data[i] );
                                            li = '<li><a href="#dir'+i+'" class="liftTabs" onclick="show_extra_dir($(this));" data-href="'+data[i]+'" role="tab" data-toggle="tab">'+newName+'</a></li>';
                                            $("#extra_dirs_tabs").append( li );
                                        }
                                    }
                                    $("#extra_info").html('');
                                    if( path != "<?php echo ROOT_DIR_PATH;?>")
                                        $("#extra_info").html( path );
                                    $('#container_id').fileTree({
                                        root: path,
                                        script: 'jqueryFileTree.php?showFile=true',
                                        expandSpeed: 500,
                                        collapseSpeed: 500,
                                        multiFolder: false
                                    }, function(file) {
                                        // NOTHING
                                    });
                                }
                                else {
                                    alert( "Server Error!" );
                                    $('#container_id').fileTree({
                                        root: path,
                                        script: 'jqueryFileTree.php?showFile=true',
                                        expandSpeed: 500,
                                        collapseSpeed: 500,
                                        multiFolder: false
                                    }, function(file) {
                                        // NOTHING
                                    });
                                }
                            }
                        );
                        $("#siteMap").modal('show');
                    }

                    function show_extra_dir(e) {
                        var is_active = e.parent().hasClass( 'active' );
                        if( is_active ) return;
                        var this_path = e.attr("data-href");
                        $("#extra_info").html('');
                        if( this_path != "<?php echo ROOT_DIR_PATH;?>")
                            $("#extra_info").html( this_path );
                        $('#container_id').empty();
                        $('#container_id').html( "<?php language_filter( "Loading...", false, true )?>" );
                        $('#container_id').fileTree({
                            root: this_path,
                            script: 'jqueryFileTree.php?showFile=true',
                            expandSpeed: 500,
                            collapseSpeed: 500,
                            multiFolder: false
                        }, function(file) {
                            // NOTHING
                        });
                    }

                    function change_page(page_go)
                    {
                        $('#preloader').modal('show');
                        $.post("ajax_show_users.php",
                        {
                            showUser:'<?php echo $core->admin_id;?>',
                            page:page_go
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
                    function block_user(user_id, method, time)
                    {
                        if(time == "alert")
                        {
                            if(method == 0)
                            {
                                $("#alert_head").html('<?php language_filter("Block User", false, true);?>');
                                $("#alert_body").html('<?php language_filter("Do you want to block this user", false, true);?>');
                                $("#alert_footer").html('<button class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel", false, true)?></button>');
                                $("#alert_footer").html($("#alert_footer").html()+'<button class="btn btn-warning" onclick="block_user(\''+user_id+'\', \''+method+'\', \'go\')"><?php language_filter("Block", false, true)?></button>');
                            }
                            else
                            {
                                $("#alert_head").html('<?php language_filter("Unblock User", false, true)?>');
                                $("#alert_body").html('<?php language_filter("Do you want to unblock this user", false, true)?>');
                                $("#alert_footer").html('<button class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel", false, true)?></button>');
                                $("#alert_footer").html($("#alert_footer").html()+'<button class="btn btn-warning" onclick="block_user(\''+user_id+'\', \''+method+'\', \'go\')"><?php language_filter("Unblock", false, true)?></button>');
                            }
                            $("#alerts").modal('show');
                        }
                        else
                        {
                            $("#alerts").modal('hide');
                            show_preloader();
                            setTimeout(function(){
                                $.post("ajax_show_users.php",
                                {
                                    showUser:'<?php echo $core->admin_id;?>',
                                    page:'<?php echo $page?>',
                                    block_user:user_id,
                                    block_method:method
                                },
                                function(data,status)
                                {
                                    if(status == "success")
                                    {
                                        $('#content_show').html('');
                                        $('.bar').addClass('bar-success');
                                        $('li').removeClass();
                                        $('#users').addClass('active');
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
                    function remove_user(user_id, time)
                    {
                        if(time == 'alert')
                        {
                            $("#alert_head").html('<?php language_filter("Remove User", false, true)?>');
                            $("#alert_body").html('<?php language_filter("Do you want to remove this user", false, true)?>');
                            $("#alert_footer").html('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button>');
                            $("#alert_footer").html($("#alert_footer").html()+'<button class="btn btn-danger" onclick="remove_user(\''+user_id+'\', \'go\')"><?php language_filter("Remove", false, true)?></button>');
                            $("#alerts").modal('show');
                        }
                        else
                        {
                            $("#alerts").modal('hide');
                            //$('#preloader').modal('show');
                            show_preloader();
                            setTimeout(function(){
                                $.post("ajax_show_users.php",
                                {
                                    showUser:'<?php echo $core->admin_id;?>',
                                    page:'<?php echo $page;?>',
                                    remove_user:user_id
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
                            }, 1000);
                        }
                    }

                    function show_user_permissions(value)
                    {
                        $("#alert_head").html('<?php language_filter("User Permissions", false, true)?>');
                        if(value != "")
                            $("#alert_body").html('<div class="alert alert-info">'+value+'</div>');
                        else
                            $("#alert_body").html('<div class="alert alert-info" style="text-align: center"><?php language_filter("Public User", false, true)?></div>');
                        $("#alert_footer").html('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button>');
                        $("#alerts").modal('show');
                    }
                    function show_user_extension(value)
                    {
                        $("#alert_head").html('<?php language_filter("User Extensions", false, true)?>');
                        $("#alert_body").html('<div class="alert alert-info">'+value+'</div>');
                        $("#alert_footer").html('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button>');
                        $("#alerts").modal('show');
                    }
                    function show_user_uploader(value, size)
                    {
                        $("#alert_head").html('<?php language_filter("User Uploader Extensions", false, true)?>');
                        if(value != "")
                            $("#alert_body").html('<div class="alert alert-info">'+value+'<br><br><b><?php language_filter("Upload Limitation Size", false, true)?> </b>'+size+' MB</div>');
                        else
                            $("#alert_body").html('<div class="alert alert-info" style="text-align: center"><?php language_filter("No Upload Files Permission", false, true)?></div>');
                        $("#alert_footer").html('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button>');
                        $("#alerts").modal('show');
                    }
                    function show_user_deny(value)
                    {
                        $("#alert_head").html('<?php language_filter("User Deny Files And Folders", false, true)?>');
                        if(value != "") {
                            var find = '\t';
                            var re = new RegExp(find, 'g');
                            value = value.replace(re, '&#92;t');
                            find = '\n';
                            re = new RegExp(find, 'g');
                            value = value.replace(re, '&#92;n');
                            $("#alert_body").html('<div class="alert alert-info">'+value+'</div>');
                        }
                        else
                            $("#alert_body").html('<div class="alert alert-info" style="text-align: center"><?php language_filter("No Deny Files And Folders", false, true)?></div>');
                        $("#alert_footer").html('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button>');
                        $("#alerts").modal('show');
                    }
                    function show_user_profile(fullname, username, email, dir, date)
                    {
                        if(dir == "<?php echo ROOT_DIR_PATH ?>")
                        {
                            dir = "<?php echo language_filter("ROOT", false, true);?>";
                        }
                        $("#alert_head").html('<?php language_filter("User Profile", false, true)?>');
                        $("#alert_body").html('<p><b><?php language_filter("Full Name", false, true)?> </b>'+fullname+'</p> <p><b><?php language_filter("Username", false, true)?>: </b>'+username+'</p> <p><b><?php language_filter("Email", false, true)?>: </b>'+email+'</p> <p><b><?php language_filter("User Directory", false, true)?>: </b>'+dir+'</p> <p><b><?php language_filter("Date Registration", false, true)?>: </b>'+date+'</p>');
                        $("#alert_footer").html('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button>');
                        $("#alerts").modal('show');
                    }

                    function edit_user(user_id)
                    {
                        $('#preloader').modal('show');
                        $.post("ajax_edit_user.php",
                        {
                            editUser:'<?php echo $core->admin_id;?>',
                            backPage:'<?php echo $page;?>',
                            userId:user_id
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
