<?php
session_start();
if($_SESSION['token']==''){
  header('Location: http://localhost?youHaveNoAccess');
  exit;
}
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://keyrock:3000/user?access_token='.$_SESSION['token'],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Cookie: session=eyJyZWRpciI6Ii8ifQ==; session.sig=TqcHvLKCvDVxuMk5xVfrKEP-GSQ'
  ),
));
$response = curl_exec($curl);
if(curl_errno($curl)!=0){
  header('Location: http://localhost?errorComunicatingKeyrock');
  exit;
}
curl_close($curl);
$json = json_decode($response, true);
if($json['error']=='invalid_token'){
  header('Location: http://localhost?invalid_token');
  exit;
}elseif($json['roles'][0]['name']!='CinemaOwner'){
  $_SESSION['access'] = 'cineme owners';
  header("Location: ../deniedAccess.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Cinema Application</title>
  <link rel="stylesheet" type="text/css" href="owner/owner.css">
  <style>
  table {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 200%;
  }
  td,th{
    border: 1px solid #ddd;padding: 8px;
  }
  tr:nth-child(even){background-color: #333;}
  th{
    padding-top: 12px;padding-bottom: 12px;text-align:left;color: white;background-color: #b30217;
  }
  .add{
    position: fixed;
    top:18%; left: 71%;
    width: 24px; height: 24px;background: url(images/add.png);background-color:#fff;
     background-position: center;background-size: 24px 24px;background-repeat: no-repeat;
     border: 1px solid #333;padding-left: 15px;box-sizing: border-box;
  }
  </style>
</head>
<body>
  <header class="header">
      <div class="left-menu">Menu
        <div class="dropdown-menu">
          <?php
          echo "<a href='welcome.php?token_type=".$_SESSION['token_type']."&expires_at=".$_SESSION['expires_at']."&token=".$_SESSION['token']."&state=".$_SESSION['state']."'style='color:#fff;text-decoration: none;'>Home</a>";
          ?>
        </div>
        <div class="page"><h1>My Cinema</h1></div>
      </div>
      <p class="hello-user" style="color:#fff;position:absolute;top:5px;left:75%;">
        <?php echo $json['username']." (".$json['roles'][0]['name'].")"; ?>
      </p>
      <form action="welcome/logout.php" method="post">
        <button type="submit" name="logout" style="padding-right:4%;">Logout</button>
      </form>
  </header>
  <div class="background"></div>
  <div class="body">
    <input id="placeholder" type="text" name="search" placeholder="search">
    <div class="option-menu" style="top:2.2%;">
    <label for="option">Search by: </label>
    <select class="search-option" id="option" name="search-option">
        <option disabled selected value="empty"> -- select an option -- </option>
        <option value="category">Movie Category</option>
        <option value="date">Projection Date (yyyy/mm/dd)</option>
        <option value="title">Title Of Movie</option>
    </select>
  </div>
  <button id="search-btn" style="height: 50px;border-radius: 3px;background: #333;color: #fff;border: 1px solid #333;
  padding-left: 15px;padding-right: 15px;box-sizing: border-box;position:absolute;top:5%;left:62%;" type="submit">Search</button>
  <button id="add-btn" name='add' class="add"></button>
  <div id="inside-table" class="table" style="position:absolute;top:15%;left:2%;">
    <table style="font-family: Arial,Helvetica,sans-serif;border-collapse: collapse;width: 200%;">
      <tr><th>Title</th><th>Start Date</th><th>End Date</th><th>Category</th><th>&nbsp;</th><th>&nbsp;</th></tr>
    </table>
  </div>
  </div>
</body>
<script>
const searchBtn = document.getElementById('search-btn');
const addBtn = document.getElementById('add-btn');
const searchBy = document.getElementById('option');
const placeholder = document.getElementById('placeholder');
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const id = urlParams.get('id');
function createLine(title,start_date,end_date,category,id){
  var output = '';
  output+="<tr> <td>"+title+"</td> <td>"+start_date.split("T")[0]+"</td> <td>"+end_date.split("T")[0]+"</td><td>"+category+"</td> ";
  output+="<td><button class='edit' value='"+id+"'"+
   "style='width: 24px; height: 24px;background: url(images/edit.png);background-color:#333;"+
   "background-position: center;background-size: 24px 24px;background-repeat: no-repeat;"+
   "border: 1px solid #333;padding-left: 15px;box-sizing: border-box;'></button></td>";
  output+="<td><button class='delete' value='"+id+"'"+
    "style='width: 24px; height: 24px;background: url(images/delete.png);background-color:#333;"+
    "background-position: center;background-size: 24px 24px;background-repeat: no-repeat;"+
    "border: 1px solid #333;padding-left: 15px;box-sizing: border-box;'></button></td></tr>";
  return output;
}
function printAll(){
  var req_body = "num_var=1&mode=GET&url="+"/movies/all-cinema"+"&var1="+"owner_id"+"&price1="+id;
  var request = new XMLHttpRequest();
  request.open('POST',"request-send/request.php",true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      var output = '';
      for(let i of data){
        output+=createLine(i.title,i.start_date,i.end_date,i.category,i._id);
      }
      output="<table style='font-family: Arial,Helvetica,sans-serif;border-collapse: collapse;width: 200%;'>"+
      "<tr><th>Title</th><th>Start Date</th><th>End Date</th><th>Category</th><th>&nbsp;</th><th>&nbsp;</th></tr>"+
      output+"</table>";
      document.getElementById('inside-table').innerHTML = output;
      addActionListenerToEditDelete();
    }
  }
  request.onerror = function(){
    console.log('Request error...');
  }
  request.send(req_body);
}
function searchByCategory(category){
  var req_body = "num_var=2&mode=GET&url="+"/movies/category-cinema"+"&var1="+"owner_id"+"&price1="+id+
  "&var2=category&price2="+category;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      var output = '';
      for(let i of data){
        output+=createLine(i.title,i.start_date,i.end_date,i.category,i._id);
      }
      output="<table style='font-family: Arial,Helvetica,sans-serif;border-collapse: collapse;width: 200%;'>"+
      "<tr><th>Title</th><th>Start Date</th><th>End Date</th><th>Category</th><th>&nbsp;</th><th>&nbsp;</th></tr>"+
      output+"</table>";
      document.getElementById('inside-table').innerHTML = output;
      addActionListenerToEditDelete();
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function searchByDate(date){
  var req_body = "num_var=2&mode=GET&url="+"/movies/date-cinema"+"&var1="+"owner_id"+"&price1="+id+
  "&var2=ddate&price2="+date;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      var output = '';
      for(let i of data){
        output+=createLine(i.title,i.start_date,i.end_date,i.category,i._id);
      }
      output="<table style='font-family: Arial,Helvetica,sans-serif;border-collapse: collapse;width: 200%;'>"+
      "<tr><th>Title</th><th>Start Date</th><th>End Date</th><th>Category</th><th>&nbsp;</th><th>&nbsp;</th></tr>"+
      output+"</table>";
      document.getElementById('inside-table').innerHTML = output;
      addActionListenerToEditDelete();
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function searchByTitle(title){
  var req_body = "num_var=2&mode=GET&url="+"/movies/title-cinema"+"&var1="+"owner_id"+"&price1="+id+
  "&var2=title&price2="+title;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      var output = '';
      for(let i of data){
        output+=createLine(i.title,i.start_date,i.end_date,i.category,i._id);
      }
      output="<table style='font-family: Arial,Helvetica,sans-serif;border-collapse: collapse;width: 200%;'>"+
      "<tr><th>Title</th><th>Start Date</th><th>End Date</th><th>Category</th><th>&nbsp;</th><th>&nbsp;</th></tr>"+
      output+"</table>";
      document.getElementById('inside-table').innerHTML = output;
      addActionListenerToEditDelete();
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function addBtnListener(){
  var url = "http://localhost/owner/add_edit.php";
  location.replace(url+"?id="+id+"&page=add");
}
function searchBtnListener(){
  if(placeholder.value==''){ // placeholder is empty
    printAll();
  }
  else{ //placeholder is not empty
    if(searchBy.value == "empty"){ //searchBy is empty
      alert('Search by option is not selected!');
    }
    else if(searchBy.value == "category"){
      searchByCategory(placeholder.value);
    }
    else if(searchBy.value == "date"){
      searchByDate(placeholder.value);
    }
    else if(searchBy.value == "title"){
      searchByTitle(placeholder.value);
    }
  }
}
function editMovie(){
  var url = "http://localhost/owner/add_edit.php";
  location.replace(url+"?id="+id+"&page=edit&movieId="+this.value);
}
function deleteMovie(){
  var req_body = "num_var=1&mode=DELETE&url="+"/movies/delete"+"&var1="+"movie_id"+"&price1="+this.value;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      //console.log(JSON.parse(this.responseText));
      printAll();
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function addActionListenerToEditDelete(){
  var buttons = document.getElementsByClassName("edit");
  for(var i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener('click',editMovie);
  }
  buttons = document.getElementsByClassName("delete");
  for(var i = 0; i < buttons.length; i++) {
    buttons[i].addEventListener('click',deleteMovie);
  }
}

searchBtn.addEventListener('click',searchBtnListener);
addBtn.addEventListener('click',addBtnListener);
</script>
</html>
