<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--
CREATE TABLE `student` ( `id` int(11) NOT NULL,  `name` varchar(45) NOT NULL,
`sex` char(1) NOT NULL DEFAULT '1',  `age` int(11) NOT NULL DEFAULT '18',  `
tel` varchar(20) NOT NULL,  `gen` char(1) NOT NULL,
`gra` decimal(4,1) NOT NULL COMMENT '高考成绩',
 `dep` varchar(45) DEFAULT NULL COMMENT '学院',
 `sch` varchar(45) DEFAULT NULL COMMENT '奖学金',
 PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET='utf8';
为保证不会出现乱码，需要保持字符集同为 utf-8
-->
<title>无标题文档</title>
</head>
<body>
<?php
//定义连接常量
define(DB_HOST, 'localhost');
define(DB_USER, 'root');
define(DB_PASS, 'MysqlRoot');
define(DB_DATABASENAME, 'EstateDb');
define(DB_TABLENAME, 'student');

 ?>
 <?php
//新建数据库链接，失败则直接提示错误信息
$conn = mysql_connect(DB_HOST,DB_USER,DB_PASS) or die("connect failed" . mysql_error());
//选择查询的数据库
mysql_select_db(DB_DATABASENAME, $conn);

mysql_query("SET NAMES 'UTF8'");//还是为了保证输出的字符不会出现乱码

$query = "select * from ".DB_TABLENAME." where 1=1 ";

foreach ($_POST as $key => $value) {
  $query = $query."and $key = '$value' ";
}
echo $query;
 ?>

<table border="1">
  <tr>
    <td>考生号</td>
    <td>考生姓名</td>
    <td>性别</td>
    <td>年龄</td>
    <td>电话</td>
    <td>应届/往届</td>
    <td>高考成绩</td>
    <td>学院</td>
    <td>奖学金</td>
  </tr>
  <?php
  //执行并循环提取结果
  $result = mysql_query($query,$conn);
while ($row = mysql_fetch_row($result)) {
  $row['2'] = $row['2']==0?"女":"男";//值的转换
  $row['5'] = $row['5']==0?"应届":"往届";//值的转换

  echo "<tr>";
  foreach ($row as $tableSell ) {
    echo "<td>"."$tableSell"."</td>";
  }
  echo "</tr>";
}

?>

</table>
</body>
</html>
