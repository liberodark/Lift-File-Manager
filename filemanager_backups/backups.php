<?php
if (!isset($core))
{
	require_once '../filemanager_core.php';
	$core = new filemanager_core();
}
if ($core->isLogin())
{
	if (isset($_GET["file_name"]))
	{
        $filename = $_GET["file_name"].".zip";
        unset( $_GET["file_name"] );
		if (file_exists($filename))
		{
			 header('Content-Description: File Transfer');
		     header('Content-Type: application/octet-stream');
		     header('Content-Disposition: attachment; filename='.basename($filename));
		     header('Content-Transfer-Encoding: binary');
		     header('Expires: 0');
		     header('Cache-Control: must-revalidate');
		     header('Pragma: public');
		     header('Content-Length: ' . filesize($filename));
		     @ob_clean();
		     flush();
		     readfile($filename);
    		 exit;
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
else 
{
	header("Status: 404 Not Found");
}
?>