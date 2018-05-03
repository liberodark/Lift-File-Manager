<?php
if (!isset($core))
{
    require_once '../filemanager_user_core.php';
    $core = new filemanager_user_core();
    $core->userInfo();
    $core->create_user_panel($core->user_id);
    require_once '../filemanager_language_user.php';
}
if ($core->isLogin() and isset( $user_modals ))
{
    if($settings->share == "on")
    {
?>

<div class="modal fade" id="share_files" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel_share"><?php language_filter("Share");?></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="send_to" class="col-sm-2 control-label"><?php language_filter("Send to");?></label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="send_to" placeholder="email@example.com">
                        </div>
                        <div class="col-sm-1" style="margin-top: 7px;">
                            <span class="glyphicon glyphicon-plus-sign" style="cursor: pointer;" onclick="add_send_to();"></span>
                        </div>
                    </div>
                    <div id="send_to_place">

                    </div>
                    <div class="form-group">
                        <label for="send_to" class="col-sm-2 control-label"><?php language_filter("Send to");?></label>
                        <div class="col-sm-10">
                            <textarea id="extra_send_to" class="form-control" rows="3" placeholder="email@example.com,email2@example.com,email3@example.com,email4@example.com,email5@example.com"></textarea>
                        </div>
                    </div>
                    <hr />
                    <div class="form-group">
                        <label for="from" class="col-sm-2 control-label"><?php language_filter("From");?></label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="from" value="<?php echo $core->user_email?>" placeholder="email@example.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="from" class="col-sm-2 control-label"><?php language_filter("subject");?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="subject" placeholder="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message" class="col-sm-2 control-label"><?php language_filter("Message");?></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="4" id="message"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel")?></button>
                <button type="button" class="btn btn-warning" onclick="download_selected_files();"><?php language_filter("Download")?></button>
                <button type="button" class="btn btn-primary" onclick="share_selected_files();"><?php language_filter("Share")?></button>
            </div>
        </div>
    </div>
</div>

<?php
    }
?>

<div class="modal fade" id="showConf" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="confLable"></h4>
            </div>
            <div class="modal-body" id="container_id_tree">

            </div>
            <div class="modal-footer" id="confButton" style="text-align: left;">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploader" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel_upload"><?php language_filter("Upload")?></h4>
            </div>
            <div class="modal-body" id="show_uploader">
                <?php language_filter("Loading...")?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="show_preloader(); setTimeout(function(){loading_from_file = false; hide_preloader(); showFileManager(here)}, 1000);"><?php language_filter("Click to see uploaded files.")?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newFolder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel_folder"><?php language_filter("Create New Folder")?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" class="form-control" id="new_folder" placeholder="<?php language_filter("New Folder Name")?>" onchange="set_new_folder_name(this.value);"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel")?></button>
                <button type="button" class="btn btn-primary" onclick="mkdir();"><?php language_filter("Create")?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newzipFile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel_zip"><?php language_filter("Create Zip File")?></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" style="text-align: center; font-weight: bold;"><?php language_filter("Please write zip file name without extension.")?></div>
            </div>
            <div class="modal-footer">
                <div class="form-group col-md-4">
                    <input type="text" class="form-control" id="new_zip" placeholder="<?php language_filter("Zip File Name")?>"  onchange="set_new_zipFile_name(this.value);"/>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel")?></button>
                <button type="button" class="btn btn-primary" onclick="create_zip();"><?php language_filter("Create")?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newbackupFile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel_backup"><?php language_filter("Create Backup")?></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" style="text-align: center; font-weight: bold;"><?php language_filter("Please write zip file name without extension.")?></div>
            </div>
            <div class="modal-footer">
                <div class="form-group col-md-4">
                    <input type="text" class="form-control" id="new_zip_backup" placeholder="<?php language_filter("Zip File Name")?>" onchange="set_new_zipFile_name(this.value);"/>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel")?></button>
                <button type="button" class="btn btn-primary" onclick="create_backup();"><?php language_filter("Create")?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="removeSelected" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel_folders"><?php language_filter("Remove Selected Files And Folders")?></h4>
            </div>
            <div class="modal-body">
                <?php language_filter("Do you want to remove selected files and folders")?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel")?></button>
                <button type="button" class="btn btn-primary" onclick="remove_selected();"><?php language_filter("Remove")?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="moveSelected" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel_move"><?php language_filter("Move Selected Files And Folders")?></h4>
            </div>
            <div class="modal-body" id="container_id_tree2">

            </div>
            <div class="modal-footer">
                <div class="form-group col-md-4">
                    <input type="text" class="form-control" id="selected_move" placeholder="<?php language_filter("New Folder Path")?>" onchange="/*set_new_name(this.value);*/" disabled="disabled"/>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel")?></button>
                <button class="btn btn-info" type="button" onclick="showInlineTree();"><?php language_filter("Browse")?></button>
                <button type="button" class="btn btn-primary" onclick="move_selected();"><?php language_filter("Move")?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="copySelected" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel_copy"><?php language_filter("Copy Selected Files And Folders")?></h4>
            </div>
            <div class="modal-body" id="container_id_tree3">
            </div>
            <div class="modal-footer">
                <div class="form-group col-md-4">
                    <input type="text" class="form-control" id="selected_copy" placeholder="<?php language_filter("New Folder Path")?>" onchange="/*set_new_name(this.value);*/" disabled="disabled"/>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel")?></button>
                <button class="btn btn-info" type="button" onclick="showInlineTree();"><?php language_filter("Browse")?></button>
                <button type="button" class="btn btn-primary" onclick="copy_selected();"><?php language_filter("Copy")?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="download_files" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel_download"><?php language_filter("Download");?></h4>
            </div>
            <div class="modal-body">
                <p><?php language_filter("Do_you_want_to_download_selected_files_or_folders"); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel")?></button>
                <button type="button" class="btn btn-primary" onclick="download_selected_files();"><?php language_filter("Download")?></button>
            </div>
        </div>
    </div>
</div>


<!-- PDF modal -->
<div class="modal fade" id="pdf_file" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="pdf_file_label"></h4>
            </div>
            <div class="modal-body" id="pdf_file_body">
                <iframe style="width: 100%; height: 400px; border: 0" id="pdf_file_iframe">

                </iframe>
            </div>
        </div>
    </div>
</div>


<!-- Edit file modal -->
<div class="modal fade" id="show_edit_file" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="show_edit_file_label"></h4>
            </div>
            <div class="modal-body" style="padding: 0">
                <iframe style="width: 100%; height: 400px; border: 0" id="edit_file_iframe">

                </iframe>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="share_system_files" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php language_filter("share_in_system");?></h4>
            </div>
            <div class="modal-body">
                <p><?php language_filter("share_alert_msg"); ?></p>
                <div class="form-group">
                    <label><?php language_filter("file_desc")?></label>
                    <textarea class="form-control" rows="3" id="system_share_desc"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Cancel")?></button>
                <button type="button" class="btn btn-primary" onclick="system_share()"><?php language_filter("Share")?></button>
            </div>
        </div>
    </div>
