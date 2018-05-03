<?php
if (!isset($core))
{
    require_once '../filemanager_user_core.php';
    $core = new filemanager_user_core();
    $core->userInfo();
    $core->create_user_panel($core->user_id);
    require_once '../filemanager_language_user.php';
}
if ($core->isLogin() and $core->is_block == 0)
{
?>
    <!-- Modal -->
    <div class="lift_preloader modal fade" id="preloader" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="filemanager_assets/img.php?img=pageloader" />
                </div>
            </div>
        </div>
    </div>


    <div id="show_status" style="display: none">

    </div>

    <div class="panel panel-default">
        <div class="panel-body">
            <div id="content_show" style="display: none;">

            </div>
        </div>
    </div>
<?php
}
else
{
    header("Location: .");
}
?>