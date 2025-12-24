#!/bin/bash

# 输入和输出文件路径
MYSQL_SQL_FILE="mysql.sql"
SQLITE_DB_FILE="data/database.sqlite"

# 检查依赖工具
check_dependencies() {
    command -v sqlite3 || { echo "Error: sqlite3 not found"; exit 1; }
    [ -f "$MYSQL_SQL_FILE" ] || { echo "Error: $MYSQL_SQL_FILE not found"; exit 1; }
}

# 创建数据目录
create_data_dir() {
    mkdir -p data
}

# 转换SQL文件为SQLite兼容格式
convert_sql() {
    local temp_sql=$(mktemp)
    
    cat "$MYSQL_SQL_FILE" | \
    sed 's/`//g' | \
    sed 's/AUTO_INCREMENT//g' | \
    sed 's/ENGINE=MyISAM//g' | \
    sed 's/ENGINE=InnoDB//g' | \
    sed 's/DEFAULT CHARSET=utf8//g' | \
    sed 's/DEFAULT CHARSET=utf8mb4//g' | \
    sed 's/CHARSET=utf8//g' | \
    sed 's/CHARSET=utf8mb4//g' | \
    sed 's/COLLATE=utf8_general_ci//g' | \
    sed 's/COLLATE=utf8mb4_general_ci//g' | \
    sed 's/utf8_general_ci//g' | \
    sed 's/utf8mb4_general_ci//g' | \
    sed 's/COMMENT.*[A-Z]//g' | \
    sed 's/COMMENT.*$//g' | \
    sed 's/,$/)/' | \
    sed 's/CREATE TABLE/CREATE TABLE IF NOT EXISTS/' | \
    sed 's/int(11)/INTEGER/' | \
    sed 's/int(10)/INTEGER/' | \
    sed 's/int(8)/INTEGER/' | \
    sed 's/int(5)/INTEGER/' | \
    sed 's/int(4)/INTEGER/' | \
    sed 's/int(2)/INTEGER/' | \
    sed 's/int(1)/INTEGER/' | \
    sed 's/tinyint(4)/INTEGER/' | \
    sed 's/tinyint(3)/INTEGER/' | \
    sed 's/tinyint(2)/INTEGER/' | \
    sed 's/tinyint(1)/INTEGER/' | \
    sed 's/smallint(6)/INTEGER/' | \
    sed 's/smallint(5)/INTEGER/' | \
    sed 's/mediumint(9)/INTEGER/' | \
    sed 's/mediumint(8)/INTEGER/' | \
    sed 's/varchar(/TEXT(/' | \
    sed 's/longtext/TEXT/' | \
    sed 's/mediumtext/TEXT/' | \
    sed 's/tinytext/TEXT/' | \
    sed 's/text/TEXT/' | \
    sed 's/datetime/TEXT/' | \
    sed 's/timestamp/TEXT/' | \
    sed 's/time/TEXT/' | \
    sed 's/date/TEXT/' | \
    sed 's/binary/BLOB/' | \
    sed 's/varbinary/BLOB/' | \
    sed 's/blob/BLOB/' | \
    sed 's/double/REAL/' | \
    sed 's/float/REAL/' | \
    sed 's/decimal/REAL/' | \
    sed 's/numeric/REAL/' > "$temp_sql"
    
    # 创建SQLite数据库
    sqlite3 "$SQLITE_DB_FILE" < "$temp_sql"
    
    rm "$temp_sql"
}

# 主流程
main() {
    check_dependencies
    create_data_dir
    
    if [ -f "$SQLITE_DB_FILE" ]; then
        rm "$SQLITE_DB_FILE"
    fi
    
    convert_sql
    
    echo "转换完成：$SQLITE_DB_FILE"
}

main