</div>

<script>

$(function () {
    /*$(window).resize(function () {
        var h = Math.max($(window).height() - 0, 420);
        $('#container, #data, #tree, #data .content').height(h).filter('.default').css('lineHeight', h + 'px');
    }).resize();*/

    $('#tree')
    .jstree({
        'core' : {
            'data' : {
                'url' : 'filemanager_user/filemanager_siteMap.php?operation=get_node&switch=<?php echo @$active;?>',
                'data' : function (node) {
                    return { 'id' : node.id };
                }
            },
            'check_callback' : function(o, n, p, i, m) {
                if(m && m.dnd && m.pos !== 'i') { return false; }
                if(o === "move_node" || o === "copy_node") {
                    if(this.get_node(n).parent === this.get_node(p).id) { return false; }
                }
                return true;
            },
            'themes' : {
                'responsive' : false,
                'variant' : 'small',
                'stripes' : true
            }
        },
        'sort' : function(a, b) {
            return this.get_type(a) === this.get_type(b) ? (this.get_text(a) > this.get_text(b) ? 1 : -1) : (this.get_type(a) >= this.get_type(b) ? 1 : -1);
        },
        'contextmenu' : {
            'items' : function(node) {
                var tmp = $.jstree.defaults.contextmenu.items();
                delete tmp.create.action;
                tmp.create.label = "New";
                tmp.create.submenu = {
                    "create_folder" : {
                        "separator_after"	: true,
                        "label"				: "Folder",
                        "action"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);
                            inst.create_node(obj, { type : "default" }, "last", function (new_node) {
                                setTimeout(function () { inst.edit(new_node); },0);
                            });
                        }
                    },
                    "create_file" : {
                        "label"				: "File",
                        "action"			: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                    obj = inst.get_node(data.reference);
                            inst.create_node(obj, { type : "file" }, "last", function (new_node) {
                                setTimeout(function () { inst.edit(new_node); },0);
                            });
                        }
                    }
                };
                if(this.get_type(node) === "file") {
                    delete tmp.create;
                }
                return tmp;
            }
        },
        'types' : {
            'default' : { 'icon' : 'folder' },
            'file' : { 'valid_children' : [], 'icon' : 'file' }
        },
        'unique' : {
            'duplicate' : function (name, counter) {
                return name + ' ' + counter;
            }
        },
        'plugins' : ['state','dnd','sort','types','contextmenu','unique']
    })
    .on('delete_node.jstree', function (e, data) {
        $.get('filemanager_user/filemanager_siteMap.php?operation=delete_node&switch=<?php echo @$active;?>', { 'id' : data.node.id })
                .done(function (d) {
                    var lift_parent = data.node;
                    lift_parent = "/"+lift_parent.parent;
                    if( here == root_name+"/"+lift_parent ) {
                        showFileManager( here );
                    }
                    if( here == root_name && lift_parent == '//' ) {
                        showFileManager( here );
                    }
                })
                .fail(function () {
                    data.instance.refresh();
                });
    })
    .on('dblclick.jstree', function(e, data) {
        var node = $(e.target).closest("li");
        var navigate = "/"+node[0].id;
        var ext = navigate.split('.').pop();
        ext = ext.toLowerCase();
        if( !in_array( supported_ext, ext ) ) {
            showFileManager( root_name+"/"+navigate );
        }
    })
    .on('create_node.jstree', function (e, data) {
        $.get('filemanager_user/filemanager_siteMap.php?operation=create_node&switch=<?php echo @$active;?>', { 'type' : data.node.type, 'id' : data.node.parent, 'text' : data.node.text })
                .done(function (d) {
                    data.instance.set_id(data.node, d.id);
                    var lift_parent = data.node;
                    lift_parent = "/"+lift_parent.parent;
                    if( here == root_name+"/"+lift_parent ) {
                        showFileManager( here );
                    }
                    if( here == root_name && lift_parent == '//' ) {
                        showFileManager( here );
                    }
                })
                .fail(function () {
                    data.instance.refresh();
                });
    })
    .on('rename_node.jstree', function (e, data) {
        $.get('filemanager_user/filemanager_siteMap.php?operation=rename_node&switch=<?php echo @$active;?>', { 'id' : data.node.id, 'text' : data.text })
                .done(function (d) {
                    data.instance.set_id(data.node, d.id);
                    var lift_parent = data.node;
                    lift_parent = "/"+lift_parent.parent;
                    if( here == root_name+"/"+lift_parent ) {
                        showFileManager( here );
                    }
                    if( here == root_name && lift_parent == '//' ) {
                        showFileManager( here );
                    }
                })
                .fail(function () {
                    data.instance.refresh();
                });
    })
    .on('move_node.jstree', function (e, data) {
        $.get('filemanager_user/filemanager_siteMap.php?operation=move_node&switch=<?php echo @$active;?>', { 'id' : data.node.id, 'parent' : data.parent })
                .done(function (d) {
                    //data.instance.load_node(data.parent);
                    data.instance.refresh();
                    var lift_parent = "/"+data.old_parent;
                    if( here == root_name+"/"+lift_parent ) {
                        showFileManager( here );
                    }
                    if( here == root_name && lift_parent == '//' ) {
                        showFileManager( here );
                    }
                    lift_parent = data.node;
                    lift_parent = "/"+lift_parent.parent;
                    if( here == root_name+"/"+lift_parent ) {
                        showFileManager( here );
                    }
                    if( here == root_name && lift_parent == '//' ) {
                        showFileManager( here );
                    }
                })
                .fail(function () {
                    data.instance.refresh();
                });
    })
    .on('copy_node.jstree', function (e, data) {
        $.get('filemanager_user/filemanager_siteMap.php?operation=copy_node&switch=<?php echo @$active;?>', { 'id' : data.original.id, 'parent' : data.parent })
                .done(function (d) {
                    //data.instance.load_node(data.parent);
                    data.instance.refresh();
                    var lift_parent = data.node;
                    lift_parent = "/"+lift_parent.parent;
                    if( here == root_name+"/"+lift_parent ) {
                        showFileManager( here );
                    }
                    if( here == root_name && lift_parent == '//' ) {
                        showFileManager( here );
                    }
                })
                .fail(function () {
                    data.instance.refresh();
                });
    })
    .on('changed.jstree', function (e, data) {
        if(data && data.selected && data.selected.length) {
            $.get('filemanager_user/filemanager_siteMap.php?operation=get_content&switch=<?php echo @$active;?>&id=' + data.selected.join(':'), function (d) {
                if(d && typeof d.type !== 'undefined') {
                    $('#data .content').hide();
                    switch(d.type) {
                        case 'text':
                        case 'txt':
                        case 'md':
                        case 'htaccess':
                        case 'log':
                        case 'sql':
                        case 'php':
                        case 'js':
                        case 'json':
                        case 'css':
                        case 'html':
                            $('#data .code').show();
                            $('#code').val(d.content);
                            break;
                        case 'png':
                        case 'jpg':
                        case 'jpeg':
                        case 'bmp':
                        case 'gif':
                            $('#data .image img').one('load', function () { $(this).css({'marginTop':'-' + $(this).height()/2 + 'px','marginLeft':'-' + $(this).width()/2 + 'px'}); }).attr('src',d.content);
                            $('#data .image').show();
                            break;
                        default:
                            $('#data .default').html(d.content).show();
                            break;
                    }
                }
            });
        }
        else {
            $('#data .content').hide();
            $('#data .default').html('Select a file from the tree.').show();
        }
    });
});


