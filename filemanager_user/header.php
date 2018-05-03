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
?>
<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="<?php language_filter("charset")?>">
      <title><?php language_filter("title");?></title>
      <meta name="robots" content="noindex">
      <meta name="googlebot" content="noindex">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="IE=9" />

      <!-- Le styles -->
      <link href="filemanager_css/bootstrap.css" rel="stylesheet" />
      <?php
      if( $language["direction"] == "rtl" ) {
      ?>
          <link href="filemanager_css/bootstrap-rtl.css" rel="stylesheet" />
      <?php
      }
      ?>
      <link href="filemanager_css/jqueryFileTree.css" rel="stylesheet" />
      <link href="filemanager_css/jquery.fancybox.css" rel="stylesheet" />
      <link rel="stylesheet" href="filemanager_assets/vakata-jstree/dist/themes/default/style.css" />
      <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!--[if lt IE 9]>
      <script src="filemanager_js/html5shiv.js"></script>
      <script src="filemanager_js/respond.min.js"></script>
      <![endif]-->
      <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
      <link href="filemanager_css/filemanager.css" rel="stylesheet" />
      <?php
      if( $language["direction"] == "rtl" ) {
      ?>
          <link href="filemanager_css/filemanager-rtl.css" rel="stylesheet" />
      <?php
      }
      ?>
      <style>
          body {
              padding-top: 20px;
              padding-bottom: 20px;
              direction: <?php echo $language["direction"];?>;
          }
      </style>
  </head>
<?php
}
else
{
    header("Location: .");
}
?>
