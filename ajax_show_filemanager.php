<?php
if (!isset($core))
{
	require_once 'filemanager_core.php';
	$core = new filemanager_core();
    require_once 'option_class.php';
    $option = new option_class();
    require_once 'filemanager_language.php';
}
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
{
    if ($core->isLogin())
    {
        if( isset( $_POST["showFilemanager"] ) and isset( $_POST["my_dir_path"] ) )
        {
            $core->adminInfo();
            if ($_POST["showFilemanager"] == $core->admin_id)
            {
                $this_dir = $_POST["my_dir_path"];
                $path = ROOT_DIR_PATH.$this_dir;
                $path = str_replace( "//", "/", $path );
                if(isset($_POST["sort_type"]))
                {
                    $sort_with = $_POST["sort_type"];
                }
                else
                {
                    $sort_with = 'date';
                }
                if(isset($_POST["page"]))
                {
                    $page = (int) $_POST["page"];
                }
                else
                {
                    $page = 1;
                }
                if(isset($_POST["countShow"]))
                {
                    $countShow = $_POST["countShow"];
                }
                else
                {
                    $countShow = 10;
                }
                $search = '';
                if(isset($_POST["search"]))
                {
                    $search = $_POST["search"];
                }



                $filemanager = new filemanager( $path, $sort_with, $search );
                $navigation_url = str_replace("ajax_show_filemanager.php", "navigate.php", $filemanager->curPageURL());
                $download_url = str_replace("ajax_show_filemanager.php", "download.php", $filemanager->curPageURL());
                $fullCount = count($filemanager->show_files_folders);
                $core->page($page, $fullCount, $countShow);
                $load_modals = true;
                $download_link = $option->get_option( "settings" );
                if( isset( $download_link->download_link ) ) {
                    if( $download_link->download_link == "on" ) {
                        $download_link = true;
                    }
                    else {
                        $download_link = false;
                    }
                }
                else {
                    $download_link = false;
                }
?>

    <!-- START HTML -->
    <?php $core->create_breadcrumb( $path );?>
    <div class="row">
<?php
        if( $search != "" ) {
?>
            <div class="col-md-12">
                <div class="alert alert-success text-center" style="font-weight: bold">
                    <?php echo language_filter("Search results for", true)." ".$search;?>
                </div>
            </div>
<?php
        }
?>
        <div class="col-md-3 col-sm-12">
            <h3 class="site_map_title"><?php echo language_filter( "Site Map" );?></h3>
            <div id="container">
                <div id="tree"></div>
            </div>
        </div>

        <div class="col-md-9">
            <h3><?php echo language_filter( "Directory" );?></h3>
            <?php
            if( $search == '' ) {
            ?>

            <nav class="navbar navbar-default" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse option-navbar" id="navbar-collapse-2">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle text-center" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-sort"></i><br />
                                <?php language_filter("Sort by");?>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li class="<?php if($sort_with == 'date') echo 'active';?>"><a href="javascript:;" onclick="my_sort = 'date'; showFileManager('<?php echo addslashes($this_dir);?>');"><?php language_filter("Sort date");?></a></li>
                                <li class="<?php if($sort_with == 'name') echo 'active';?>"><a href="javascript:;" onclick="my_sort = 'name'; showFileManager('<?php echo addslashes($this_dir);?>');"><?php language_filter("Sort name");?></a></li>
                            </ul>
                        </li>
                        <li><a href="#newFolder" class="text-center" data-toggle="modal" data-target="#newFolder"><i class="glyphicon glyphicon-folder-open"></i><br /><?php language_filter("New Folder");?></a></li>
                        <li><a href="javascript:;" class="text-center" onclick="$('#container_id_tree3').html('<div class=\'alert alert-info\'><center><b><?php language_filter("Choose your target directory.", false, true);?></b></center></div>'); if(selected == '' || selected == null) alert('<?php language_filter("Please select files and folders", false, true);?>'); else $('#copySelected').modal('show');"><i class="glyphicon glyphicon-book"></i><br /><?php language_filter("Copy");?></a></li>
                        <li><a href="javascript:;" class="text-center" onclick="$('#container_id_tree2').html('<div class=\'alert alert-info\'><center><b><?php language_filter("Choose your target directory.", false, true);?></b></center></div>'); if(selected == '' || selected == null) alert('<?php language_filter("Please select files and folders", false, true);?>'); else $('#moveSelected').modal('show');"><i class="glyphicon glyphicon-fullscreen"></i><br /><?php language_filter("Move");?></a></li>
                        <li><a href="javascript:;" class="text-center" onclick="if(selected == '' || selected == null) alert('<?php language_filter("Please select files and folders", false, true);?>'); else check_selected_files_folders();"><i class="glyphicon glyphicon-remove"></i><br /><?php language_filter("Remove");?></a></li>
                        <li><a href="javascript:;" class="text-center" onclick="if(selected == '' || selected == null) alert('<?php language_filter("Please select files and folders", false, true);?>'); else $('#newzipFile').modal('show');"><i class="glyphicon glyphicon-compressed"></i><br /><?php language_filter("Zip");?></a></li>
                        <li><a href="#uploader" class="text-center" onclick="showUploader('<?php echo addslashes($path);?>')" data-toggle="modal" data-target="#uploader"><i class="glyphicon glyphicon-cloud-upload"></i><br /><?php language_filter("Upload");?></a></li>
                        <li><a href="javascript:;" class="text-center" onclick="if(selected == '' || selected == null) alert('<?php language_filter("Please select files and folders", false, true);?>'); else $('#download_files').modal('show');"><i class="glyphicon glyphicon-cloud-download"></i><br /><?php language_filter("Download");?></a></li>
                        <li><a href="javascript:;" class="text-center" onclick="if(selected == '' || selected == null) alert('<?php language_filter("Please select files and folders", false, true);?>'); else $('#newbackupFile').modal('show');"><i class="glyphicon glyphicon-floppy-save"></i><br /><?php language_filter("Backup");?></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle text-center" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-share"></i><br />
                                <?php language_filter("Share");?>
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:;" onclick="if(selected == '' || selected == null) alert('<?php language_filter("Please select files and folders", false, true);?>'); else $('#share_files').modal('show');"><?php language_filter("share_via_email");?></a></li>
                                <li><a href="javascript:;" onclick="if(selected == '' || selected == null) alert('<?php language_filter("Please_select_files_for_share", false, true);?>'); else $('#share_system_files').modal('show');"><?php language_filter("share_in_system");?></a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->

            </nav>
            <?php
            }
            ?>
            <div class="table-responsive">
                <table class="table">
            <?php
            if( $fullCount == '' or $fullCount == 0 ) {
                echo '<div class="alert alert-info" style="margin-top: 10px"><center><b>'.language_filter("NO FILES AND FOLDERS", true).'</b></center></div>';
            }
            else {
                echo '<tr>
                        <th style="text-align: center;"><button class="btn btn-default btn-xs select-btn-filemanager" onclick="select_all('.$core->start.', '.$core->end.')" id="select_all">'.language_filter("Select All", true).'</button></th>
                        <th style="">'.language_filter("Open / Download / View", true).'</th>
                        <th style="text-align: center;">'.language_filter("Size", true).'</th>
                        <th style="text-align: center;">'.language_filter("Last Activity Time", true).'</th>
                        <th style="text-align: center;">'.language_filter("Setting", true).'</th>
                     </tr>';
                $root_path_dir = realpath( ROOT_DIR_PATH );
                for ($i = $core->start; $i < $core->end; $i++)
                {
                    $file_path = $filemanager->show_files_folders[$i];
                    $search_path = "";
                    $search_check = "";
                    if( $search != '' ) {
                        $search_path = str_replace( $root_path_dir, "", $file_path );
                        $search_check = ltrim ($search_path, '/');
                        if( in_array( $search_check, $filemanager->ignored ) ) continue;
                    }
                    $fileSize = $filemanager->formatBytes( $file_path );
                    $fileTime = date ( "Y/m/d H:i:s", filemtime( $file_path ) );
                    $filename = basename( $filemanager->show_files_folders[$i] );
                    $little_file_path = $this_dir."/".$filename;
                    $is_img = 0;
                    $is_file = 0;
                    $is_zip = 0;
                    $is_pdf = 0;
                    $is_editable_file = 0;
                    $navigate = "";
                    if( is_dir( $file_path ) ) {
                        $navigate = $navigation_url."?redirect=".base64_encode(utf8_encode($little_file_path));
                    }
                    else {
                        if( $download_link ) {
                            $navigate = $download_url."?fid=".base64_encode(utf8_encode($little_file_path));
                        }
                        if( $search != '' ) {
                            $search_path = explode( "/", $search_path );
                            array_pop( $search_path );
                            $search_path = implode( "/", $search_path );
                        }
                        $is_file = 1;
                        $ext = pathinfo($file_path, PATHINFO_EXTENSION);
                        $ext = strtolower($ext);
                        if( $ext == "zip" ) {
                            $is_zip = 1;
                        }

                        if( $ext == "txt" ) {
                            if( is_writable( $filename ) ) {
                                $is_editable_file = 1;
                            }
                        }

                        if( $ext == "jpg" or $ext == "png" or $ext == "gif" or $ext == "jpeg" ) {
                            $is_img = 1;
                        }

                        if( $ext == "pdf" ) {
                            $is_pdf = 1;
                            $is_editable_file = 1;
                        }
                    }
?>
                    <tr id="row<?php echo $i;?>">
                        <td style="text-align: center;"><input type="checkbox" id="check_<?php echo $i;?>" value="<?php echo $file_path;?>" onclick="set_selected( this.value, <?php echo $i;?>, this.checked );"></td>
                        <td><a href='<?php if($is_img == 1) echo "img.php?filename=".base64_encode(utf8_encode($little_file_path)); else echo "javascript:;";?>' <?php if($is_img == 1) echo "class='image_show' rel='group1'";?> onclick="show_this_dir_file('<?php echo $is_file;?>', '<?php echo $is_zip;?>', '<?php echo $is_img;?>', '<?php echo addslashes($little_file_path);?>', '<?php echo base64_encode(utf8_encode($little_file_path));?>', '<?php echo addslashes( $search );?>')" style="<?php if($is_file == 0) echo 'color: #9933ff;'; else echo 'color: #3366cc;';?>"><img src="<?php if($is_file == 1 and $is_zip == 1) echo 'filemanager_assets/img.php?img=zip'; elseif($is_file == 0) echo 'filemanager_assets/img.php?img=directory'; else if($is_file == 1 and $is_img == 1) echo 'filemanager_assets/img.php?img=picture'; else if( $is_pdf == 1 ) echo 'filemanager_assets/img.php?img=pdf'; else echo 'filemanager_assets/img.php?img=file';?>" style="margin-right: 5px;" /><?php echo $filename;?></a></td>
                        <td class="text-center"><?php echo $fileSize;?></td>
                        <td class="text-center"><?php echo $fileTime;?></td>
                        <td class="text-center">
                        <?php
                        if( $search != '' ) {
                            echo '<button type="button" class="btn btn-primary btn-xs" onclick="showFileManager(\''.addslashes($search_path).'\'); " >'.language_filter("Go to directory", true).'</button>';
                        }
                        else {
                        ?>
                        <button class="btn btn-xs btn-info" type="button" onclick="show_config('<?php echo addslashes($navigate);?>', '<?php echo $is_file;?>', '<?php echo $is_zip;?>', '<?php echo addslashes($little_file_path);?>', '<?php echo addslashes($filename);?>', '<?php echo $is_editable_file;?>', '<?php echo $is_img;?>')"><span class="glyphicon glyphicon-cog"></span></button>
                        <a href="javascript:;" onclick="<?php if($is_file == 0 or $is_img == 1 and ( $is_pdf == 0 ) ) echo 'javascript:;'; elseif($is_img == 0 and $is_editable_file == 0){ echo 'javascript:;';} else{ if($is_editable_file == 1 or $is_pdf == 1) echo "show_edit_file_box('".base64_encode(utf8_encode($file_path))."', '".addslashes($filename)."', '".$is_pdf."')";}?>" class="btn btn-xs btn-info" type="button" <?php if($is_file == 0 or $is_img == 1) echo 'disabled="disabled"'; else{if($is_img == 0 and $is_editable_file == 0) echo 'disabled="disabled"';}?> ><span class="glyphicon glyphicon-edit"></span></a>
                        <?php
                        }
                        ?>
                        </td>
                    </tr>
<?php
                }
            }
?>
                </table>

                <!-- Start pagination -->
                <ul class="pagination pagination-sm pull-right">
                    <?php
                    for($j = 1; $j <= $core->pageCount; $j++)
                    {
                        ?>
                        <li <?php if($j == $page) echo 'class="active"';?> onclick="page = <?php echo $j;?>; loading_from_file = false; showFileManager(here);"><a href="javascript:;"><?php echo $j;?></a></li>
                        <?php
                    }
                    ?>
                    <li>
                        <div class="btn-group btn-group-sm">
                            <button type="button" style="border-left: 0; border-radius: 0; border-top-right-radius: 4px; border-bottom-right-radius: 4px;" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li <?php if($countShow == 10) echo 'class="active"';?> onclick="page = <?php echo $page;?>; loading_from_file = false; countShow = 10; showFileManager(here);"><a href="javascript:;">10</a></li>
                                <li <?php if($countShow == 20) echo 'class="active"';?> onclick="page = <?php echo $page;?>; loading_from_file = false; countShow = 20; showFileManager(here);"><a href="javascript:;">20</a></li>
                                <li <?php if($countShow == 100) echo 'class="active"';?> onclick="page = <?php echo $page;?>; loading_from_file = false; countShow = 100; showFileManager(here);"><a href="javascript:;">100</a></li>
                                <li class="divider"></li>
                                <li <?php if($countShow == "all") echo 'class="active"';?> onclick="page = 1; loading_from_file = false; countShow = 'all'; showFileManager(here);"><a href="javascript:;"><?php language_filter("Show All");?></a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
                <!-- End pagination -->
            </div>
        </div>
    </div>
    <!-- END HTML -->

    <!-- START JS -->
    <script type="text/javascript">
        var all_loaded_files = new Array(<?php if(is_array($filemanager->show_files_folders)) {for ($j = $core->start; $j < $core->end; $j++){ if($j == ($core->end - 1)){echo "\"".addslashes($filemanager->show_files_folders[$j])."\"";}else{echo "\"".addslashes($filemanager->show_files_folders[$j])."\", ";} } }?>);
        var here = "<?php echo addslashes( $this_dir );?>";
        var new_name = "";
        var filext = "";
        var is_rename = false;
        var is_move = false;
        var navigate_to = "";
        var send_to_counter = 1;
        var selected = new Array();
        var zip_file_name = "";
        var send_to_counter = 1;
        var supported_ext = "<?php echo implode( ";", $filemanager->support_ext );?>";
        supported_ext = supported_ext.split(";");
    </script>

    <!-- END JS -->

    <!-- START MODALS -->
    <?php require_once 'modals.php';?>
    <!-- END MODALS -->


<?php
            }
        }
    }
    else
    {
        header("Status: 404 Not Found");
    }
}
else
{
    header("Status: 404 Not Found");
}
?>