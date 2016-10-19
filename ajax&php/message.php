<?php
/**
 * 渲染Ajax返回信息
 */
class Result
{
  private $resultArray;
  function __construct()
  {
    $this->resultArray = array("msg"=>"OK","code"=>100);
    //echo "contruct done";
  }

  function needAuth(){
    $this->resultArray["msg"]="Need Auth";
    $this->resultArray["code"]=101;
  }

  function nullObject(){
    $this->resultArray["msg"]="Can Not Find Object";
    $this->resultArray["code"]=104;
  }

  function getResult(){
    echo json_encode($this->resultArray);
  }
}
?>
