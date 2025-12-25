<?php
// 数据库类型: 'sqlite' 或 'mysql'
$dbType = "sqlite";

// SQLite数据库信息
$dbFile = "data/database.sqlite";

// MySQL数据库信息 (当$dbType为'mysql'时使用)
$dbhost = "localhost:3306";
$dbname = "your_database_name";
$dbUser = "your_username";
$dbPass = "your_password";

// 数据表前缀
$prefix = "sino_";

// 是否启用调试
$viewbug = 0;

// 是否启用伪静态页功能，使用为true，不使用为false
$urlRewrite = false;

// 后台是否启用验证码功能，使用为true，不使用为false
$isCheckCode = true;
?>