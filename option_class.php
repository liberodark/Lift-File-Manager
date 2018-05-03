<?php
class option_class extends filemanager_core
{
    function get_option($name)
    {
        $content = array();
        $select = mysql_query("SELECT * FROM filemanager_options WHERE option_name='$name'");
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
            $update = mysql_query("UPDATE filemanager_options SET option_content='$content' WHERE option_name='$name'");
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
            $insert = mysql_query("INSERT INTO filemanager_options (option_name, option_content) VALUES ('$name', '$content')");
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
        $delete = mysql_query("DELETE FROM filemanager_options WHERE option_name='$name'");
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
        $select = mysql_query("SELECT id FROM filemanager_options WHERE option_name='$name'");
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