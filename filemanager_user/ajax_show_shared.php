<?php
    if (!isset($core))
    {
        require_once '../filemanager_user_core.php';
        $core = new filemanager_user_core();
        require_once '../filemanager_language_user.php';
    }
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
{
    if ($core->isLogin())
    {
        if (isset($_POST["showShared"]))
        {
            $core->userInfo();
            if ($_POST["showShared"] == $core->user_id)
            {
                $user = $_POST["show_what"];
                $page = $_POST["share_page"];
                $role = $_POST["share_role"];
                $shared = $core->get_shared_files( $page, $user, $role );
                if( $shared == "" and !isset( $_POST["extra_page"] ) ) {
?>
                <div class="alert alert-info text-center" style="font-weight: bold;"><?php language_filter( "No_Share" );?></div>
<?php
                    exit();
                }

                if( !isset( $_POST["extra_page"] ) ) {
?>
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <?php language_filter( "filter" );?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li <?php if( $user == $core->user_id ) echo 'class="active"';?>>
                            <a href="javascript:;" onclick="show_what = '<?php echo $core->user_id;?>'; share_page = 1; share_role = 'user'; $('#shared_files').click();"><span class="importo"><?php language_filter("my_shared_files")?></span></a>
                        </li>
                        <li <?php if( $user == "all" ) echo 'class="active"';?>>
                            <a href="javascript:;" onclick="show_what = 'all'; share_page = 1; share_role = 'user'; $('#shared_files').click();"><span class="importo"><?php language_filter("All_Files")?></span></a>
                        </li>
                    </ul>
                </div>
<?php
                }

?>
                <div id="timeline" style="padding-left: 18px; padding-right: 18px">
<?php
                if( !isset( $_POST["extra_page"] ) ) {
?>
                    <div class="row timeline-movement timeline-movement-top">
                        <div class="timeline-badge timeline-future-movement">
                            <a href="javascript:;">
                                <span class="glyphicon glyphicon-time"></span>
                            </a>
                        </div>
                    </div>
<?php
                }
                if( !isset( $shared["id"] ) )
                {
                    echo '</div><div class="alert alert-info text-center" style="font-weight: bold; margin-top: 10px">'.language_filter( "No_Share", true ).'</div>';
                    exit();
                }
                $count = count($shared["id"]);
                $counter = 0;
                $month = @$language["MONTH"];
                for( $i = 0; $i < $count; $i++ ) {
                    $user_id = $shared["user_id"][$i];
                    if( $counter == 0 ) {
                        echo '<div class="row timeline-movement">';
                        $month_show = date( "m", strtotime( $shared["date_added"][$i] ) );
                        $month_show = (int) $month_show;
                        echo '
                        <div class="timeline-badge">
                            <span class="timeline-balloon-date-day">'.date( "d", strtotime( $shared["date_added"][$i] ) ).'</span>
                            <span class="timeline-balloon-date-month">'.strtoupper( $month[ $month_show ] ).'</span>
                        </div>';
                    }
?>

                        <div class="col-sm-6  timeline-item" >
                            <div class="row" id="timeline_<?php echo $shared["id"][$i];?>">
                                <div class="<?php if( $counter == 0) echo 'col-sm-11 remove_checker'; else echo 'col-sm-offset-1 col-sm-11 remove_checker';?>">
                                    <div class="timeline-panel <?php if( $counter == 0 ) echo 'credits'; else echo 'debits';?>">
                                        <div class="row" style="padding: 0">
                                            <div class="col-md-3">
                                                <img class="img-responsive img-rounded" src="<?php echo $shared["gravatar"][$i];?>" />
                                            </div>
                                            <div class="col-md-9">
                                                <ul class="timeline-panel-ul">
                                                    <li>
                                                        <a href="javascript:;" class="share_fullname" onclick="share_role = '<?php echo $shared["role"][$i]?>'; show_what = '<?php echo $shared["user_id"][$i];?>'; share_page = 1; $('#shared_files').click();"><span class="importo"><?php echo $shared["fullname"][$i];?></span></a>
                                                        <?php
                                                        if( $core->user_id == $shared["user_id"][$i] and $shared["role"][$i] == "user" ) {
                                                            echo '
                                                                <a href="javascript:;" id="remove_sh_'.$shared["id"][$i].'" style="color: #D9534F" onclick="remove_from_shared( \''.$shared["id"][$i].'\' );" class="remove-share pull-right" data-toggle="tooltip" data-placement="left" title="'.language_filter( "remove_share", true, true ).'">
                                                                    <i class="glyphicon glyphicon-remove"></i>
                                                                </a>';
                                                        }
                                                        ?>
                                                    </li>
                                                    <li style="min-height: 40px; text-align: justify; padding-right: 5px;"><span class="causale"><?php echo $shared["description"][$i];?></span> </li>
                                                    <li>
                                                        <p>
                                                            <small class="text-muted"><i class="glyphicon glyphicon-time"></i> <?php echo date( "Y/m/d", strtotime( $shared["date_added"][$i] ) );?></small>&nbsp;|&nbsp;
                                                            <small class="text-muted"><i class="glyphicon glyphicon-file"></i> <?php echo $shared["file_name"][$i]?></small>&nbsp;|&nbsp;
                                                            <small class="text-muted"><i class="glyphicon glyphicon-cloud-download"></i> <a href="javascript:;" onclick="download_share_file( '<?php echo $shared["id"][$i]?>' );"><?php language_filter( "Download" );?></a> </small>
                                                        </p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

<?php
                    if( $counter == 1 ) {
                        echo '</div>';
                        $counter = 0;
                    }
                    else {
                        $counter++;
                    }
            }
?>

        <?php
        if( @$shared["pages"] > 1 and @$page < @$shared["pages"]  ) {
        ?>
        <div class="add_more_share" style="text-align: center">
            <a href="javascript:;" id="plus_<?php echo $page;?>" onclick="share_page = '<?php echo $page + 1;?>'; show_what = '<?php echo $user;?>'; load_more_share( this.id );">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
        <?php
        }
        ?>
</div>
<script>
    $(".remove-share").tooltip();
</script>
<?php
            }
        }
    }
}
?>