<?php
if( @$system_share == true ) {
?>
function system_share() {

    if( $("#system_share_desc").val() == "" ) {
        alert( "<?php language_filter( "file_desc_error", false, true);?>" );
        return false;
    }
    var desc = $("#system_share_desc").val();
    $("#share_system_files").modal("hide");
    show_preloader();
    setTimeout(function(){
        $.post("filemanager_user/ajax_manage_dir.php",
        {
            share_system_files:selected,
            this_place:here,
            description: desc,
            extra_dir_show:"<?php echo @$active;?>"
        },
        function(data,status)
        {
            if(status == "success")
            {
                $("#system_share_desc").val('');
                if(data == "true")
                {
                    show_errors_on_nav('<?php language_filter("share_system_done", false, true)?>', 'green');
                    return true;
                }
                else
                {
                    show_errors_on_nav('<?php language_filter("share_system_error", false, true)?>', 'red');
                    return false;
                }
            }
            else
            {
                alert("Error: " + status);
                hide_preloader();
            }
        });
    }, 1000);
}
<?php
}
?>

function show_pdf_file( info, filename ) {
    $("#pdf_file_label").html(filename);
    $("#pdf_file_iframe").attr( "src", "filemanager_user/filemanager_pdfShow.php?filename="+info+"&switch=<?php echo @$active;?>");
    $("#pdf_file").modal();
}

function show_edit_file( info, filename ) {
    $("#show_edit_file_label").html("<?php language_filter("Edit", false, true);?> "+filename);
    $("#edit_file_iframe").attr("src", "filemanager_user/edit_file.php?info="+info+"&switch=<?php echo @$active;?>");
    $("#show_edit_file").modal();
}

function select_all()
{
    var method = $("#select_all").html();
    var start = <?php echo $core->start;?>;
    var count = <?php echo $core->end;?>;
    var value = new Array(<?php if(is_array($filemanager->show_files_folders)) {for ($j = $core->start; $j < $core->end; $j++){ if($j == ($core->end - 1)){echo "\"".addslashes($filemanager->show_files_folders[$j])."\"";}else{echo "\"".addslashes($filemanager->show_files_folders[$j])."\", ";} } }?>);
    if(method == "<?php language_filter("Select All")?>")
    {
        $("#select_all").html("<?php language_filter("Unselect All")?>");
        for(var i = start; i < count; i++)
        {
            document.getElementById("check_"+i).checked = true;
        }
        selected = value;
    }
    else
    {
        $("#select_all").html("<?php language_filter("Select All")?>");
        for(var i = start; i < count; i++)
        {
            document.getElementById("check_"+i).checked = false;
        }
        selected = new Array();
    }
}

<?php
if($settings->share == "on")
{
?>

function add_send_to()
{
    var values = {};
    if(send_to_counter > 1)
    {
        for(var i = 1; i <= send_to_counter; i++)
        {
            if(typeof document.getElementsByName('send_to_'+i) != "undefined")
            {
                values[i] = $('#send_to_'+i).val();
            }
        }
    }
    var html = '<div class="form-group" id="form-group-'+send_to_counter+'">'+
            '<label for="send_to_'+send_to_counter+'" class="col-sm-2 control-label"><?php language_filter("Send to", false, true);?></label>'+
            '<div class="col-sm-9">'+
            '<input type="email" value="" class="form-control" id="send_to_'+send_to_counter+'" placeholder="email@example.com">'+
            '</div>'+
            '<div class="col-sm-1" style="margin-top: 7px;">'+
            '<span class="glyphicon glyphicon-minus-sign" style="cursor: pointer;" onclick="remove_send_to('+send_to_counter+');"></span>'+
            '</div>'+
            '</div>';
    document.getElementById("send_to_place").innerHTML += html;
    send_to_counter++;
    if(send_to_counter > 1)
    {
        for(var i = 1; i <= send_to_counter; i++)
        {
            if(typeof document.getElementsByName('send_to_'+i) != "undefined")
            {
                $('#send_to_'+i).val(values[i]);
            }
        }
    }
}

function remove_send_to(i)
{
    var div = document.getElementById("form-group-" + i);
    div.parentNode.removeChild(div);
}

function share_selected_files()
{
    var send_to = $("#send_to").val();
    var from = $("#from").val();
    var message = $('#message').val();
    var subject = $("#subject").val();
    var extra_send_to = $("#extra_send_to").val();
    var emails = new Array();
    if(send_to == '' || from == '' || message == '' || subject == '')
    {
        alert('<?php language_filter("Please_fill_the_fields.", false, true);?>');
        return false;
    }
    if(!validateEmail(send_to) || !validateEmail(from))
    {
        alert('<?php language_filter("Please_write_a_valid_email.", false, true);?>');
        return false;
    }
    if(send_to_counter > 1)
    {
        for(var i = 1; i <= send_to_counter; i++)
        {
            if(typeof document.getElementsByName('send_to_'+i) != "undefined")
            {
                if(typeof $("#send_to_"+i).val() != "undefined")
                {
                    if(!validateEmail($("#send_to_"+i).val()))
                    {
                        alert('<?php language_filter("Please_write_a_valid_email.", false, true);?>');
                        return false;
                    }
                    else
                    {
                        emails.push($("#send_to_"+i).val());
                    }
                }
            }
        }
    }
    if(extra_send_to != "")
    {
        var extra_checker = extra_send_to.split(",");
        for(var i in extra_checker)
        {
            if(!validateEmail(extra_checker[i]))
            {
                alert('<?php language_filter("Please_write_a_valid_email.", false, true);?>');
                return false;
            }
            else
            {
                emails.push(extra_checker[i]);
            }
        }
    }
    $("#share_files").modal("hide");
    if(emails.length == -1)
    {
        emails = "";
    }
    else
    {
        emails = emails.join(", ");
    }
    show_preloader();
    setTimeout(function(){
        $.post("filemanager_user/ajax_manage_dir.php",
        {
            share_selected:selected,
            this_path:here,
            send_to:send_to,
            from:from,
            subject:subject,
            message:message,
            emails:emails,
            extra_dir_show:"<?php echo @$active;?>"
        },
        function(data,status)
        {
            if(status == "success")
            {
                if(data == "true")
                {
                    show_errors_on_nav('<?php language_filter("share_done", false, true)?>', 'green');
                    $("#send_to_place").html('');
                    $("#send_to").val('');
                    $("#extra_send_to").val('');
                    $("#from").val('');
                    $("#subject").val('');
                    $('#message').val('');
                    send_to_counter = 1;
                    return true;
                }
                else
                {
                    show_errors_on_nav('<?php language_filter("share_error", false, true)?>', 'red');
                    return false;
                }
            }
            else
            {
                alert("Error: " + status);
                hide_preloader();
            }
        });
    }, 1000);
}
function validateEmail(email)
{
    var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length)
    {
        return false;
    }
    else
    {
        return true;
    }
}
    <?php
}
?>

