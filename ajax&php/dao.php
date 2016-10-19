<?php
  /**
   * Dao层模板
   */
  class Dao
  {
    private $mysqli;

    function __construct()
    {
      $this->mysqli = mysqli_connect("localhost","root","MysqlRoot","test") or die("Connection Error");
     }

    function __destruct()
    {
      mysql_close($mysqli);
    }

    function selectToArray($argArray){
      var_dump($argArray);
    }

    function choseTable($tableName){
      mysqli_select_db($tableName,$this->mysqli);
    }

  }
?>
