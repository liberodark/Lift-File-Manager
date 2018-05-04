<?php

if( !defined('DB_HOST') ) {
    include 'filemanager_config.php';
}

require_once 'filemanager_core.php';

class option_class extends filemanager_core
{
    var $db;

    function __construct()
    {
        try {
          $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
          var_dump($this->db);
        } catch (Exception $exception){
          return $exception->getMessage();
        }
    }

    public function mysql_request($query) {
        try {
          $request_response = $this->db->query($query);
          return $request_response;
        } catch (Exception $exception){
          return $exception->getMessage();
        }
    }

    public function quote($txt){
        return $this->db->quote($txt);
    }

    private function encode_me($txt)
    {
        $txt = strip_tags($txt);
        $txt = $this->quote($txt);
        $txt = urlencode($txt);
        return $txt;
    }

    function get_option($name)
    {
        $content = array();
        $select = $this->mysql_request("SELECT * FROM filemanager_options WHERE option_name='$name'");
        while($row = mysql_fetch_array($select))
        {
            $content = $this->decode($row["option_content"]);
        }
        return $content;
    }

    function update_option($name, $content)
    {
        if($this->add_option($name, $content))
        {
            return true;
        }
        else
        {
            $content = $this->_encode($content);
            $update = $this->mysql_request("UPDATE filemanager_options SET option_content='$content' WHERE option_name='$name'");
            if($update)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    function add_option($name, $content)
    {
        if($this->exists_option($name))
        {
            return false;
        }
        else
        {
            $content = $this->_encode($content);
            $insert = $this->mysql_request("INSERT INTO filemanager_options (option_name, option_content) VALUES ('$name', '$content')");
            if($insert)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    function delete_option($name)
    {
        $delete = $this->mysql_request("DELETE FROM filemanager_options WHERE option_name='$name'");
        if($delete)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function exists_option($name)
    {
        $select = $this->mysql_request("SELECT id FROM filemanager_options WHERE option_name='$name'");
        $num = mysql_num_rows($select);
        if($num <= 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

}
