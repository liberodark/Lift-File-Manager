<?php
if (!isset($core))
{
    require_once 'filemanager_core.php';
    $core = new filemanager_core();
    require_once 'filemanager_language.php';
}
if ($core->isLogin())
{
    $core->adminInfo();
    ?>
<body>

<div class="container">
    <!-- Static navbar -->
    <div class="navbar navbar-inverse filemanager-nav" role="navigation" style="z-index: 0;">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <img class="img-responsive filemanager-gravatar" src="<?php echo $core->gravatar_src( $core->admin_email );?>" />
                <a class="navbar-brand" href="javascript:;" id="welcome" data-html="true" data-title="" data-delay="0" data-container="body" data-toggle="popover" data-placement="bottom" data-content="" data-trigger="manual"><?php language_filter("Welcome");?>  <?php echo $core->admin_firstname." ".$core->admin_lastname;?></a>
                <a class="filemanager-nav-profile" id="editProfile" href="javascript:;"><?php language_filter("Edit Profile");?></a>
                <a class="filemanager-nav-logout" href="logout.php"><i class="glyphicon glyphicon-log-out"></i></a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="active" id="fileManager"><a href="javascript:;"><center><i class="glyphicon glyphicon-th menu-icons"></i></center><?php language_filter("File Manager");?></a></li>
                    <li id="homeMenu"><a href="javascript:;"><center><i class="glyphicon glyphicon-floppy-disk menu-icons"></i></center><?php language_filter("Backup Files");?></a></li>
                    <li id="setting"><a href="javascript:;"><center><i class="glyphicon glyphicon-cog menu-icons"></i></center><?php language_filter("General Setting");?></a></li>
                    <!--<li id="addUser"><a href="javascript:;" ><center><i class="glyphicon glyphicon-th menu-icons"></i></center><?php /*language_filter("Add User");*/?></a></li>-->
                    <li id="users"><a href="javascript:;" ><center><i class="glyphicon glyphicon-user menu-icons"></i></center><?php language_filter("Users");?></a></li>
                    <li id="tickets"><a href="javascript:;" onclick="show_what = 'all'; ticket_page = 1;"><center><i class="glyphicon glyphicon-comment menu-icons"></i></center><?php language_filter("Tickets");?></a></li>
                    <li id="shared_files"><a href="javascript:;" onclick="show_what = 'all'; share_page = 1;"><center><i class="glyphicon glyphicon-share menu-icons"></i></center><?php language_filter("system_share");?></a></li>
                </ul>

                <div class="full-with">
                    <form class="navbar-form <?php if($language["direction"] == "ltr") echo 'navbar-right'; else echo 'navbar-left'; ?>" action="javascript:;" method="post" onsubmit="return false;" role="search">
                        <div class="input-group">
                            <?php
                            if($language["direction"] == "ltr") {
                            ?>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" onclick="search = $('#searchinput').val(); if(search == '') {alert('<?php language_filter("Please write a file name", false, true)?>'); return false;} page = 1; loading_from_file = false; countShow = 'all'; showFileManager(here);"><i class="glyphicon glyphicon-search"></i></button>
                            </span>
                            <?php
                            }
                            ?>
                            <input type="text" class="form-control" id="searchinput">
                            <?php
                            if($language["direction"] == "rtl") {
                            ?>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" onclick="search = $('#searchinput').val(); if(search == '') {alert('<?php language_filter("Please write a file name", false, true)?>'); return false;} page = 1; loading_from_file = false; countShow = 'all'; showFileManager(here);"><i class="glyphicon glyphicon-search"></i></button>
                            </span>
                            <?php
                            }
                            ?>
                        </div>
                    </form>
                </div>

            </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
    </div>
    <?php
}
else
{
    header("Location: .");
}
?>
