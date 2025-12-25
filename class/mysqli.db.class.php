<?php
#------------------------------------------------------------------------------
#[感谢您使用情感家园企业站程序：qgweb]
#[本程序由情感开发完成，当前版本：5.0]
#[本程序基于LGPL授权发布]
#[如果您使用正式版，请将授权文件用FTP上传至copyright目录中]
#[官方网站：www.phpok.com   www.qinggan.net]
#[客服邮箱：qinggan@188.com]
#[文件：mysqli.class.php]
#------------------------------------------------------------------------------

#[类库sql]
class qgSQL
{
	var $queryCount = 0;
	var $host;
	var $user;
	var $pass;
	var $data;
	var $port;
	var $charset;
	var $conn;
	var $result;
	var $rsType = MYSQLI_ASSOC;
	var $queryTimes = 0;#[查询时间]

	#[构造函数]
	function qgSQL($dbhost,$dbdata,$dbuser="",$dbpass="",$dbOpenType=false)
	{
		// 解析主机:端口格式
		$host_parts = explode(':', $dbhost);
		$this->host = $host_parts[0];
		$this->port = isset($host_parts[1]) ? $host_parts[1] : 3306;
		$this->user = $dbuser;
		$this->pass = $dbpass;
		$this->data = $dbdata;
		$this->charset = 'utf8';
		$this->connect($dbOpenType);
		unset($dbhost,$dbdata,$dbuser,$dbpass,$dbOpenType);
	}

	#[兼容PHP5/PHP7/PHP8]
	function __construct($dbhost,$dbdata,$dbuser="",$dbpass="",$dbOpenType=false)
	{
		$this->qgSQL($dbhost,$dbdata,$dbuser,$dbpass,$dbOpenType);
		unset($dbhost,$dbdata,$dbuser,$dbpass,$dbOpenType);
	}

	#[连接数据库]
	function connect($dbconn = false)
	{
		$this->conn = mysqli_connect($this->host, $this->user, $this->pass, $this->data, $this->port);
		if (!$this->conn) {
			die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		}

		mysqli_set_charset($this->conn, $this->charset);
		$mysql_version = $this->get_mysql_version();

		if($mysql_version > "4.1") {
			mysqli_query($this->conn, "SET NAMES '" . $this->charset . "'");
		}

		if($mysql_version > "5.0.1") {
			mysqli_query($this->conn, "SET sql_mode=''");
		}

		mysqli_select_db($this->conn, $this->data);
	}

	#[关闭数据库连接]
	function qgClose()
	{
		return mysqli_close($this->conn);
	}

	#[兼容PHP5/PHP7/PHP8]
	function __destruct()
	{
		if ($this->conn) {
			mysqli_close($this->conn);
		}
	}

	function qgQuery($sql,$type="ASSOC")
	{
		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? MYSQLI_NUM : MYSQLI_BOTH) : MYSQLI_ASSOC;
		$this->result = mysqli_query($this->conn, $sql);
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

	function qgBigQuery($sql,$type="ASSOC")
	{
		$this->rsType = $type != "ASSOC" ? ($type == "NUM" ? MYSQLI_NUM : MYSQLI_BOTH) : MYSQLI_ASSOC;
		$this->result = mysqli_query($this->conn, $sql, MYSQLI_USE_RESULT);
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

	function qgGetAll($sql="",$nocache=false)
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
		while($rows = mysqli_fetch_array($this->result,$this->rsType))
		{
			$rs[] = $rows;
		}
		return $rs;
	}

	function qgGetOne($sql = "")
	{
		if($sql)
		{
			$this->qgQuery($sql);
		}
		$rows = mysqli_fetch_array($this->result,$this->rsType);
		return $rows;
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
			return mysqli_insert_id($this->conn);
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
			$rsC = mysqli_num_rows($this->result);
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
		$rsC = mysqli_num_rows($this->result);
		return $rsC;
	}

	function qgNumFields($sql = "")
	{
		if($sql)
		{
			$this->qgQuery($sql);
		}
		return @mysqli_num_fields($this->result);
	}

	function qgListFields($table)
	{
		$rs = @mysqli_fetch_fields(mysqli_query($this->conn, "DESCRIBE `{$table}`"));
		$field_names = array();
		foreach($rs as $field) {
			$field_names[] = $field->name;
		}
		return $field_names;
	}