function download_selected_files()
{
    $("#download_files").modal("hide");
    show_preloader();
    setTimeout(function(){
        $.post("filemanager_user/ajax_manage_dir.php",
        {
            download_selected:selected,
            this_path:here,
            extra_dir_show:"<?php echo @$active;?>"
        },
        function(data,status)
        {
            if(status == "success")
            {
                if(data != "false")
                {
                    show_errors_on_nav('<?php language_filter("Please wait to get the download started", false, true)?>', 'green');
                    document.location = data;
                    return false;
                }
                else
                {
                    show_errors_on_nav('<?php language_filter("Can not download selected files", false, true)?>', 'red');
                    return false;
                }
            }
            else
            {
                alert("Error: " + status);
                hide_preloader();
            }
        });
    }, 1000);
}

function copy_selected()
{
    document.getElementById('selected_copy').value = "";
    $("#copySelected").modal("hide");
    show_preloader();
    setTimeout(function(){
        $.post("filemanager_user/ajax_manage_dir.php",
        {
            copy_selected:selected,
            this_path:here,
            copy_path:new_name,
            extra_dir_show:"<?php echo @$active;?>"
        },
        function(data,status)
        {
            if(status == "success")
            {
                if(data == "true")
                {
                    loading_from_file = '<?php language_filter("Files and folders has been copied.", false, true)?>';
                    loading_from_file_status = "green";
                }
                else
                {
                    loading_from_file = '<?php language_filter("Error! Can not copy", false, true)?> '+data+'.';
                    loading_from_file_status = "red";
                }
                showFileManager(here);
            }
            else
            {
                alert("Error: " + status);
                hide_preloader();
            }
        });
    }, 1000);
}

function move_selected()
{
    $("#moveSelected").modal("hide");
    show_preloader();
    setTimeout(function(){
        $.post("filemanager_user/ajax_manage_dir.php",
        {
            move_selected:selected,
            this_path:here,
            move_path:new_name,
            extra_dir_show:"<?php echo @$active;?>"
        },
        function(data,status)
        {
            if(status == "success")
            {
                if(data == "true")
                {
                    loading_from_file = '<?php language_filter("Files and folders has been moved.", false, true)?>';
                    loading_from_file_status = "green";
                }
                else
                {
                    loading_from_file = '<?php language_filter("Error! Can not move", false, true)?> '+data+'.';
                    loading_from_file_status = "red";
                }
                showFileManager(here);
            }
            else
            {
                alert("Error: " + status);
                hide_preloader();
            }
        });
    }, 1000);
}

function remove_selected()
{
    $("#removeSelected").modal("hide");
    show_preloader();
    setTimeout(function(){
        $.post("filemanager_user/ajax_manage_dir.php",
        {
            remove_selected:selected,
            this_path:here,
            extra_dir_show:"<?php echo @$active;?>"
        },
        function(data,status)
        {

            if(status == "success")
            {
                if(data == "true")
                {
                    loading_from_file = '<?php language_filter("Files and folders has been removed.", false, true)?>';
                    loading_from_file_status = "green";
                }
                else
                {
                    loading_from_file = '<?php language_filter("Error! Can not remove", false, true)?> '+data+'. <?php language_filter("Please Wait...", false, true)?>';
                    loading_from_file_status = "red";
                }
                showFileManager(here);
            }
            else
            {
                alert("Error: " + status);
                hide_preloader();
            }
        });
    }, 1000);
}

function check_selected_files_folders()
{
    if(selected == "" || selected == null)
    {
        alert("<?php language_filter("Please select files and folders", false, true)?>");
    }
    else
    {
        $("#removeSelected").modal('show');
    }
}

function showUploader()
{
    var uploadDir = '<?php echo addslashes($path);?>';
    $.post("filemanager_user/filemanager_uploader.php",
    {
        upload_dir:uploadDir,
        extra_dir_show:"<?php echo @$active;?>"
    },
    function(data,status)
    {
        if(status == "success")
        {
            $("#show_uploader").html(data);
        }
        else
        {
            alert("Error: " + status);
        }
    });
}

function set_new_zipFile_name(value)
{
    var check = value.indexOf(".zip");
    if(check != -1)
    {
        alert("<?php language_filter("Please write zip file name without extension.", false, true)?>");
        return false;
    }
    zip_file_name = value;
}

function set_selected(value, id, checker)
{
    if(checker == true)
    {
        if(!in_array(selected, value))
        {
            selected.push(value);
        }
    }
    else
    {
        if(in_array(selected, value))
        {
            removeItem(selected, value);
        }
    }
}

function create_zip()
{
    if(selected == "" || selected == null)
    {
        alert("<?php language_filter("Please select files and folders", false, true)?>");
    }
    else
    {
        $("#newzipFile").modal("hide");
        show_preloader();
        setTimeout(function(){
            $.post("filemanager_user/ajax_manage_dir.php",
            {
                create_zip:selected,
                this_place:here,
                zip_name:zip_file_name,
                extra_dir_show:"<?php echo @$active;?>"
            },
            function(data,status)
            {
                if(status == "success")
                {
                    if(data == "true")
                    {
                        loading_from_file = '<?php language_filter("Zip file has been created.", false, true)?>';
                        loading_from_file_status = "green";
                    }
                    else
                    {
                        loading_from_file = '<?php language_filter("Zip file has not been created.", false, true)?>';
                        loading_from_file_status = "red";
                    }
                    showFileManager(here);
                }
                else
                {
                    alert("Error: " + status);
                    hide_preloader();
                }
            });
            zip_file_name = "";
        }, 1000);
    }
}

