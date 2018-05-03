<?php
if (!isset($core))
{
    require_once 'filemanager_core.php';
    $core = new filemanager_core();
    require_once 'filemanager_language.php';
}
if ($core->isLogin())
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
    $key = "redirect_to_url_file_manager_go";
    if(isset($_SESSION[$key]))
    {
        $dir = $_SESSION[$key];
        unset($_SESSION[$key]);
        if(is_dir($dir))
        {
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
        //showHomePage();
        loading_from_file = false;
        showFileManager('');
    <?php
    }
    ?>
    $("#homeMenu").click(function()
    {
        loading_from_file = false;
        showHomePage();
    });

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

    $("#setting").click(function(){
        loading_from_file = false;
        showSetting();
    });

    $("#users").click(function(){
        loading_from_file = false;
        showUsers();
    });

    $("#addUser").click(function(){
        loading_from_file = false;
        showAddUser();
    });

    $("#tickets").click(function(){
        loading_from_file = false;
        showTickets();
    });


});

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
    $.post("ajax_ticket_show.php",
    {
        showTicket:<?php echo $core->admin_id;?>,
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
    $.post("ajax_tickets_show.php",
    {
        showTickets:<?php echo $core->admin_id;?>,
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

function showAddUser()
{
    $('#preloader').modal('show');
    $.post("ajax_add_user.php",
    {
        showAddUser:<?php echo $core->admin_id;?>
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

function showHomePage()
{
    $('#preloader').modal('show');
    $.post("ajax_show_home.php",
    {
        showHome:<?php echo $core->admin_id;?>
    },
    function(data,status)
    {
        if(status == "success")
        {
            $('#content_show').html('');
            $('.bar').addClass('bar-success');
            $('li').removeClass();
            $('#homeMenu').addClass('active');
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

function showUsers()
{
    $('#preloader').modal('show');
    $.post("ajax_show_users.php",
    {
        showUser:<?php echo $core->admin_id;?>
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

function showSetting()
{
    $('#preloader').modal('show');
    $.post("ajax_show_setting.php",
    {
        showSetting:<?php echo $core->admin_id;?>
    },
    function(data,status)
    {
        if(status == "success")
        {
            $('#content_show').html('');
            $('.bar').addClass('bar-success');
            $('li').removeClass();
            $('#setting').addClass('active');
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

function showEditProfile()
{
    $('#preloader').modal('show');
    $.post("ajax_show_profile.php",
    {
        showProfile:<?php echo $core->admin_id;?>
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

function showFileManager( dir_path )
{
    if(typeof (my_sort) == 'undefined')
    {
        my_sort = "date";
    }

    if(typeof ( loading_from_file_status ) == 'undefined')
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

    if(loading_from_file == false)
        $('#preloader').modal('show');


    $.post("ajax_show_filemanager.php",
    {
        showFilemanager: <?php echo $core->admin_id;?>,
        my_dir_path: dir_path,
        sort_type: my_sort,
        page: page,
        countShow: countShow,
        search: search
    },
    function(data,status)
    {
        if(status == "success")
        {
            $('#content_show').html('');
            $('.bar').addClass('bar-success');
            $('li').removeClass();
            $('#fileManager').addClass('active');
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
        share_role = 'admin';
    }
    $('#preloader').modal('show');
    $.post("ajax_show_shared.php",
    {
        showShared:<?php echo $core->admin_id;?>,
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

    $.post("ajax_show_shared.php",
    {
        showShared:<?php echo $core->admin_id;?>,
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

function download_share_file( info )
{
    var data = "ajax_manager_share.php?fid="+info+"&uid="+"<?php echo $core->admin_id;?>";
    document.location = data;
    return false;
}

function remove_from_shared( info ) {
    $("#remove_sh_"+info).html( "<?php language_filter( "Loading..." )?>" );
    $.post(
    "ajax_manager_share.php",
    {
        share: info,
        admin: "<?php echo $core->admin_id;?>"
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
