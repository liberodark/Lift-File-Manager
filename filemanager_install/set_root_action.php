<?php
require_once 'set_users_and_share_root.php';
$set = new set_users_and_share_root();
$set->change_root_dir();
echo $set->msg1 . "<br />";
echo $set->msg2 . "<br />";
echo $set->msg3. "<br />";