	function qgListTables()
	{
		$query = mysqli_query($this->conn, "SHOW TABLES");
		$rs = array();
		while($rows = @mysqli_fetch_array($query))
		{
			$rs[] = $rows[0];
		}
		return $rs;
	}

	function qgTableName($table_list,$i)
	{
		return isset($table_list[$i]) ? $table_list[$i] : false;
	}

	function qgEscapeString($char)
	{
		if(!$char)
		{
			return false;
		}
		return @mysqli_real_escape_string($this->conn, $char);
	}
	
	#[安全的参数化查询方法 - 防止SQL注入]
	function prepare_query($sql_template, $params = array())
	{
		$stmt = mysqli_prepare($this->conn, $sql_template);
		if (!$stmt) {
			return false;
		}

		if (!empty($params)) {
			$types = '';
			$values = array();
			foreach($params as $key => $value) {
				if(is_int($value)) {
					$types .= 'i';
				} elseif(is_float($value)) {
					$types .= 'd';
				} else {
					$types .= 's';
				}
				$values[] = $value;
			}
			
			// 绑定参数
			if (!empty($values)) {
				$refs = array();
				$refs[] = &$stmt;
				$refs[] = &$types;
				foreach($values as $key => $value) {
					$refs[] = &$values[$key];
				}
				
				call_user_func_array('mysqli_stmt_bind_param', $refs);
			}
		}

		$result = mysqli_stmt_execute($stmt);
		$this->result = $result ? mysqli_stmt_get_result($stmt) : false;
		$this->queryCount++;
		
		mysqli_stmt_close($stmt);
		return $result !== false;
	}
	
	#[安全的参数化查询获取单行结果方法 - 防止SQL注入]
	function prepare_get_one($sql_template, $params = array())
	{
		$stmt = mysqli_prepare($this->conn, $sql_template);
		if (!$stmt) {
			return false;
		}

		if (!empty($params)) {
			$types = '';
			$values = array();
			foreach($params as $key => $value) {
				if(is_int($value)) {
					$types .= 'i';
				} elseif(is_float($value)) {
					$types .= 'd';
				} else {
					$types .= 's';
				}
				$values[] = $value;
			}
			
			// 绑定参数
			if (!empty($values)) {
				$refs = array();
				$refs[] = &$stmt;
				$refs[] = &$types;
				foreach($values as $key => $value) {
					$refs[] = &$values[$key];
				}
				
				call_user_func_array('mysqli_stmt_bind_param', $refs);
			}
		}

		$result = mysqli_stmt_execute($stmt);
		if ($result) {
			$res_result = mysqli_stmt_get_result($stmt);
			if ($res_result) {
				$row = mysqli_fetch_array($res_result, $this->rsType);
				mysqli_stmt_close($stmt);
				return $row;
			}
		}
		
		mysqli_stmt_close($stmt);
		return false;
	}
	
	#[安全的参数化查询获取所有结果方法 - 防止SQL注入]
	function prepare_get_all($sql_template, $params = array())
	{
		$stmt = mysqli_prepare($this->conn, $sql_template);
		if (!$stmt) {
			return false;
		}

		if (!empty($params)) {
			$types = '';
			$values = array();
			foreach($params as $key => $value) {
				if(is_int($value)) {
					$types .= 'i';
				} elseif(is_float($value)) {
					$types .= 'd';
				} else {
					$types .= 's';
				}
				$values[] = $value;
			}
			
			// 绑定参数
			if (!empty($values)) {
				$refs = array();
				$refs[] = &$stmt;
				$refs[] = &$types;
				foreach($values as $key => $value) {
					$refs[] = &$values[$key];
				}
				
				call_user_func_array('mysqli_stmt_bind_param', $refs);
			}
		}

		$result = mysqli_stmt_execute($stmt);
		$rs = array();
		if ($result) {
			$res_result = mysqli_stmt_get_result($stmt);
			if ($res_result) {
				while($rows = mysqli_fetch_array($res_result, $this->rsType)) {
					$rs[] = $rows;
				}
			}
		}
		
		mysqli_stmt_close($stmt);
		return $rs;
	}

	function get_mysql_version()
	{
		return mysqli_get_server_info($this->conn);
	}
}
?>