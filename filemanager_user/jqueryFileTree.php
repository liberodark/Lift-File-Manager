<?php
//
// jQuery File Tree PHP Connector
//
// Version 1.01
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 24 March 2008
//
// History:
//
// 1.01 - updated to work with foreign characters in directory/file names (12 April 2008)
// 1.00 - released (24 March 2008)
//
// Output a list of files for jQuery File Tree
//


if (!isset($core))
{
	require_once '../filemanager_user_core.php';
	$core = new filemanager_user_core();
    $core->userInfo();
}
if ($core->isLogin())
{
    $ignored = array(
        '.',
        '..',
        'filemanager_assets',
        'filemanager_assets/lng',
        'filemanager_assets/PHPMailer',
        'filemanager_assets/PHPMailer/docs',
        'filemanager_assets/PHPMailer/extras',
        'filemanager_assets/PHPMailer/language',
        'filemanager_assets/securimage',
        'filemanager_assets/securimage/backgrounds',
        'filemanager_assets/securimage/images',
        'filemanager_assets/securimage/words',
        'filemanager_assets/uploader',
        'filemanager_assets/uploader/src',
        'filemanager_assets/vakata-jstree',
        'filemanager_assets/vakata-jstree/dist',
        'filemanager_assets/vakata-jstree/dist/libs',
        'filemanager_assets/vakata-jstree/dist/themes/default',
        'filemanager_assets/vakata-jstree/src',
        'filemanager_assets/vakata-jstree/src/themes',
        'filemanager_assets/vakata-jstree/src/themes/default',
        'filemanager_assets/vakata-jstree/dist/themes',
        'filemanager_assets/vakata-jstree/libs',
        'filemanager_backups',
        'filemanager_css',
        'filemanager_fonts',
        'filemanager_img',
        'filemanager_img/fancy',
        'filemanager_img/pattern',
        'filemanager_install',
        'filemanager_js',
        'filemanager_temp',
        'filemanager_user',
        'filemanager_user/filemanager_error_log.txt',
        'filemanager_user/ajax_check_username.php',
        'filemanager_user/ajax_manage_dir.php',
        'filemanager_user/ajax_manage_tickets.php',
        'filemanager_user/ajax_manager_share.php',
        'filemanager_user/ajax_show_filemanager.php',
        'filemanager_user/ajax_show_profile.php',
        'filemanager_user/ajax_show_shared.php',
        'filemanager_user/ajax_ticket_show.php',
        'filemanager_user/ajax_tickets_show.php',
        'filemanager_user/ajax_update_profile.php',
        'filemanager_user/content.php',
        'filemanager_user/download.php',
        'filemanager_user/edit_file.php',
        'filemanager_user/filemanager_pdfShow.php',
        'filemanager_user/filemanager_siteMap.php',
        'filemanager_user/filemanager_uploader.php',
        'filemanager_user/footer.php',
        'filemanager_user/header.php',
        'filemanager_user/img.php',
        'filemanager_user/index.php',
        'filemanager_user/jqueryFileTree.php',
        'filemanager_user/menu.php',
        'filemanager_user/modals.php',
        'filemanager_user/navigate.php',
        'filemanager_user/option_class.php',
        'filemanager_user/upload.php',
        'filemanager_error_log.txt',
        'ajax_add_user.php',
        'ajax_check_user.php',
        'ajax_edit_user.php',
        'ajax_manage_dir.php',
        'ajax_manage_tickets.php',
        'ajax_manager_share.php',
        'ajax_remove_file.php',
        'ajax_show_filemanager.php',
        'ajax_show_home.php',
        'ajax_show_profile.php',
        'ajax_show_setting.php',
        'ajax_show_shared.php',
        'ajax_show_users.php',
        'ajax_ticket_show.php',
        'ajax_tickets_show.php',
        'ajax_update_profile.php',
        'content.php',
        'download.php',
        'edit_file.php',
        'filemanager_config.php',
        'filemanager_core.php',
        'filemanager_language.php',
        'filemanager_language_user.php',
        'filemanager_pdfShow.php',
        'filemanager_siteMap.php',
        'filemanager_uploader.php',
        'filemanager_user_core.php',
        'footer.php',
        'header.php',
        'img.php',
        'index.php',
        'jqueryFileTree.php',
        'login.php',
        'logout.php',
        'menu.php',
        'modals.php',
        'navigate.php',
        'option_class.php',
        'upload.php',
    );
	$new_ignored = $core->get_option("deny_folders_".$core->user_id);
    if(!empty($new_ignored))
    {
        $ignored = array_merge($ignored, $new_ignored);
    }
    $allow_ext = $core->get_option("allow_extensions_".$core->user_id);
    $deny_folders = $core->get_option( "deny_folders_".$core->user_id );
    foreach( $deny_folders as $key => $val ) {
        $deny_folders[$key] = $core->filter_txt( $val, true );
    }
	$_POST['dir'] = urldecode($_POST['dir']);



    $sign = "..";
    if(isset($_POST['sign']))
    {
        $sign = urldecode($_POST['sign']);
        $sign = $core->system_root_dir.ROOT_DIR_PATH.$sign;
        if($sign == $core->user_dir)
        {
            $sign = $core->user_dir."/";
        }
        else
        {
            $sign = $core->user_dir."/";
        }
    }
    $dir_root = "..";
	if( file_exists(@$root . $_POST['dir']) ) {
		$files = scandir(@$root . $_POST['dir']);
		natcasesort($files);
		if( count($files) > 2 ) { /* The 2 accounts for . and .. */
			echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
			// All dirs
            if($_POST['dir'] == $core->user_dir)
                echo "<li class=\"directory \"><a href=\"javascript:;\" id=\"filemanager_home_directory_root\" rel=\"".$sign."\">" . htmlentities($dir_root) . "</a></li>";

            foreach( $files as $file ) {
				if (in_array($file, $ignored)) continue;
                $realpath = realpath( $_POST['dir'].$file );
                $realpath = $core->filter_txt( $realpath, true );
                if(in_array($realpath, $deny_folders)) continue;
				if( file_exists(@$root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir(@$root . $_POST['dir'] . $file) ) {
					echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file, ENT_QUOTES, "UTF-8") . "/\">" . htmlentities($file, ENT_QUOTES, "UTF-8") . "</a></li>";
				}
			}
			// All files
            if( isset($_GET["showFiles"] ) ) {
                foreach( $files as $file ) {
                    if (in_array($file, $ignored)) continue;

                    $ext = strtolower(end(explode(".", $file)));
                    if(!in_array($ext, $allow_ext)) continue;

                    if( file_exists(@$root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir(@$root . $_POST['dir'] . $file) ) {
                        $ext = preg_replace('/^.*\./', '', $file);
                        echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file, ENT_QUOTES, "UTF-8") . "\">" . htmlentities($file, ENT_QUOTES, "UTF-8") . "</a></li>";
                    }
                }
            }

			echo "</ul>";	
		}
	}
}

?>