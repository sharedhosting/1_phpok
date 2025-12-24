<?php
#------------------------------------------------------------------------------
#[谢您使用情感家园企业站程序：qgweb]
#[本程序由情感开发完成，当前版本：5.0]
#[本程序基于LGPL授权发布]
#[如果您使用正式版，请将授权文件用FTP上传至copyright目录中]
#[官方网站：www.phpok.com   www.qinggan.net]
#[客服邮箱：qinggan@188.com]
#[文件：sqlite.class.php]
#------------------------------------------------------------------------------

#[类库sql]
class qgSQL
{
	var $queryCount = 0;
	var $dbFile;
	var $conn;
	var $stmt;
	var $result;
	var $rsType = SQLITE3_ASSOC;
	var $queryTimes = 0;#[查询时间]

	#[构造函数]
	function qgSQL($dbFile, $dbuser="", $dbpass="", $dbOpenType=false)
	{
		$this->dbFile = $dbFile;
		$this->connect();
		unset($dbFile, $dbuser, $dbpass, $dbOpenType);
	}

	#[兼容PHP5]
	function __construct($dbFile, $dbuser="", $dbpass="", $dbOpenType=false)
	{
		$this->qgSQL($dbFile, $dbuser, $dbpass, $dbOpenType);
		unset($dbFile, $dbuser, $dbpass, $dbOpenType);
	}

	#[连接数据库]
	function connect()
	{
		try {
			$this->conn = new SQLite3($this->dbFile, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
			$this->conn->exec("PRAGMA foreign_keys = ON;"); // 启用外键约束
		} catch (Exception $e) {
			die("数据库连接失败: " . $e->getMessage());
		}
	}

	#[关闭数据库连接]
	function qgClose()
	{
		if($this->conn) {
			return $this->conn->close();
		}
		return true;
	}

	#[兼容PHP5]
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
			$this->qgQuery($sql);
			unset($sql);
		}
		if($this->result) {
			// SQLite3中统计行数需要特殊处理，我们执行COUNT查询
			$rsC = 0;
			while($rows = $this->result->fetchArray($this->rsType)) {
				$rsC++;
			}
			// 重新执行查询以供后续使用
			if($sql) {
				$this->qgQuery($sql);
			}
			return $rsC;
		}
		return 0;
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
	
	#[安全的参数化查询方法 - 防止SQL注入]
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
	
	#[安全的参数化查询获取单行结果方法 - 防止SQL注入]
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
	
	#[安全的参数化查询获取所有结果方法 - 防止SQL注入]
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