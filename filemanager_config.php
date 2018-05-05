<?php
/*
 * Database Information
 */
define("DB_HOST", "localhost"); // Address of your mysql server
define("DB_USER", "root"); // User of your mysql server
define("DB_PASS", "Romain.mysql"); // Password of your user on mysql server
define("DB_NAME", "my_yurfile"); // Your database name that you created on your mysql server


/*
 * Custom Avatar
 */
//define( "ADMIN_DEFAULT_AVATAR", "http://url-to-your-image.com/your-image.png.jpg.gif" );
//define( "USER_DEFAULT_AVATAR", "http://url-to-your-image.com/your-image.png.jpg.gif" );


/*
 * Default Language
 */
define( "DEFAULT_LNG", "English" ); // As an example if you write German instead of English here your default language will be German.


/*
 * SMTP SETTINGS
 */
define( 'IS_SMTP_USE', false );
define( 'SMTPAuth', true );
define( 'SMTPSecure', 'ssl' );
define( 'SMTPHost', '' );
define( 'SMTPPort', '' );
define( 'SMTPUsername', '' );
define( 'SMTPPassword', '' );
define( 'SMTPFromSMTPUsername', false );

//----------------------------
/*
 * Directory Settings
 */
define("ROOT_DIR_PATH", "./"); // Root directory path # "/" in end of path is required

define("ROOT_UPLOAD_PATH", "./upload/");


/* V2.0.0 => V3.0.0 OR Change root and set it with users and share files*/
define( "CHANGE_ROOT", false );
define( "LAST_ROOT_DIR_PATH", "../" ); // If you are update from version 2.X.X to V3.X.X please set it "../" else set with your last ROOT_DIR_PATH
?>