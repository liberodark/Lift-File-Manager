<?php
require_once 'filemanager_core.php';
$core = new filemanager_core();
require_once 'filemanager_language.php';
if ($core->isLogin())
{
    if(isset($_GET["info"]))
    {
        $file = utf8_decode(base64_decode($_GET["info"]));
        $file = str_replace("//", "/", $file);

        if($core->check_base_root($file))
        {
            if(is_file($file))
            {
                $result = "";
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $ext = strtolower($ext);
                $name = end(explode("/", $file));
                if($ext == "txt")
                {
                    $post = "save_".md5($file);
                    if(isset($_POST[$post]))
                    {
                        $new = $_POST["new_value"];
                        $fp = fopen($file, "w");
                        fwrite($fp, $new);
                        fclose($fp);
                        $result = '<b class="text-success">'.language_filter("Your file has been saved.", true).'</b>';
                    }
                    $input = file_get_contents($file);
                    ?>
                <html>
                <head>
                    <meta charset="<?php echo $language["charset"];?>">
                    <link rel="stylesheet" type="text/css" href="filemanager_css/bootstrap.css">
                    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
                    <!--[if lt IE 9]>
                    <script src="filemanager_js/html5shiv.js"></script>
                    <script src="filemanager_js/respond.min.js"></script>
                    <![endif]-->
                    <title><?php echo $language["title"];?></title>
                    <style type="text/css">
                        body {
                            padding: 0;
                            margin: 0;
                            background: #ffffff;
                            direction: <?php echo $language["direction"];?>;
                        }
                    </style>
                </head>
                <body>
                    <form method="post" style="width: 100%" action="edit_file.php?info=<?php echo base64_encode(utf8_encode($file));?>">
                        <textarea style="width: 97%; height: 300px; margin: 10px 12px 0 12px;" name="new_value" class="form-control"><?php echo $input;?></textarea>
                        <hr style="width: 100%"/>
                        <button type="submit" style="margin-left: 10px" name="save_<?php echo md5($file);?>" class="btn btn-success"><?php language_filter("Save")?></button>
                        <?php echo $result;?>
                    </form>
                </body>
                </html>
                <?php
                }
                else
                {
                    header("Status: 404 Not Found");
                }
            }
            else
            {
                header("Status: 404 Not Found");
            }
        }
    }
    else
    {
        header("Status: 404 Not Found");
    }
}
else
{
    header("Status: 404 Not Found");
}
?>