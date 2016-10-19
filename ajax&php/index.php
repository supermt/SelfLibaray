<body>
<div id = 'target'>
  本来是这个
</div>
<button onclick="showHint('true')">Ajax初步</button>
<input id="nameHere">输入名字试试</input>
<button onclick="ajaxPost(document.getElementById('nameHere').value)">模拟登录</button>
</body>
<script>
function showHint(str)
{
var xmlhttp;
if (str.length==0)
{
document.getElementById("target").innerHTML="";
return;
}
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
xmlhttp=new XMLHttpRequest();
}
else
{// code for IE6, IE5
xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
if (xmlhttp.readyState==4 && xmlhttp.status==200)
  {
    //alert(xmlhttp.responseText);
    var result = xmlhttp.responseText;
    alert(result);
    if (result.code == 100)
    document.getElementById("target").innerHTML=result.msg;
    else
    document.getElementById("target").innerHTML="Error Occured";
  }
}
xmlhttp.open("GET","ajax.php?target="+str,true);
xmlhttp.send();
}

function ajaxPost(str)
{
  var data = "name="+str;

  var xhr = new XMLHttpRequest();
  xhr.withCredentials = true;

  xhr.addEventListener("readystatechange", function () {
    if (this.readyState === 4) {
      alert(this.responseText);
    }
  });

  xhr.open("POST", "http://localhost:8888/ajax.php");
  xhr.setRequestHeader("content-type", "application/x-www-form-urlencoded");
  xhr.setRequestHeader("cache-control", "no-cache");
  xhr.setRequestHeader("postman-token", "9349f8ab-c498-93c1-c7fc-a4e95d2ac5ba");

  xhr.send(data);
}

</script>
