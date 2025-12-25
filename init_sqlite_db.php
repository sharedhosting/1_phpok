<?php
// 初始化SQLite数据库脚本

require_once("config.php");
require_once("class/sqlite.db.class.php");

echo "开始初始化SQLite数据库...\n";

try {
    // 创建数据库连接
    $DB = new qgSQL($dbFile, $dbUser, $dbPass, false);
    
    // 读取并执行SQL文件
    $sql_content = file_get_contents("sqlite.sql");
    
    if ($sql_content === false) {
        throw new Exception("无法读取sqlite.sql文件");
    }
    
    // 分割SQL语句并执行
    $sql_statements = array_filter(array_map('trim', preg_split('/;[\r\n]+/', $sql_content)));
    
    foreach ($sql_statements as $sql) {
        if (!empty($sql)) {
            $result = $DB->qgQuery($sql);
            if ($result === false) {
                echo "执行SQL失败: $sql\n";
                echo "错误信息: " . print_r($DB->conn->lastErrorMsg(), true) . "\n";
            }
        }
    }
    
    echo "SQLite数据库初始化完成！\n";
    echo "数据库文件: " . $dbFile . "\n";
    
    // 验证表是否创建成功
    $tables = $DB->qgListTables();
    echo "已创建的表:\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    $DB->qgClose();
    
} catch (Exception $e) {
    echo "初始化过程中发生错误: " . $e->getMessage() . "\n";
    exit(1);
}
?>