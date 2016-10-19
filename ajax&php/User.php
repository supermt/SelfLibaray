<?php
include 'dao.php';
/**
 * 用户类，专门用于处理用户的操作
 */
class User extends Dao
{

  function __construct()
  {
    parent::__construct();
    // var_dump(mysqli_error());
    parent::choseTable("users");
    // echo "Child Class In Position";
  }

  function checkUser($username,$password){
    //逻辑判断
    if ($username == 'hello')    return true;
    else return false;
  }

}


?>
