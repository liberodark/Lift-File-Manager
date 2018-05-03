<?php
if (!isset($core))
{
    require_once '../filemanager_user_core.php';
    $core = new filemanager_user_core();
    $core->userInfo();
    $core->create_user_panel($core->user_id);
    require_once '../filemanager_language_user.php';
}
if ($core->isLogin())
{
    if($core->is_block == 1)
    {
        echo '<div class="alert alert-info" style="text-align: center"><b>'.language_filter("You are blocked.", true).' <a href="logout.php">'.language_filter("Logout", true).'</a></b></div>';
        exit;
    }
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
                    <img class="img-responsive filemanager-gravatar" src="<?php echo $core->gravatar_src( $core->user_email );?>" />
                    <a class="navbar-brand" href="javascript:;" id="welcome" data-html="true" data-title="" data-delay="0" data-container="body" data-toggle="popover" data-placement="bottom" data-content="" data-trigger="manual"><?php language_filter("Welcome");?>  <?php echo $core->user_firstname." ".$core->user_lastname;?></a>
                    <?php if( in_array( 'edit_profile', $core->user_permissions ) ) { ?><a class="filemanager-nav-profile" id="editProfile" href="javascript:;"><?php language_filter("Edit Profile");?></a><?php } ?>
                    <a class="filemanager-nav-logout" href="logout.php"><i class="glyphicon glyphicon-log-out"></i></a>
                </div>

                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <?php echo $core->user_menu;?>
                    </ul>

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
