<?php
#------------------------------------------------------------------------------
// 感谢您使用情感家园企业站程序：qgweb
// 本程序由情感开发完成，当前版本：5.0
// 本程序基于LGPL授权发布
// 如果您使用正式版，请将授权文件用FTP上传至copyright目录中
// 官方网站：www.phpok.com   www.qinggan.net
// 客服邮箱：qinggan@188.com
// 文件：sqlite.class.php
#------------------------------------------------------------------------------

// 类库sql
class qgSQL
{
	var $queryCount = 0;
	var $dbFile;
	var $conn;
	var $stmt;
	var $result;
	var $rsType = SQLITE3_ASSOC;
	var $queryTimes = 0;#[查询时间

	//
	function qgSQL($dbFile, $dbuser="", $dbpass="", $dbOpenType=false)
	{
		$this->dbFile = $dbFile;
		$this->connect();
		unset($dbFile, $dbuser, $dbpass, $dbOpenType);
	}

	//
	function __construct($dbFile, $dbuser="", $dbpass="", $dbOpenType=false)
	{
		$this->qgSQL($dbFile, $dbuser, $dbpass, $dbOpenType);
		unset($dbFile, $dbuser, $dbpass, $dbOpenType);
	}

	//
	function connect()
	{
		try {
			$this->conn = new SQLite3($this->dbFile, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
			$this->conn->exec("PRAGMA foreign_keys = ON;"); // 启用外键约束
		} catch (Exception $e) {
			die("数据库连接失败: " . $e->getMessage());
		}
	}

	//
	function qgClose()
	{
		if($this->conn) {
			return $this->conn->close();
		}
		return true;
	}

	//
	function __destruct()
	{
		$this->qgClose();
	}

	function qgQuery($sql, $type="ASSOC")
	{
		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? SQLITE3_NUM : SQLITE3_BOTH) : SQLITE3_ASSOC;
		$this->result = $this->conn->query($sql);
		$this->queryCount++;
		if($this->result)
		{
			return $this->result;
		}
		else
		{
			return false;
		}
	}

	function qgBigQuery($sql, $type="ASSOC")
	{
		// SQLite3中没有非缓冲查询，直接使用普通查询
		return $this->qgQuery($sql, $type);
	}

	function qgGetAll($sql="", $nocache=false)
	{
		if($sql)
		{
			if($nocache)
			{
				$this->qgBigQuery($sql);
			}
			else
			{
				$this->qgQuery($sql);
			}
		}
		$rs = array();
		if($this->result) {
			while($rows = $this->result->fetchArray($this->rsType))
			{
				$rs[] = $rows;
			}
		}
		return $rs;
	}

	function qgGetOne($sql = "")
	{
		if($sql)
		{
			$this->qgQuery($sql);
		}
		if($this->result) {
			$rows = $this->result->fetchArray($this->rsType);
			return $rows;
		}
		return false;
	}

	function qgInsertID($sql="")
	{
		if($sql)
		{
			$rs = $this->qgGetOne($sql);
			return $rs;
		}
		else
		{
			return $this->conn->lastInsertRowID();
		}
	}

	function qgInsert($sql)
	{
		$this->result = $this->qgQuery($sql);
		$id = $this->qgInsertID();
		return $id;
	}

	function qg_count($sql="")
	{
		if($sql)
		{
			$this->qgQuery($sql,"NUM");
			$rs = $this->qgGetOne();
			return $rs[0];
		}
		else
		{
			$rsC = $this->result->numColumns();
			return $rsC;
		}
	}

	function qgCount($sql = "")
	{
		if($sql)
		{
			// 如果传入了SQL，我们先执行原始查询以保持与原有行为一致
			$this->qgQuery($sql);
			// 然后执行COUNT查询来获取行数
			// 从原始SQL构建COUNT查询
			$count_sql = "SELECT COUNT(*) as count FROM (" . $sql . ") AS count_table";
			$count_result = $this->qgGetOne($count_sql);
			return isset($count_result['count']) ? $count_result['count'] : 0;
		}
		else
		{
			// 如果没有传入SQL，说明之前已经执行了查询，我们尝试统计结果
			// 但SQLite3的结果集无法直接获取行数，我们需要重新查询COUNT
			// 这种情况下返回0或抛出异常，因为无法确定之前查询的行数
			return 0;
		}
	}

	function qgNumFields($sql = "")
	{
		if($sql)
		{
			$this->qgQuery($sql);
		}
		if($this->result) {
			return $this->result->numColumns();
		}
		return 0;
	}

	function qgListFields($table)
	{
		$sql = "PRAGMA table_info(`{$table}`)";
		$result = $this->qgGetAll($sql);
		$fields = array();
		foreach($result as $row) {
			$fields[] = $row['name'];
		}
		return $fields;
	}


	function qgListTables()
	{
		$sql = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
		$result = $this->qgGetAll($sql);
		$tables = array();
		foreach($result as $row) {
			$tables[] = $row['name'];
		}
		return $tables;
	}

	function qgTableName($table_list, $i)
	{
		// 在SQLite中，我们直接返回表名列表中的元素
		return isset($table_list[$i]) ? $table_list[$i] : false;
	}

	function qgEscapeString($char)
	{
		if(!$char)
		{
			return false;
		}
		return $this->conn->escapeString($char);
	}
	
	// - 防止SQL注入
	function prepare_query($sql_template, $params = array())
	{
		$stmt = $this->conn->prepare($sql_template);
		if (!$stmt) {
			return false;
		}
		
		foreach($params as $key => $value) {
			$param_type = is_numeric($value) ? SQLITE3_INTEGER : SQLITE3_TEXT;
			$stmt->bindValue(':'.$key, $value, $param_type);
		}
		
		$this->result = $stmt->execute();
		$this->queryCount++;
		return $this->result !== false;
	}
	
	// - 防止SQL注入
	function prepare_get_one($sql_template, $params = array())
	{
		$stmt = $this->conn->prepare($sql_template);
		if (!$stmt) {
			return false;
		}
		
		foreach($params as $key => $value) {
			$param_type = is_numeric($value) ? SQLITE3_INTEGER : SQLITE3_TEXT;
			$stmt->bindValue(':'.$key, $value, $param_type);
		}
		
		$result = $stmt->execute();
		if ($result) {
			return $result->fetchArray(SQLITE3_ASSOC);
		}
		return false;
	}
	
	// - 防止SQL注入
	function prepare_get_all($sql_template, $params = array())
	{
		$stmt = $this->conn->prepare($sql_template);
		if (!$stmt) {
			return false;
		}
		
		foreach($params as $key => $value) {
			$param_type = is_numeric($value) ? SQLITE3_INTEGER : SQLITE3_TEXT;
			$stmt->bindValue(':'.$key, $value, $param_type);
		}
		
		$result = $stmt->execute();
		$rs = array();
		if ($result) {
			while($rows = $result->fetchArray(SQLITE3_ASSOC)) {
				$rs[] = $rows;
			}
		}
		return $rs;
	}

	function get_sqlite_version()
	{
		return $this->conn->version()['versionString'];
	}
}
?>