function create_backup()
{
    if(selected == "" || selected == null)
    {
        alert("<?php language_filter("Please select files and folders", false, true)?>");
    }
    else
    {
        $("#newbackupFile").modal("hide");
        show_preloader();
        setTimeout(function(){
            $.post("filemanager_user/ajax_manage_dir.php",
            {
                create_zip:selected,
                this_place:here,
                zip_name:zip_file_name,
                create_back_up:'true',
                extra_dir_show:"<?php echo @$active;?>"
            },
            function(data,status)
            {
                if(status == "success")
                {
                    if(data == "true")
                    {
                        loading_from_file = '<?php language_filter("Backup has been created.", false, true)?>';
                        loading_from_file_status = "green";
                    }
                    else
                    {
                        loading_from_file = '<?php language_filter("Backup has not been created.", false, true)?>';
                        loading_from_file_status = "red";
                    }
                    showFileManager(here);
                }
                else
                {
                    alert("Error: " + status);
                    hide_preloader();
                }
            });
            zip_file_name = "";
        }, 1000);
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

function cleanArray(actual)
{
    var newArray = new Array();
    for(var i = 0; i<actual.length; i++){
        if (actual[i]){
            newArray.push(actual[i]);
        }
    }
    return newArray;
}

function check_is_real_root()
{
    var user_dir = '<?php echo addslashes($core->user_dir);?>';
    if(user_dir == ".." || user_dir == "../")
    {
        return true;
    }
    else
    {
        return false;
    }
}

function showInlineTree()
{
    $('#container_id_tree').fileTree({
        root: map_path,
        script: 'filemanager_user/jqueryFileTree.php?switch=<?php echo @$active;?>',
        expandSpeed: 500,
        collapseSpeed: 500,
        multiFolder: false
    }, function(file) {
        file = file.replace( "<?php echo $core->user_dir?>", "" );
        var copy_new_name = document.getElementById('copy_new_name');
        if(copy_new_name != null)
        {
            $("#copy_new_name").val( file );
            filext = document.getElementById('rename_new_ext').value;
        }
        var rename_new_name = document.getElementById('rename_new_name');
        if(rename_new_name != null)
        {
            $("#rename_new_name").val( file );
            filext = document.getElementById('rename_new_ext').value;
        }

        var copy_new_name_dir = document.getElementById('copy_new_name_dir');
        if(copy_new_name_dir != null)
        {
            $("#copy_new_name_dir").val( file );
            filext = '';
        }

        var rename_new_name_dir = document.getElementById('rename_new_name_dir');
        if(rename_new_name_dir != null)
        {
            $("#rename_new_name_dir").val( file );
            filext = '';
        }

        set_new_name(file);
    });

    $('#container_id_tree2').fileTree({
        root: map_path,
        script: 'filemanager_user/jqueryFileTree.php?switch=<?php echo @$active;?>',
        expandSpeed: 500,
        collapseSpeed: 500,
        multiFolder: false
    }, function(file) {
        var set_back_slashes = '';
        /*if(is_root == "true")
        {
            var real_name_show = file.replace('../', '');
        }
        else
        {*/
        var real_name_show = file;
        var create_back = here.split("/");
        removeItem(create_back, "");
        create_back = create_back.length;
        var debug = here.split("/");
        removeItem(debug, "");
        create_back -= 2;
        for(var j = 0; j < debug.length; j++)
        {
            if(debug[j] == "" && j != (debug.length - 1))
            {
                create_back += 2;
                create_back -= 3;
                break;
            }
        }


        if(create_back >= 1)
        {
            for(var i = 0; i < create_back; i++)
            {
                set_back_slashes += '../';
            }
        }

        //}

        real_name_show = real_name_show.substring(0, real_name_show.length - 1);
        var selected_copy = document.getElementById('selected_move');
        if(selected_copy != null)
        {
            var parse_user = real_name_show.split("/");
            var user_folder = parse_user.indexOf('<?php echo $core->user_folder_name;?>');
            for(var i = 0; i <= user_folder; i++)
            {
                delete parse_user[i];
            }
            var show_to_user = cleanArray(parse_user);
            show_to_user = show_to_user.join("/");
            show_to_user = show_to_user.replace("//", "/");
            document.getElementById('selected_move').value = show_to_user+old_name;
            /*if(is_root == "true")
                document.getElementById('selected_move').value = real_name_show+"/"+old_name;
            else
            {
                document.getElementById('selected_move').value = set_back_slashes+real_name_show+"/"+old_name;
            }*/
        }

        var is_user_root = set_back_slashes+real_name_show;
        var check_is_user_root = is_user_root.split("../").length - 1;
        if(check_is_user_root <= 1 && check_is_real_root())
        {
            is_user_root = is_user_root.replace('../', '');
        }
        set_new_name(real_name_show);
    });

    $('#container_id_tree3').fileTree({
        root: map_path,
        script: 'filemanager_user/jqueryFileTree.php?switch=<?php echo @$active;?>',
        expandSpeed: 500,
        collapseSpeed: 500,
        multiFolder: false
    }, function(file) {
        var set_back_slashes = '';
        /*if(is_root == "true")
        {
            var real_name_show = file.replace('../', '');
        }
        else
        {*/
        var real_name_show = file;
        var create_back = here.split("/");
        removeItem(create_back, "");
        create_back = create_back.length;
        var debug = here.split("/");
        removeItem(debug, "");
        create_back -= 2;
        for(var j = 0; j < debug.length; j++)
        {
            if(debug[j] == "" && j != (debug.length - 1))
            {
                create_back += 2;
                create_back -= 3;
                break;
            }
        }

        if(create_back >= 1)
        {
            for(var i = 0; i < create_back; i++)
            {
                set_back_slashes += '../';
            }
        }
        //}

        real_name_show = real_name_show.substring(0, real_name_show.length - 1);
        var selected_copy = document.getElementById('selected_copy');
        if(selected_copy != null)
        {
            var parse_user = real_name_show.split("/");
            var user_folder = parse_user.indexOf('<?php echo $core->user_folder_name;?>');
            for(var i = 0; i <= user_folder; i++)
            {
                delete parse_user[i];
            }
            var show_to_user = cleanArray(parse_user);
            show_to_user = show_to_user.join("/");
            document.getElementById('selected_copy').value = show_to_user+old_name;
            /*if(is_root == "true")
                document.getElementById('selected_copy').value = real_name_show+"/"+old_name;
            else
            {
                document.getElementById('selected_copy').value = set_back_slashes+real_name_show+"/"+old_name;
            }*/
        }
        var is_user_root = set_back_slashes+real_name_show;
        var check_is_user_root = is_user_root.split("../").length - 1;
        if(check_is_user_root <= 1 && check_is_real_root())
        {
            is_user_root = is_user_root.replace('../', '');
        }
        set_new_name(real_name_show);
    });
}

function show_this_dir_file(is_file, is_zip, is_img, name, download)
{
    if(is_file == 0)
    {
        page = 1;
        this_dir_path = name;
        showFileManager(this_dir_path);
    }
    else
    {
        if(is_img == 0) window.open("download.php?show="+download, "download.php?show="+download);
    }
}

function show_config(navigate, is_file, is_zip, name, index, is_editable_file, is_img)
{
    $('#container_id_tree').html("<?php language_filter("What do you want to do now", false, true)?>");
    $("#confLable").html(index);
    $("#confButton").html('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button>');
    if(is_file == 0)
    {
        if(navigate != "")
        {
            var html = $('#container_id_tree').html();
            $('#container_id_tree').html('<p>'+html+'</p><div class="form-group"><label><?php language_filter('Navigate url', false, true)?></label><input type="text" class="form-control" onclick="this.select();" value="'+navigate+'"/></div>');
        }
        <?php echo $core->user_can_folder;?>
    }
    else
    {
        if(navigate != "")
        {
            var html = $('#container_id_tree').html();
            $('#container_id_tree').html('<p>'+html+'</p><div class="form-group"><label><?php language_filter('Download Link', false, true)?></label><input type="text" class="form-control" onclick="this.select();" value="'+navigate+'"/></div>');
        }
        <?php echo $core->user_can_file;?>
        if(is_zip == 1)
        {
            <?php echo $core->user_can_unzip;?>
        }
    }
    var copy_new_name = document.getElementById('copy_new_name');
    if(copy_new_name != null)
    {
        document.getElementById('copy_new_name').value = '';
    }
    var rename_new_name = document.getElementById('rename_new_name');
    if(rename_new_name != null)
    {
        document.getElementById('rename_new_name').value = '';
    }

    $("#showConf").modal("show");
}

function unZip(name, index, time)
{
    if(time == 'first')
    {
        $("#confLable").html('<?php language_filter("Unzip", false, true)?> ' + index);
        $('#container_id_tree').html('<?php language_filter("Do you want to unzip this file", false, true)?>');
        $("#confButton").html('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button><button class="btn btn-inverse" onclick="unZip(\''+name+'\', \''+index+'\', \'u\')"><?php language_filter("Unzip", false, true)?></button>');
    }
    else
    {
        $("#showConf").modal("hide");
        show_preloader();
        setTimeout(function(){
            $.post("filemanager_user/ajax_manage_dir.php",
            {
                un_zip:name,
                path_location:here,
                extra_dir_show:"<?php echo @$active;?>"
            },
            function(data,status)
            {
                if(status == "success")
                {
                    if(data == "true")
                    {
                        loading_from_file = '<?php language_filter("File has been unzipped.", false, true)?>';
                        loading_from_file_status = "green";
                    }
                    else
                    {
                        loading_from_file = '<?php language_filter("File has not been unzipped.", false, true)?>';
                        loading_from_file_status = "red";
                    }
                    showFileManager(here);
                }
                else
                {
                    alert("Error: " + status);
                    hide_preloader();
                }
            });
        }, 1000);
    }
}

function rename_file(name, index, time)
{
    filext = name.substr((Math.max(0, name.lastIndexOf(".")) || Infinity) + 1);
    filext = '.'+filext;
    if(time == "first")
    {
        is_rename = true;
        $("#confLable").html('<?php language_filter("Rename", false, true)?> ' + index);
        $('#container_id_tree').html('');
        $('#container_id_tree').html("<?php language_filter("Write a new name.", false, true)?>");
        $("#confButton").html('<div class="row"><div class="col-xs-6"><input type="text" class="form-control" id="rename_new_name" placeholder="<?php language_filter("New File Name", false, true)?>" onchange="is_rename = true; set_new_name(this.value);"/><input type="hidden" class="input-small" id="rename_new_ext" value="'+filext+'" style="float: left; margin-top: 0px;" onchange="//filext = this.value;"/></div><div class="col-xs-6"><button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button><button class="btn btn-success" onclick="rename_file(\''+name+'\', \''+index+'\', \'r\')"><?php language_filter("Rename", false, true)?></button></div></div>');
    }
    else if(time == "move")
    {
        $("#confLable").html('<?php language_filter("Move", false, true)?> ' + index);
        $('#container_id_tree').html('');
        $('#container_id_tree').html("<?php language_filter("Choose your target directory.", false, true)?>");
        old_name = name.replace(here, "");
        //old_name = old_name.replace(filext, "");
        is_move = true;
        $("#confButton").html('<div class="row"><div class="col-xs-6"><input type="text" class="form-control" id="rename_new_name" placeholder="<?php language_filter("New File Path", false, true)?>" onchange="is_move = true; /*set_new_name(this.value);*/" disabled="disabled"/><input type="hidden" class="input-small" id="rename_new_ext" value="'+filext+'" onchange="//filext = this.value;" disabled="disabled"/></div><div class="col-xs-6"><button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button><button class="btn btn-info" onclick="showInlineTree()"><?php language_filter("Browse", false, true)?></button><button class="btn btn-success" onclick="rename_file(\''+name+'\', \''+index+'\', \'m\')"><?php language_filter("Move", false, true)?></button></div></div>');
    }
    else
    {
        $("#showConf").modal("hide");
        var user_name = new_name;
        var method = "rename";
        if(is_rename) {
            is_rename = false;
        }
        else {
            user_name = user_name.replace( filext, "" );
            user_name = user_name + "/" + old_name;
            is_move = false;
            method = "move";
        }
        show_preloader();
        setTimeout(function(){
            $.post("filemanager_user/ajax_manage_dir.php",
            {
                filename:name,
                newName:user_name,
                move_method:method,
                this_dir_path: here,
                extra_dir_show:"<?php echo @$active;?>"
            },
            function(data,status)
            {
                if(status == "success")
                {
                    if(data == "true")
                    {
                        if(time == 'r')
                            var error_text = "<?php language_filter("File has been renamed.", false, true)?>";
                        if(time == 'm')
                            var error_text = '<?php language_filter("File has been moved.", false, true)?>';
                        loading_from_file = error_text;
                        loading_from_file_status = "green";
                    }
                    else
                    {
                        if(time == 'r')
                            var error_text = "<?php language_filter("File has not been renamed.", false, true)?>";
                        if(time == 'm')
                            var error_text = "<?php language_filter("File has not been moved.", false, true)?>";
                        loading_from_file = error_text;
                        loading_from_file_status = "red";
                    }
                    showFileManager(here);
                }
                else
                {
                    alert("Error: " + status);
                    hide_preloader();
                }
            });
        }, 1000);
    }
}

function copy_file(name, index, time)
{
    filext = name.substr((Math.max(0, name.lastIndexOf(".")) || Infinity) + 1);
    filext = '.'+filext;
    if(time == "first")
    {
        $("#confLable").html('Copy ' + index);
        $('#container_id_tree').html('');
        $('#container_id_tree').html("<?php language_filter("Choose your target directory.", false, true)?>");
        old_name = name.replace(here, "");
        //old_name = old_name.replace(filext, "");
        $("#confButton").html('<div class="row"><div class="col-xs-6"><input type="text" class="form-control" id="copy_new_name" placeholder="<?php language_filter("New Folder Path", false, true)?>" onchange="/*set_new_name(this.value);*/" disabled="disabled"/></div><div class="col-xs-6"><input type="hidden" class="input-small" id="rename_new_ext" value="'+filext+'" style="float: left; margin-top: 0px;" onchange="//filext = this.value;" disabled="disabled"/><button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button><button class="btn btn-info" onclick="showInlineTree()"><?php language_filter("Browse", false, true)?></button><button class="btn btn-success" onclick="copy_file(\''+name+'\', \''+index+'\', \'rename\')"><?php language_filter("Copy", false, true)?></button></div></div>');
    }
    else
    {

        $("#showConf").modal("hide");
        var user_name = new_name;
        user_name = user_name.replace(filext, "");
        user_name = user_name + "/" + old_name;
        show_preloader();
        setTimeout(function(){
            $.post("filemanager_user/ajax_manage_dir.php",
            {
                filename:name,
                newName:user_name,
                copy_this:'ok',
                extra_dir_show:"<?php echo @$active;?>"
            },
            function(data,status)
            {
                if(status == "success")
                {
                    if(data == "true")
                    {
                        loading_from_file = '<?php language_filter("File has been copied.", false, true)?>';
                        loading_from_file_status = "green";
                    }
                    else
                    {
                        loading_from_file = '<?php language_filter("File has not been copied.", false, true)?>';
                        loading_from_file_status = "red";
                    }
                    showFileManager(here);
                }
                else
                {
                    alert("Error: " + status);
                    hide_preloader();
                }
            });
        }, 1000);
    }
}

function remove_file(name, index, time)
{
    if(time == "first")
    {
        $("#confLable").html('<?php language_filter("Remove", false, true)?> ' + index);
        $("#container_id_tree").html("<?php language_filter("Do you want to remove this file", false, true)?>");
        $("#confButton").html('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button><button class="btn btn-danger" onclick="remove_file(\''+name+'\', \''+index+'\',\'rename\')"><?php language_filter("Remove", false, true)?></button>');
    }
    else
    {
        $("#showConf").modal("hide");
        show_preloader();
        setTimeout(function(){
            $.post("filemanager_user/ajax_manage_dir.php",
            {
                removeFileName:name,
                extra_dir_show:"<?php echo @$active;?>"
            },
            function(data,status)
            {
                if(status == "success")
                {
                    if(data == "true")
                    {
                        loading_from_file = '<?php language_filter("File has been deleted.", false, true)?>';
                        loading_from_file_status = "green";
                    }
                    else
                    {
                        loading_from_file = '<?php language_filter("File has not been deleted.", false, true)?>';
                        loading_from_file_status = "red";
                    }
                    showFileManager(here);
                }
                else
                {
                    alert("Error: " + status);
                    hide_preloader();
                }
            });
        }, 1000);
    }
}

function rename_dir(name, index, time)
{
    if(time == "first")
    {
        $("#confLable").html('<?php language_filter("Rename", false, true)?> ' + index);
        $('#container_id_tree').html('');
        $('#container_id_tree').html("<?php language_filter("Write a new name.", false, true)?>");
        $("#confButton").html('<div class="row"><div class="col-xs-6"><input type="text" class="form-control" id="rename_new_name" placeholder="<?php language_filter("New Folder Name", false, true)?>" onchange="is_rename = true; set_new_name(this.value);"/></div><div class="col-xs-6"><button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button><button class="btn btn-success" onclick="rename_dir(\''+name+'\', \''+index+'\', \'r\')"><?php language_filter("Rename", false, true)?></button></div></div>');
    }
    else if(time == "move")
    {
        $("#confLable").html('<?php language_filter("Move", false, true)?> ' + index);
        $('#container_id_tree').html('');
        $('#container_id_tree').html("<?php language_filter("Choose your target directory.", false, true)?>");
        old_name = name.replace(here, "");
        old_name = old_name.replace(filext, "");
        is_move = true;
        $("#confButton").html('<div class="row"><div class="col-xs-6"><input type="text" class="form-control" id="rename_new_name_dir" placeholder="<?php language_filter("New Folder Path", false, true)?>" onchange="is_move = true; /*set_new_name(this.value);*/" disabled="disabled"/></div><div class="col-xs-6"><button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button><button class="btn btn-info" onclick="showInlineTree()"><?php language_filter("Browse", false, true)?></button><button class="btn btn-success" onclick="rename_dir(\''+name+'\', \''+index+'\', \'m\')"><?php language_filter("Move", false, true)?></button></div></div>');
    }
    else
    {
        $("#showConf").modal("hide");
        var user_name = new_name;
        var method = "rename";
        if(is_rename)
        {
            is_rename = false;
        }
        else {
            is_move = false;
            user_name = user_name+"/"+old_name;
            method = "move";
        }
        show_preloader();
        setTimeout(function(){
            $.post("filemanager_user/ajax_manage_dir.php",
            {
                dirname:name,
                newName:user_name,
                this_dir_path:here,
                move_method:method,
                extra_dir_show:"<?php echo @$active;?>"
            },
            function(data,status)
            {
                if(status == "success")
                {
                    if(data == "true")
                    {
                        if(time == 'r')
                            var error_text = "<?php language_filter("Folder has been renamed.", false, true)?>";
                        if(time == 'm')
                            var error_text = "<?php language_filter("Folder has been moved.", false, true)?>";
                        loading_from_file = error_text;
                        loading_from_file_status = "green";
                    }
                    else
                    {
                        if(time == 'r')
                            var error_text = "<?php language_filter("Folder has not been renamed.", false, true)?>";
                        if(time == 'm')
                            var error_text = "<?php language_filter("Folder has not been moved.", false, true)?>";
                        loading_from_file = error_text;
                        loading_from_file_status = "red";
                    }
                    showFileManager(here);
                }
                else
                {
                    alert("Error: " + status);
                    hide_preloader();
                }
            });
        }, 1000);
    }
}

function copy_dir(name, index, time)
{
    if(time == "first")
    {
        $("#confLable").html('<?php language_filter("Copy", false, true)?> ' + index);
        $('#container_id_tree').html('');
        $('#container_id_tree').html("<?php language_filter("Choose your target directory.", false, true)?>");
        old_name = name.replace(here, "");
        old_name = old_name.replace(filext, "");
        $("#confButton").html('<div class="row"><div class="col-xs-6"><input type="text" class="form-control" id="copy_new_name_dir" placeholder="<?php language_filter("New Folder Path", false, true)?>" onchange="/*set_new_name(this.value);*/" disabled="disabled"/></div><div class="col-xs-6"><button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button><button class="btn btn-info" onclick="showInlineTree()"><?php language_filter("Browse", false, true)?></button><button class="btn btn-success" onclick="copy_dir(\''+name+'\', \''+index+'\', \'rename\')"><?php language_filter("Copy", false, true)?></button></div></div>');
    }
    else
    {

        $("#showConf").modal("hide");
        var user_name = new_name + "/" + old_name;
        show_preloader();
        setTimeout(function(){
            $.post("filemanager_user/ajax_manage_dir.php",
            {
                dirname:name,
                newName:user_name,
                copy_this:'ok',
                extra_dir_show:"<?php echo @$active;?>"
            },
            function(data,status)
            {
                if(status == "success")
                {
                    if(data == "true")
                    {
                        loading_from_file = '<?php language_filter("Folder has been copied.", false, true)?>';
                        loading_from_file_status = "green";
                    }
                    else
                    {
                        loading_from_file = '<?php language_filter("Folder has not been copied.", false, true)?>';
                        loading_from_file_status = "red";
                    }
                    showFileManager(here);
                }
                else
                {
                    alert("Error: " + status);
                    hide_preloader();
                }
            });
        }, 1000);
    }
}

function remove_dir(name, index, time)
{
    if(time == "first")
    {
        $("#confLable").html('<?php language_filter("Remove", false, true)?> ' + index);
        $("#container_id_tree").html("<?php language_filter("Do you want to remove this folder", false, true)?>");
        $("#confButton").html('<button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php language_filter("Cancel", false, true)?></button><button class="btn btn-danger" onclick="remove_dir(\''+name+'\', \''+index+'\',\'rename\')"><?php language_filter("Remove", false, true)?></button>');
    }
    else
    {
        $("#showConf").modal("hide");
        show_preloader();
        setTimeout(function(){
            $.post("filemanager_user/ajax_manage_dir.php",
            {
                removeDirName:name,
                extra_dir_show:"<?php echo @$active;?>"
            },
            function(data,status)
            {
                if(status == "success")
                {
                    if(data == "true")
                    {
                        loading_from_file = '<?php language_filter("Folder has been deleted.", false, true)?>';
                        loading_from_file_status = "green";
                    }
                    else
                    {
                        loading_from_file = '<?php language_filter("Folder has not been deleted.", false, true)?>';
                        loading_from_file_status = "red";
                    }
                    showFileManager(here);
                }
                else
                {
                    alert("Error: " + status);
                    hide_preloader();
                }
            });
        }, 1000);
    }
}

function mkdir()
{
    $("#newFolder").modal("hide");
    show_preloader();
    setTimeout(function(){
        $.post("filemanager_user/ajax_manage_dir.php",
        {
            mkdir_path:new_folder_path,
            this_place:here,
            extra_dir_show:"<?php echo @$active;?>"
        },
        function(data,status)
        {
            if(status == "success")
            {
                if(data == "true")
                {
                    loading_from_file = '<?php language_filter("Folder has been created.", false, true)?>';
                    loading_from_file_status = "green";
                }
                else
                {
                    loading_from_file = '<?php language_filter("Folder has not been created.", false, true)?>';
                    loading_from_file_status = "red";
                }
                showFileManager(here);
            }
            else
            {
                alert("Error: " + status);
                hide_preloader();
            }
        });
        new_folder_path = "";
    }, 1000);
}

function set_new_name(name)
{
    if(name == "./..")
    {
        name = "..";
    }
    else
    {
        name = name.replace("./..", "../");
    }
    if(is_rename == true)
    {
        //is_rename = false;
        var check = name.indexOf("/");
        if(check != -1)
        {
            alert("<?php language_filter("Please write new folder/file name.", false, true)?>");
            return false;
        }
    }
    if(is_move == true)
    {
        is_move = false;
        var check = name.indexOf("/");
        if(check == -1)
        {
            alert("<?php language_filter("Please write new folder/file path.", false, true)?>");
            return false;
        }
    }
    var check = name.indexOf("'");
    if(check != -1)
    {
        alert("<?php language_filter("Please don't use quotation in server folders.", false, true)?>");
        return false;
    }
    check = name.indexOf("\"");
    if(check != -1)
    {
        alert("<?php language_filter("Please don't use quotation in server folders.", false, true)?>");
        return false;
    }
    //if(filext != "")
    //{
    var file_ext = document.getElementById('rename_new_ext');
    if(file_ext != null)
        filext = document.getElementById('rename_new_ext').value;
    //}
    new_name = name+filext;
    filext = "";
}

function addslashes(string)
{
    return string.replace(/\\/g, '\\\\').
            replace(/\u0008/g, '\\b').
            replace(/\t/g, '\\t').
            replace(/\n/g, '\\n').
            replace(/\f/g, '\\f').
            replace(/\r/g, '\\r').
            replace(/'/g, '\\\'').
            replace(/"/g, '\\"');
}

function set_new_folder_name(name)
{
    var check = name.indexOf("'");
    if(check != -1)
    {
        alert("<?php language_filter("Please don't use quotation in server folders.", false, true)?>");
        return false;
    }
    check = name.indexOf("\"");
    if(check != -1)
    {
        alert("<?php language_filter("Please don't use quotation in server folders.", false, true)?>");
        return false;
    }
    new_folder_path = name;
}

function navigate_to_path(navigate_to, is_file)
{
    navigate_to = full_replace( navigate_to, "\\", "/" );
    if( is_file == 1 ) {
        navigate_to = navigate_to.split( "/" );
        navigate_to.pop();
        navigate_to = "/"+navigate_to.join( "/" );
    }
    if( navigate_to == "" ) {
        navigate_to = "/";
    }
    navigate_to = navigate_to.replace( "/\\", "/" );
    console.log( navigate_to );
    show_preloader();
    setTimeout(function(){
        hide_preloader();
        showFileManager(navigate_to);
    }, 1000);
}

function full_replace( txt, rep1, rep2 ) {
    txt = txt.split( "" );
    for( var i in txt ) {
        txt[i] = txt[i].replace( rep1, rep2 );
    }
    txt = txt.join("");
    return txt;
}
</script>
<?php
}
?>