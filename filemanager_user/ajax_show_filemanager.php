<?php
if (!isset($core))
{
    require_once '../filemanager_user_core.php';
    $core = new filemanager_user_core();
    $core->userInfo();
    $core->create_user_panel($core->user_id);
    require_once '../filemanager_language_user.php';
}
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
{
    if ($core->isLogin() and $core->is_block == 0)
    {
        if(isset($_POST["showFilemanager"]))
        {
            if ($_POST["showFilemanager"] == $core->user_id)
            {
                $this_dir = $_POST["my_dir_path"];
                $path = $core->system_root_dir.ROOT_DIR_PATH.$this_dir;
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

                $filemanager = new user_filemanager( $path, $sort_with, $core->user_id, $search );
                $navigation_url = str_replace("ajax_show_filemanager.php", "navigate.php", $filemanager->curPageURL());
                $download_url = str_replace("ajax_show_filemanager.php", "download.php", $filemanager->curPageURL());

                $fullCount = count($filemanager->show_files_folders);
                $core->page($page, $fullCount, $countShow);
                $settings = $core->get_option("settings");
                $download_link = false;
                $system_share = false;
                if( isset( $settings->download_link ) ) {
                    if( $settings->download_link == "on" ) {
                        $download_link = true;
                    }
                }
                if( isset( $settings->system_share ) ) {
                    if( $settings->system_share ) {
                        $system_share = true;
                    }
                }
                $user_modals = true;
                $active = 0;
                if( isset($_POST["extra_dir_show"]) ) {
                    $active = (int) mysql_real_escape_string( $_POST["extra_dir_show"] );
                }
?>

<?php $core->create_breadcrumb( $path );?>
<?php
if( $search != "" ) {
?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success" style="text-align: center; font-weight: bold">
                <?php echo language_filter("Search results for", true)." ".$search;?>
            </div>
        </div>
    </div>
<?php
}
?>
<div class="row">

    <div class="col-md-3 col-sm-12">
        <h3 class="site_map_title"><?php echo language_filter( "Site Map" );?></h3>
        <div id="container" role="main">
            <div id="tree"></div>
        </div>
    </div>

    <div class="col-md-9 col-sm-12">
    <h3><?php echo language_filter( "Directory" );?>
    <?php
    if( !empty( $core->user_permissions ) ) {
    ?>
        <small><a href="javascript:;" style="margin-top: 7px" class="pull-right" id="show_limit" onclick="$('#limit').slideDown(500);"><?php language_filter("Used Size")?></a></small>
    <?php
    }
    ?>
    </h3>

        <!-- Nav tabs -->
        <?php
        $core->set_user_tabs( $core->user_id );
        $core->create_extra_tabs( $active );
        ?>
        <?php
            if($search == '')
            {
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

                    <div class="collapse navbar-collapse option-navbar" id="navbar-collapse-2">
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle text-center" data-toggle="dropdown">
                                    <i class="glyphicon glyphicon-sort"></i><br />
                                    <?php language_filter("Sort by");?>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="<?php if($sort_with == 'date') echo 'active';?>"><a href="javascript:;" onclick="my_sort = 'date'; showFileManager('<?php echo addslashes($this_dir);?>');"><?php language_filter("Sort date");?></a></li>
                                    <li class="<?php if($sort_with == 'name') echo 'active';?>"><a href="javascript:;" onclick="my_sort = 'name'; showFileManager('<?php echo addslashes($this_dir);?>');"><?php language_filter("Sort name");?></a></li>
                                </ul>
                            </li>
                            <?php echo $core->user_can;?>
                            <?php
                            if( $settings->share == "on" or $system_share == true )
                            {
                            ?>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle text-center" data-toggle="dropdown">
                                        <i class="glyphicon glyphicon-share"></i><br />
                                        <?php language_filter("Share");?>
                                        <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php
                                        if( $settings->share == "on" )
                                        {
                                        ?>
                                            <li><a href="javascript:;" onclick="if(selected == '' || selected == null) alert('<?php language_filter("Please select files and folders", false, true);?>'); else $('#share_files').modal('show');"><?php language_filter("share_via_email");?></a></li>
                                        <?php
                                        }
                                        if( $system_share == true )
                                        {
                                        ?>
                                            <li><a href="javascript:;" onclick="if(selected == '' || selected == null) alert('<?php language_filter("Please_select_files_for_share", false, true);?>'); else $('#share_system_files').modal('show');"><?php language_filter("share_in_system");?></a></li>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                </li>
                            <?php
                            }
                            ?>
                            <li><a href="javascript:;" class="text-center" onclick="if(selected == '' || selected == null) alert('<?php language_filter("Please select files and folders", false, true);?>'); else $('#download_files').modal('show');"><i class="glyphicon glyphicon-cloud-download"></i><br /><?php language_filter("Download");?></a></li>
                        </ul>
                    </div>

                </nav>
            <?php
            }
        ?>

        <div class="alert alert-warning" id="limit" style="<?php if($core->user_limitation <= 100) echo 'display: none;';?>">
            <button type="button" class="close" onclick="$('#limit').slideUp(500);">&times;</button>
            <br />
            <p><?php language_filter("Used Size")?> (<?php if($core->user_limitation < 100) echo intval($core->user_limitation)."%"; else echo '100%';?>)</p>
            <div class="progress progress-striped">
                <div class="progress-bar <?php if($core->user_limitation < 50) echo 'progress-bar-success'; elseif($core->user_limitation > 50 and $core->user_limitation < 90) echo 'progress-bar-warning'; else{ echo 'progress-bar-danger'; }?>" style="width: <?php echo $core->user_limitation;?>%">
                    <span></span>
                </div>
            </div>
            <?php
            if($core->user_limitation > 100)
            {
                language_filter("USER LIMITATION ERROR");
            }
            ?>
        </div>

    <div class="table-responsive">
    <table class="table table-striped">
        <?php
        if($fullCount == '')
        {
            echo '<div class="alert alert-info" style="text-align: center; font-weight: bold;">'.language_filter("NO FILES AND FOLDERS", true).'</div>';
        }
        else
        {
            echo '<tr>
                        <th style="text-align: center;"><button class="btn btn-default btn-xs" onclick="select_all()" id="select_all">'.language_filter("Select All", true).'</button></th>
                        <th style="">'.language_filter("Open / Download / View", true).'</th>
                        <th style="text-align: center;">'.language_filter("Size", true).'</th>
                        <!--<th style="text-align: center;">'.language_filter("Permission", true).'</th>-->
                        <th style="text-align: center;">'.language_filter("Last Activity Time", true).'</th>
                        <th style="text-align: center;">'.language_filter("Setting", true).'</th>
                     </tr>';
            for ($i = $core->start; $i < $core->end; $i++)
            {
                $navigate = "";
                if( $search != '' ) {
                    $file_path = $filemanager->show_files_folders[$i];
                    $little_file_path = $core->user_folder_name."/".str_replace( $core->user_dir, "", $filemanager->show_files_folders[$i]);
                    $fileSize = $filemanager->formatBytes($file_path);
                    $fileTime = date ("Y/m/d H:i:s", filemtime($file_path));
                }
                else {
                    $file_path =  $path.'/'.$filemanager->show_files_folders[$i];
                    $little_file_path = $this_dir."/".$filemanager->show_files_folders[$i];
                    $fileSize = $filemanager->formatBytes($file_path);
                    $fileTime = date ("Y/m/d H:i:s", filemtime($file_path));
                }

                $is_zip = 0;
                $is_pdf = 0;

                if(is_dir($file_path))
                {
                    $is_file = 0;
                    $is_editable_file = 0;
                    $is_img = 0;
                    $navigate = $navigation_url."?redirect=".base64_encode(utf8_encode($file_path));
                    if( $active != 0 ) {
                        $navigate = $navigate."&switch=".$active;
                    }
                }
                else
                {
                    $is_file = 1;
                    $is_editable_file = 0;
                    $is_img = 0;
                    $ext = pathinfo($file_path, PATHINFO_EXTENSION);
                    $ext = strtolower($ext);
                    if($ext == "zip")
                    {
                        $is_zip = 1;
                    }

                    if($ext == "txt")
                    {
                        if(is_writable($file_path))
                        {
                            $is_editable_file = 1;
                        }
                    }

                    if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext == "jpeg")
                    {
                        $is_img = 1;
                    }

                    if( $ext == "pdf" ) {
                        $is_pdf = 1;
                        $is_editable_file = 1;
                    }
                    if( $download_link ) {
                        $download_path = str_replace( $core->system_root_dir.ROOT_DIR_PATH, "", $file_path );
                        $_active = "";
                        if( $active != 0 and $active != "" ) $_active = "&switch=".$active;
                        $navigate = $download_url."?fid=".base64_encode(utf8_encode($download_path)).$_active;
                    }
                }
                ?>
                <tr id="row<?php echo $i;?>">
                    <td style="text-align: center;"><input type="checkbox" id="check_<?php echo $i;?>" value="<?php echo $filemanager->show_files_folders[$i];?>" onclick="set_selected(this.value, <?php echo $i;?>, this.checked);"></td>
                    <td style=""><a href='<?php if($is_img == 1) echo "filemanager_user/img.php?filename=".base64_encode(utf8_encode($file_path))."&switch=".$active; else echo "javascript:;";?>' <?php if($is_img == 1) echo "class='image_show' rel='group1'";?> onclick="show_user_dir_file('<?php echo $is_file;?>', '<?php echo $is_zip;?>', '<?php echo $is_img;?>', '<?php echo addslashes($little_file_path);?>', '<?php echo base64_encode(utf8_encode($little_file_path))?>', '<?php echo $search?>', '<?php echo $active;?>')" style="<?php if($is_file == 0) echo 'color: #9933ff;'; else echo 'color: #3366cc;';?>"><img src="<?php if($is_file == 1 and $is_zip == 1) echo 'filemanager_assets/img.php?img=zip'; elseif($is_file == 0) echo 'filemanager_assets/img.php?img=directory'; else if($is_file == 1 and $is_img == 1) echo 'filemanager_assets/img.php?img=picture'; else echo 'filemanager_assets/img.php?img=file';?>" style="margin-right: 5px;" /><?php if($search != '') echo end(explode("/", $filemanager->show_files_folders[$i])); else echo $filemanager->show_files_folders[$i];?></a></td>
                    <td style="text-align: center;"><?php echo $fileSize;?></td>
                    <!--<td style="text-align: center;"><?php /*echo $filePerm[$i];*/?></td>-->
                    <td style="text-align: center;"><?php echo $fileTime;?></td>
                    <td style="text-align: center;">
                        <?php
                        if($search != '')
                        {
                            echo '<button type="button" class="btn btn-primary btn-xs" onclick="navigate_to_path(\''.addslashes($little_file_path).'\', \''.$is_file.'\'); " >'.language_filter("Go to directory", true).'</button>';
                        }
                        else
                        {
                        ?>
                            <button class="btn btn-xs btn-info" type="button" onclick="show_config('<?php echo addslashes($navigate);?>', '<?php echo $is_file;?>', '<?php echo $is_zip;?>', '<?php echo addslashes($this_dir.'/'.$filemanager->show_files_folders[$i]);?>', '<?php echo addslashes($filemanager->show_files_folders[$i]);?>', '<?php echo $is_editable_file;?>', '<?php echo $is_img;?>')"><span class="glyphicon glyphicon-cog"></span></button>
                            <a href="javascript:;" onclick="<?php if($is_file == 0) echo 'javascript:;';elseif($is_pdf == 1) echo "show_pdf_file( '".base64_encode(utf8_encode($file_path))."', '".addslashes($filemanager->show_files_folders[$i])."' );"; elseif($is_editable_file == 1 and $core->user_can_edit_file == false){echo 'javascript:;';}elseif($is_img == 1 and $core->user_can_edit_image == false){echo 'javascript:;';} else{ if($is_editable_file) echo "show_edit_file( '".base64_encode(utf8_encode($file_path))."', '".addslashes($filemanager->show_files_folders[$i])."' );";}?>" class="btn btn-xs btn-info" type="button" <?php if($is_file == 0) echo 'disabled="disabled"'; elseif( $is_pdf == 1 ) echo ''; elseif($is_editable_file == 1 and $core->user_can_edit_file == false){echo 'disabled="disabled"';}elseif($is_img == 1){echo 'disabled="disabled"';} else{if($is_img == 0 and $is_editable_file == 0) echo 'disabled="disabled"';}?> ><span class="glyphicon glyphicon-edit"></span></a>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr>

            </tr>
        </table>
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
        </div>
    </div>
<?php
    }
?>
</div>


<script src="filemanager_js/jqueryFileTree.js"></script>
<script type="text/javascript">
    $(".image_show").fancybox({
        'hideOnContentClick': true,
        'type': "image"
    });
    new_name = "";
    filext = "";
    this_dir_path = "";
    this_file_path = "";
    here = "<?php echo $this_dir;?>";
    is_rename = false;
    is_move = false;
    new_folder_path = "";
    selected = new Array();
    zip_file_name = "";
    this_dir_selected = "";
    old_name = "";
    new_file_per = "";
    navigate_to = "";
    send_to_counter = 1;
    var supported_ext = "<?php echo implode( ";", $filemanager->support_ext );?>";
    supported_ext = supported_ext.split(";");
    map_path = '<?php echo addslashes($core->user_dir);?>';
    var root_name = "<?php echo @ltrim( addslashes($core->user_dir), $core->system_root_dir.ROOT_DIR_PATH );?>";
    <?php
    if(is_array($filemanager->show_files_folders))
    {
        if(!empty($filemanager->show_files_folders))
        {
            $all_files_folders = implode(", ", $filemanager->show_files_folders);
        }
    }
    else
    {
        $all_files_folders = "";
    }
    ?>

</script>
<?php
            require_once 'modals.php';

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