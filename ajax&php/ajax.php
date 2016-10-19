<?php
  include('message.php');
  include('User.php');
  if ($_POST['name']){
    $dao = new User();
    if (!$dao->checkUser($_POST['name'],$_POST['pass'])){
      $result = new Result();
      //var_dump($result);
      $result->needAuth();
      $result->getResult();
    }
    else{
      $result = new Result();
        //var_dump($result);
        $result->getResult();
    }
  }
  else {
    if ($_GET['target']=="true") {
      // $resultArray = array('msg'=>"OK",'code'=>100);
      // echo json_encode($resultArray);
      $result = new Result();
      //var_dump($result);
      $result->getResult();
    }
    else {
      $result = new Result();
      //var_dump($result);
      $result->nullObject();
      $result->getResult();
    }
  }




?>
