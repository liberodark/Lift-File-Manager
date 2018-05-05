<?php
class option_class extends filemanager_core
{
    function get_option($name)
    {
        $content = array();
        $select = $this->mysql_request("SELECT * FROM filemanager_options WHERE option_name='$name'");
        while($row = $select->fetch())
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
        $num = $select->rowCount();
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