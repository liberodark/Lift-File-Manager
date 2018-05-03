<?php
if (!isset($core))
{
    require_once 'filemanager_core.php';
    $core = new filemanager_core();
}
if ($core->isLogin())
{
    header("Location: .");
}
else
{
    $result = '';
    if (isset($_POST["login"]))
    {
        if( isset( $_SESSION["filemanager_language"] ) ) {
            unset( $_SESSION["filemanager_language"] );
        }
        if( isset( $_POST["filemanager_lang"] ) and $_POST["filemanager_lang"] != "en" ) {
            $lng = str_replace( "/", "", $_POST["filemanager_lang"] );
            $lng = str_replace( "\\", "", $_POST["filemanager_lang"] );
            $_SESSION["filemanager_language"] = $lng;
        }
        $login = $core->login($_POST["username"], $_POST["password"]);
        if ($login["status"] == true)
        {
            header("Location: .");
        }
        else
        {
            require_once 'filemanager_user_core.php';
            $core = new filemanager_user_core();
            $login = $core->login($_POST["username"], $_POST["password"]);
            if ($login["status"] == true)
            {
                $active = "";
                if( isset( $_GET["switch"] ) ) {
                    $active = "?switch=".$_GET["switch"];
                }
                header("Location: .".$active);
                exit;
            }
            else
            {
                if($login["msg"] == "check")
                {
                    $login["msg"] = language_filter("Login_Error", true);
                }
                else
                {
                    $login["msg"] = language_filter("Login_Block", true);
                }
                $result = '<div class="alert alert-danger"><center>'.$login["msg"].'</center></div>';
            }
        }
    }

    if(isset($_POST["fotgotpass"]))
    {
        if( !function_exists( "language_filter" ) )
        {
            require_once 'filemanager_language.php';
        }
        $forgot_pass = $core->forgotPassword($_POST["email_forgot"]);
        if($forgot_pass["status"] === true)
        {
            $forgot_pass["msg"] = language_filter("Forgot_Pass_Success", true);
            $result = '<div class="alert alert-success"><center>'.$forgot_pass["msg"].'</center></div>';
        }
        else if( $forgot_pass["status"] == "email") {
            $forgot_pass["msg"] = language_filter($forgot_pass["msg"], true);
            $result = '<div class="alert alert-success"><center>'.$forgot_pass["msg"].'</center></div>';
        }
        else
        {
            require_once 'filemanager_user_core.php';
            $core = new filemanager_user_core();
            $forgot_pass = $core->forgotPassword($_POST["email_forgot"]);
            if($forgot_pass["status"])
            {
                $forgot_pass["msg"] = language_filter("Forgot_Pass_Success", true);
                $result = '<div class="alert alert-success"><center>'.$forgot_pass["msg"].'</center></div>';
            }
            else
            {
                $forgot_pass["msg"] = language_filter($forgot_pass["msg"], true);
                $result = '<div class="alert alert-danger"><center>'.$forgot_pass["msg"].'</center></div>';
            }
        }
    }

    require_once 'option_class.php';
    $option = new option_class();
    $settings = $option->get_option("settings");
    if($settings->register == "on")
    {
        $key = md5(rand());
        $_SESSION["register_page_checker"] = $key;
        if(isset($_POST["firstname"]))
        {
            if(isset($_SESSION["register_username_email_checked"]))
            {
                $firstname = $_POST["firstname"];
                $lastname = $_POST["lastname"];
                $email = $_POST["email"];
                $username = $_POST["username"];
                $captcha = $_POST["captcha"];
                require_once 'filemanager_assets/securimage/securimage.php';
                $securimage = new Securimage();
                if( !function_exists( "language_filter" ) ) {
                    require_once 'filemanager_language.php';
                }
                if ($securimage->check($captcha) == false)
                {
                    $result = '<div class="alert alert-danger" style="text-align: center;">'.language_filter("captcha_error", true).'</div>';
                }
                else
                {
                    if($core->register_this_user($username, $email, $firstname, $lastname))
                    {
                        $result = '<div class="alert alert-success" style="text-align: center;">'.language_filter("register_done_user", true).'</div>';
                    }
                    else
                    {
                        $result = '<div class="alert alert-danger" style="text-align: center;">'.language_filter("register_error_user", true).'</div>';
                    }
                }
            }
        }

        if(isset($_GET["activation_code"]) and isset($_GET["info"]))
        {
            if( !function_exists( "language_filter" ) )
            {
                require_once 'filemanager_language.php';
            }
            if($core->activate_user($_GET["activation_code"], $_GET["info"]))
            {
                $result = '<div class="alert alert-success" style="text-align: center;">'.language_filter("activate_done", true).'</div>';
            }
            else
            {
                $result = '<div class="alert alert-danger" style="text-align: center;">'.language_filter("activate_error", true).'</div>';
            }
        }
    }

    if( !function_exists( "language_filter" ) )
    {
        require_once 'filemanager_language.php';
    }

    /* Select available */
    function select_languages() {
        $dir = __DIR__."/filemanager_assets/lng";
        if( is_dir( $dir ) ) {
            $files = glob($dir."/*.{php}", GLOB_BRACE);
            if( !empty( $files ) ) {
                echo '<select class="form-control" name="filemanager_lang">';
                foreach( $files as $file ) {
                    $lang  = basename( $file, ".php" );
                    echo '<option value="'.$lang.'">'.$lang.'</option>';
                }
                echo '</select><br />';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?php echo $language["charset"];?>">
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css"  href="filemanager_css/bootstrap.css">
    <?php
    if( $language["direction"] == "rtl" ) {
    ?>
        <link href="filemanager_css/bootstrap-rtl.css" rel="stylesheet" />
    <?php
    }
    ?>
    <style type="text/css">
        body {
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #eee;
            direction: <?php echo $language["direction"];?>;
        }

        .form-signin {
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
        }
        .form-signin .form-signin-heading,
        .form-signin .checkbox {
            margin-bottom: 10px;
        }
        .form-signin .checkbox {
            font-weight: normal;
        }
        .form-signin .form-control {
            position: relative;
            font-size: 16px;
            height: auto;
            padding: 10px;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        .form-signin .form-control:focus {
            z-index: 2;
        }
        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }
        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

    </style>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="filemanager_js/html5shiv.js"></script>
    <script src="filemanager_js/respond.min.js"></script>
    <![endif]-->

    <title><?php language_filter("Login");?></title>
</head>
<body>
<div class="container">
    <?php echo $result;?>
    <form class="form-signin" role="form" action="login.php<?php if( isset( $_GET["switch"] ) ) echo "?switch=".$_GET["switch"];?>" method="post">
        <h2 class="form-signin-heading"><?php language_filter("Login");?></h2>
        <input type="text" name="username" class="form-control" placeholder="<?php language_filter("Username");?>" required="required">
        <input type="password" name="password" class="form-control" placeholder="<?php language_filter("Password");?>" required="required">
        <?php select_languages();?>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="login"><?php language_filter("Login Button");?></button>
        <?php
        if($settings->register == "on")
        {
            ?>
            <a href="javascript:;" data-toggle="modal" data-target="#register"><?php language_filter("Sign up");?></a> |
            <?php
        }
        ?>
        <a href="javascript:;" data-toggle="modal" data-target="#forgot"><?php language_filter("Forgot_Pass_Text");?></a>
    </form>
</div> <!-- /container -->


    <?php
    if($settings->register == "on")
    {
?>
    <div class="modal fade" id="register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php language_filter("Sign_up_header");?></h4>
                </div>
                <div class="modal-body">
                    <form action="login.php" method="post" name="add_user_form">
                        <div class="form-group">
                            <label for="firstname_user"><?php language_filter("First Name");?></label>
                            <input type="text" id="firstname_user" name="firstname" class="form-control" required="required">
                        </div>

                        <div class="form-group">
                            <label for="lastname_user"><?php language_filter("Last Name");?></label>
                            <input type="text" name="lastname" id="lastname_user" class="form-control" required="required">
                        </div>

                        <div class="form-group">
                            <label for="email_user"><?php language_filter("Email")?></label>
                            <input type="email" id="email_user" name="email" class="form-control" required="required">
                        </div>

                        <div class="form-group">
                            <label for="username_user"><?php language_filter("Username")?></label>
                            <input type="text" id="username_user" name="username" class="form-control" required="required">
                        </div>

                        <div class="form-group">
                            <label for="captcha_code"><?php language_filter("Captcha code");?></label>
                            <input type="text" id="captcha_code" name="captcha" class="form-control" required="required">
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <img id="captcha" src="filemanager_assets/securimage/securimage_show.php" alt="CAPTCHA Image" />
                                </div>
                                <div class="col-md-12">
                                    <a href="javascript:;" onclick="document.getElementById('captcha').src = 'filemanager_assets/securimage/securimage_show.php?' + Math.random(); return false"><?php language_filter("captcha_reset");?></a>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <p id="preloader" class="text-left" style="color: #A94442; margin-top: 5px; display: none;"><?php language_filter("Please Wait...");?></p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Close")?></button>
                                <button type="button" onclick="check_register(this.form);" name="register_new_user" class="btn btn-primary"><?php language_filter("Sign_up_btn")?></button>
                            </p>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
        <?php
    }
    ?>

<!-- Modal -->
<div class="modal fade" id="forgot" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php language_filter("Forgot_Pass_Text");?></h4>
            </div>
            <div class="modal-body">
                <form class="form-signin" role="form" action="login.php" method="post">
                    <input type="email" name="email_forgot" class="form-control" placeholder="<?php language_filter("Forgot_Placeholder");?>" required="required">
                    <br />
                    <button class="btn btn-lg btn-danger btn-block" type="submit" name="fotgotpass"><?php language_filter("Forgot_Btn");?></button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php language_filter("Close");?></button>
            </div>
        </div>
    </div>
</div>
<script src="filemanager_js/jquery-1.11.1.js"></script>
<script src="filemanager_js/bootstrap.js"></script>
    <?php
    if($settings->register == "on")
    {
        ?>
    <script>
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

        function check_register(form)
        {
            var firstname = $("#firstname_user").val();
            var lastname = $("#lastname_user").val();
            var email = $("#email_user").val();
            var username = $("#username_user").val();
            var captcha = $("#captcha_code").val();
            if(firstname == '' || lastname == '' || email == '' || username == '' || captcha == '')
            {
                alert("<?php language_filter("Please fill the fields.", false, true)?>");
                return false;
            }
            if(validateEmail(email) == false)
            {
                alert("<?php language_filter("Please write a valid email.", false, true)?>");
                return false;
            }
            $("#preloader").show();
            $.post(
                    user_checker_link,
                    {
                        email:email,
                        username:username,
                        spam:'<?php echo @$key;?>'
                    },
                    function (data, status)
                    {
                        if(status == "success")
                        {
                            if(data == "done")
                            {
                                form.submit();
                            }
                            else
                            {
                                $("#preloader").hide();
                                if(data == "username")
                                {
                                    alert("<?php language_filter("username_exists", false, true);?>");
                                    return false;
                                }
                                else if(data == "email")
                                {
                                    alert("<?php language_filter("email_exists", false, true);?>");
                                    return false;
                                }
                                else
                                {
                                    alert("Server error");
                                    return false;
                                }
                            }
                        }
                        else
                        {
                            $("#preloader").hide();
                            alert("Server error");
                            return false;
                        }
                    }
            );
        }
    </script>
        <?php
    }
    ?>
</body>
</html>
<?php
}
?>