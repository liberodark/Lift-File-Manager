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
</div> <!-- /container -->
<div class="container">
    <hr>
    <div class="footer">
        <p><?php language_filter("Footer Text");?></p>
    </div>
</div>


    <script type="text/javascript" src="filemanager_js/jquery-1.11.1.js"></script>
    <script src="filemanager_js/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="filemanager_assets/uploader/src/dmuploader.min.js"></script>
    <script type="text/javascript" src="filemanager_js/bootstrap.js"></script>
    <script type="text/javascript" src="filemanager_js/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="filemanager_assets/vakata-jstree/dist/jstree.js"></script>
    <script type="text/javascript" src="filemanager_js/filemanager.js"></script>
    <script>
    $(document).ready(function(){
<?php
        $key = "redirect_to_url_file_manager_go_user";
        if(isset($_SESSION[$key]))
        {
            $dir = $_SESSION[$key];
            unset($_SESSION[$key]);
            if(is_dir($dir) and $core->check_base_root($dir))
            {
                $dir = $core->name_filter( str_replace( $core->system_root_dir.ROOT_DIR_PATH, "", $dir ) );
?>
                loading_from_file = false;
                showFileManager('<?php echo addslashes($dir);?>');
<?php
            }
            else
            {
?>
                loading_from_file = false;
                showFileManager('');
<?php
            }
        }
        else
        {
?>
            loading_from_file = false;
            showFileManager('');
<?php
        }
        $settings = $core->get_option("settings");
?>

       $("#editProfile").click(function()
        {
            loading_from_file = false;
            showEditProfile();
        });

        $("#fileManager").click(function(){
            loading_from_file = false;
            page = 1;
            showFileManager('');
        });

        $("#tickets").click(function(){
            loading_from_file = false;
            showTickets();
        });

    });

    <?php
    if($settings->ticket == "on")
    {
    ?>
    function show_ticket(id)
    {
        if(typeof (show_what) == 'undefined')
        {
            show_what = "all";
        }

        if(typeof (ticket_page) == 'undefined')
        {
            ticket_page = 1;
        }
        $('#preloader').modal('show');
        $.post("filemanager_user/ajax_ticket_show.php",
        {
            showTicket:<?php echo $core->user_id;?>,
            show_what:show_what,
            ticket_page:ticket_page,
            ticketId:id
        },
        function(data,status)
        {
            if(status == "success")
            {
                $('#content_show').html('');
                $('.bar').addClass('bar-success');
                $('li').removeClass();
                $('#tickets').addClass('active');
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

    function showTickets()
    {
        if(typeof (show_what) == 'undefined')
        {
            show_what = "all";
        }

        if(typeof (ticket_page) == 'undefined')
        {
            ticket_page = 1;
        }
        $('#preloader').modal('show');
        $.post("filemanager_user/ajax_tickets_show.php",
        {
            showTickets:<?php echo $core->user_id;?>,
            show_what:show_what,
            ticket_page:ticket_page
        },
        function(data,status)
        {
            if(status == "success")
            {
                $('#content_show').html('');
                $('.bar').addClass('bar-success');
                $('li').removeClass();
                $('#tickets').addClass('active');
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
    <?php
    }
    ?>


    <?php
    if( isset( $settings->system_share ) ) {
        if( $settings->system_share == "on" ) {
    ?>
     $("#shared_files").click(function(){
         if(typeof (show_what) == 'undefined')
         {
             show_what = "all";
         }

         if(typeof (share_page) == 'undefined')
         {
             share_page = 1;
         }

         if(typeof (share_role) == 'undefined')
         {
             share_role = 'user';
         }
         $('#preloader').modal('show');
         $.post("filemanager_user/ajax_show_shared.php",
         {
             showShared:<?php echo $core->user_id;?>,
             show_what:show_what,
             share_page:share_page,
             share_role:share_role
         },
         function(data,status)
         {
             if(status == "success")
             {
                 $('#content_show').html('');
                 $('.bar').addClass('bar-success');
                 $('li').removeClass();
                 $('#shared_files').addClass('active');
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
     });

     function load_more_share( id )
     {
         $("#"+id).html( "<?php language_filter( "Loading..." )?>" );

         $.post("filemanager_user/ajax_show_shared.php",
         {
             showShared:<?php echo $core->user_id;?>,
             show_what:show_what,
             share_page:share_page,
             extra_page:"OK",
             share_role:share_role
         },
         function(data,status)
         {
             if(status == "success")
             {
                 $("#"+id).parent().fadeOut( 300, function(){
                     $("#"+id).parent().remove();
                 });
                 var html = $('#content_show').html();
                 $('#content_show').html(html + data);
             }
             else
             {
                 $("#"+id).parent().fadeOut( 300, function(){
                     $("#"+id).parent().remove();
                 });
                 alert( "Server Error!" );
             }
         });
     }
    <?php
        }
    }
    ?>


    function showEditProfile()
    {
        $('#preloader').modal('show');
        $.post("filemanager_user/ajax_show_profile.php",
        {
            showProfile:<?php echo $core->user_id;?>
        },
        function(data,status)
        {
            if(status == "success")
            {
                $('#content_show').html('');
                $('.bar').addClass('bar-success');
                $('li').removeClass();
                /*$('#editProfile').addClass('active');*/
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

    function showFileManager(dir_path)
    {
        if(typeof (my_sort) == 'undefined')
        {
            my_sort = "date";
        }

        if(typeof (loading_from_file_status) == 'undefined')
        {
            loading_from_file_status = "blue";
        }

        if(typeof (page) == 'undefined')
        {
            page = 1;
        }

        if(typeof (countShow) == 'undefined')
        {
            countShow = 10;
        }
        if(typeof (search) == 'undefined')
        {
            search = '';
        }
        if(dir_path == "")
        {
            dir_path = "<?php echo addslashes( $core->user_root_folder );?>";
        }
        <?php
        $active = 0;
        if( isset($_GET["switch"] ) ) {
            $active = (int) mysql_real_escape_string( $_GET["switch"] );
        }
        ?>
        if(loading_from_file == false)
            $('#preloader').modal('show');
        $.post("filemanager_user/ajax_show_filemanager.php",
        {
            showFilemanager:<?php echo $core->user_id;?>,
            my_dir_path:dir_path,
            sort_type:my_sort,
            page:page,
            countShow:countShow,
            search:search,
            extra_dir_show:"<?php echo $active;?>"
        },
        function(data,status)
        {
            if(status == "success")
            {
                $('#content_show').html('');
                $('.bar').addClass('bar-success');
                $('li').removeClass();
                $('#fileManager').addClass('active');
                $("#preloader").modal("hide");
                if(loading_from_file == false)
                {
                    $("#preloader").modal("hide");
                }
                else
                {
                    show_errors_on_nav(loading_from_file, loading_from_file_status);
                }
                loading_from_file = false;
                search = '';
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

    function show_errors_on_nav(msg, color)
    {
        if(color == "red")
        {
            color = "#D9534F";
        }

        if(color == "green")
        {
            color = "#5CB85C";
        }

        if(color == "blue")
        {
            color = "#428BCA";
        }

        $("html, body").animate({ scrollTop: 0 }, "slow");
        $("#welcome").attr("data-content", "<center><span style='color: "+color+";'>"+msg+"</span></center>");
        $('#welcome').popover('show');
        setTimeout(function(){$('#welcome').popover('hide');}, 3000);
    }

    function show_preloader()
    {
        $("html, body").animate({ scrollTop: 0 }, "slow");
        $("#welcome").attr("data-content", "<center><img src='filemanager_assets/img.php?img=preloader'/></center>");
        $('#welcome').popover('show');
    }

    function hide_preloader()
    {
        $("html, body").animate({ scrollTop: 0 }, "slow");
        $("#welcome").attr("data-content", "");
        $('#welcome').popover('hide');
    }


    function download_share_file( info )
    {
        var data = "filemanager_user/ajax_manager_share.php?fid="+info+"&uid="+"<?php echo $core->user_id;?>";
        document.location = data;
        return false;
    }

    function remove_from_shared( info ) {
        $("#remove_sh_"+info).html( "<?php language_filter( "Loading..." )?>" );
        $.post(
            "filemanager_user/ajax_manager_share.php",
            {
                share: info,
                user: "<?php echo $core->user_id;?>"
            },
            function (data, status ) {
                if( status == "success" ) {
                    if( data == "true" ) {
                        $("#remove_sh_"+info).html( "<?php language_filter( "share_removed" )?>" );
                        setTimeout( function(){
                            $("#timeline_"+info).fadeOut( 500, function(){
                                var $parent = $(this).parent().parent();
                                $(this).remove();
                                var check = $parent.find( 'div.col-sm-11').hasClass( 'remove_checker' );
                                if( !check ) {
                                    $parent.remove();
                                }
                            } );
                        }, 2000 );
                    }
                    else {
                        $("#remove_sh_"+info).html( '<i class="glyphicon glyphicon-remove"></i>' );
                    }
                }
                else {
                    alert( "Server Error" );
                    $("#remove_sh_"+info).html( '<i class="glyphicon glyphicon-remove"></i>' );
                    return false;
                }
            }
        );
    }


    </script>


</body>
</html>

<?php
}
else
{
    header("Location: .");
}
?>
