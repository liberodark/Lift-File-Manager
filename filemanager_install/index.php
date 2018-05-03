<html>
<head>
<meta name="robots" content="noindex">
<meta name="googlebot" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css"  href="../filemanager_css/bootstrap.css">
 <style type="text/css">
     body {
         padding-top: 40px;
         padding-bottom: 40px;
         background-color: #eee;
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
    <link rel="stylesheet" type="text/css"  href="../filemanager_css/bootstrap-responsive.css">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="../filemanager_js/html5shiv.js"></script>
    <script src="../filemanager_js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">

    <form class="form-signin" name="install_form" action="install.php" method="post" onsubmit="return check_submit();">
        <h2 class="form-signin-heading">Install</h2>
        <input type="text" name="firstname" class="form-control" placeholder="Firstname" required="required">
        <input type="text" name="lastname" class="form-control" placeholder="Lastname" required="required">
        <input type="text" name="email" class="form-control" placeholder="Email address" required="required">
        <input type="text" name="username" class="form-control" placeholder="Username" required="required">
        <input type="password" name="password" class="form-control" placeholder="Password" required="required">
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="install">Install</button>
    </form>

</div> <!-- /container -->

<script>
    function check_submit()
    {
        var firstname = document.forms["install_form"]["firstname"].value;
        var lastname = document.forms["install_form"]["lastname"].value;
        var username = document.forms["install_form"]["username"].value;
        var email = document.forms["install_form"]["email"].value;
        var password = document.forms["install_form"]["password"].value;
        if(firstname == "")
        {
            alert("Please write your firstname.");
            return false;
        }

        if(lastname == "")
        {
            alert("Please write your lastname.");
            return false;
        }

        if(username == "")
        {
            alert("Please write username.");
            return false;
        }

        if(email == "" || !validateEmail(email))
        {
            alert("Please write your email.");
            return false;
        }

        if(password == "")
        {
            alert("Please write password.");
            return false;
        }

        return true;
    }

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
</script>

</body>
</html>