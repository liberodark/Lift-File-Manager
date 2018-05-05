<?php
if (!isset($core))
{
    require_once 'filemanager_core.php';
    $core = new filemanager_core();
    require_once 'filemanager_language.php';
}
if ($core->isLogin())
{
    if(isset($_GET['operation']) and $core->role == "admin" ) {

        $fs = new fs( ROOT_UPLOAD_PATH );
        try {
            $rslt = null;
            switch($_GET['operation']) {
                case 'get_node':
                    $node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
                    $rslt = $fs->lst($node, (isset($_GET['id']) && $_GET['id'] === '#'));
                    break;
                case "get_content":
                    $node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
                    $rslt = $fs->data($node);
                    break;
                case 'create_node':
                    $node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
                    $rslt = $fs->create($node, isset($_GET['text']) ? $_GET['text'] : '', (!isset($_GET['type']) || $_GET['type'] !== 'file'));
                    break;
                case 'rename_node':
                    $node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
                    $rslt = $fs->rename($node, isset($_GET['text']) ? $_GET['text'] : '');
                    break;
                case 'delete_node':
                    $node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
                    $rslt = $fs->remove($node);
                    break;
                case 'move_node':
                    $node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
                    $parn = isset($_GET['parent']) && $_GET['parent'] !== '#' ? $_GET['parent'] : '/';
                    $rslt = $fs->move($node, $parn);
                    break;
                case 'copy_node':
                    $node = isset($_GET['id']) && $_GET['id'] !== '#' ? $_GET['id'] : '/';
                    $parn = isset($_GET['parent']) && $_GET['parent'] !== '#' ? $_GET['parent'] : '/';
                    $rslt = $fs->copy($node, $parn);
                    break;
                default:
                    throw new Exception('Unsupported operation: ' . $_GET['operation']);
                    break;
            }

            if(preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT']) or  strpos( $_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0' ) !== false )
            {
                header("HTTP/1.0 200 OK");
                header('Content-type: text/json; charset=utf-8');
                header("Cache-Control: no-cache, must-revalidate");
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Pragma: no-cache");
            }
            else
            {
                header('Content-Type: application/json; charset=utf-8');
            }
            echo json_encode($rslt);
        }
        catch (Exception $e) {
            header($_SERVER["SERVER_PROTOCOL"] . ' 500 Server Error');
            header('Status:  500 Server Error');
            echo $e->getMessage();
        }
        die();
    }
}
