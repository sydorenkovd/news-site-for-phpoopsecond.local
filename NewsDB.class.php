<?php
include "INewsDB.class.php";
include 'FetchIterator.class.php';
class NewsDB implements INewsDB, IteratorAggregate{
	const DB_NAME = 'news.db';
	protected $_db;
	protected $_items = [];
	function __construct(){
		if(is_file(self::DB_NAME)){
			$this->_db = new SQLite3(self::DB_NAME);
		}else{
			$this->_db = new SQLite3(self::DB_NAME);
			$sql = "CREATE TABLE msgs(
									id INTEGER PRIMARY KEY AUTOINCREMENT,
									title TEXT,
									category INTEGER,
									description TEXT,
									source TEXT,
									datetime INTEGER
								)";
			$this->_db->exec($sql) or $this->_db->lastErrorMsg();
			$sql = "CREATE TABLE category(
										id INTEGER PRIMARY KEY AUTOINCREMENT,
										name TEXT
									)";
			$this->_db->exec($sql) or $this->_db->lastErrorMsg();
			$sql = "INSERT INTO category(id, name)
						SELECT 1 as id, '��������' as name
						UNION SELECT 2 as id, '��������' as name
						UNION SELECT 3 as id, '�����' as name";
			$this->_db->exec($sql) or $this->_db->lastErrorMsg();	
		}
		$this->getCategory();
	}
	function getIterator(){
		return new ArrayIterator($this->_items);
	}
	protected function getCategory(){
		$sql = "SELECT id, name FROM category";
		$result = $this->_db->query($sql);
		while($r = $result->fetchArray(SQLITE3_ASSOC)){
		$this->_items[$r['id']] = $r['name'];
		}
	}
	function __destruct(){
		unset($this->_db);
	}
	function saveNews($title, $category, $description, $source){
		$dt = time();
		$sql = "INSERT INTO msgs(title, category, description, source, datetime)
					VALUES('$title', $category, '$description', '$source', $dt)";
		$ret = $this->_db->exec($sql);
		if(!$ret)
			return false;
		return true;	
	}	
	protected function db2Arr($data){
		$arr = array();
		while($row = $data->fetchArray(SQLITE3_ASSOC))
			$arr[] = $row;
		return $arr;	
	}
	public function getNews(){
		try{
			$sql = "SELECT msgs.id as id, title, category.name as category, description, source, datetime 
					FROM msgs, category
					WHERE category.id = msgs.category
					ORDER BY msgs.id DESC";
			$result = $this->_db->query($sql);
			if (!is_object($result)) 
				throw new Exception($this->_db->lastErrorMsg());
			// return $this->db2Arr($result);
			$fetchFunction = function() use ($result){
				return $result->fetchArray(SQLITE3_ASSOC);
			};
			return new FetchIterator($fetchFunction);
		}catch(Exception $e){
			return false;
		}
	}	
	public function deleteNews($id){
		try{
			$sql = "DELETE FROM msgs WHERE id = $id";
			$result = $this->_db->exec($sql);
			if (!$result) 
				throw new Exception($this->_db->lastErrorMsg());
			return true;
		}catch(Exception $e){
			echo $e->getMessage();
			return false;
		}
	}
	function clearData($data){
		return $this->_db->escapeString($data); 
	}	
}
?>