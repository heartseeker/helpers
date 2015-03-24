<?php

class DataManager
{
  private static function _connect()
  {
  	try {
  		 return new PDO("mysql:host=localhost;dbname=db_chanel3", 'root','');
  	} catch(PDOException $e){
  		die($e->getMessage());
  	}
   
  }

  /******************************************
  * Description: Fetch multiple rows
  *******************************************/
  public static function fetchAll($sql)
  {
    $db = self::_connect();

    $st = $db->query($sql);

	$rs = $st->fetchAll(PDO::FETCH_ASSOC);
	
    return count($rs)? $rs : array();
  }

  /******************************************
  * Description: Fetch data with query
  *******************************************/
  public static function fetchQuery($sql, $fieldValue)
  {
    $db = self::_connect();

    $st = $db->prepare($sql);
	foreach($fieldValue as $key => $value) {
		$st->bindValue($key, $value);
	}
	$exe = $st->execute();
    $rs = $st->fetchAll(PDO::FETCH_ASSOC);

    return count($rs)? $rs : array();
  }

  /******************************************
  * Description: Delete a data
  *******************************************/
  public static function delete($sql, $fieldValue)
  {
    $db = self::_connect();

    $st = $db->prepare($sql);
	foreach($fieldValue as $key => $value) {
		$st->bindValue($key, $value);
	}
    $st->execute();

    return $st->rowCount()? 1 : 0;
  }

  /******************************************
  * Description: Insert a new data based on an array of data
  *******************************************/
  public static function insert($sql, $values)
  {
    $db = self::_connect();

    $st = $db->prepare($sql);
    $st->execute($values);

    return ($db->lastInsertId())? 1 : 0;
  }

  /******************************************
  * Description: update information
  *******************************************/
  public static function update($sql, $fieldValue)
  {
    $db = self::_connect();

    $st = $db->prepare($sql);
	foreach($fieldValue as $key => $value) {
		$st->bindValue($key, $value);
	}
    $st->execute();

    return ($st->rowCount())? 1 : 0;
  }
}
?>