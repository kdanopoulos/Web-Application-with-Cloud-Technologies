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
  <link rel="stylesheet" type="text/css" href="add_edit.css">
  <style>
  .button{
    width: 100%;
    background: #e50914;
    border: 1px solid #e50914;
  }
  </style>
</head>
<body>
    <div class='wrapper'>
        <header class='header'></header>
       <div class='signup-box-outer'>
            <div class='signup-box-inner'>
                 <h1 id="pageName">Page Name</h1>
                      <div class='input-wrap'>
                        <input id="title" type='text'placeholder='Title' required>
                      </div>
                      <div class='input-wrap'>
                        <input id="sDate" type='date' required>
                      </div>
                      <div class='input-wrap'>
                        <input id="eDate" type='date' required>
                      </div>
                      <div class='input-wrap'>
                        <input id="category" type='text' placeholder='Category' required>
                      </div>
                      <div class='input-wrap'>
                        <button id="submit" class='button'>Button Name</button>
                      </div>
                      <div class='input-wrap'>
                        <button id='cancel' class='cancel'>Cancel</button>
                      </div>
            </div>
       </div>
    </div>
<script>
const pageName = document.getElementById('pageName');
const submitBtn = document.getElementById('submit');
const cancelBtn = document.getElementById('cancel');
const title = document.getElementById('title');
const sDate = document.getElementById('sDate');
const eDate = document.getElementById('eDate');
const category = document.getElementById('category');
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const page = urlParams.get('page');
if(page=="add"){
    pageName.innerHTML = "Add a Movie";
    submitBtn.innerHTML= "Create";
}else if (page=="edit") {
    pageName.innerHTML = "Edit the Movie";
    submitBtn.innerHTML= "Edit";
    const movieId = urlParams.get('movieId');
    var req_body = "num_var=1&mode=GET&url="+"/movies/get/id"+"&var1="+"id"+"&price1="+movieId;
    var xhr = new XMLHttpRequest();
    xhr.open('POST',"../request-send/request.php",true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function(){
      if(this.status== 200){
        var data = JSON.parse(this.responseText);
        title.value = data.title;
        sDate.value = data.start_date.split("T")[0];
        eDate.value = data.end_date.split("T")[0];
        category.value = data.category;
      }
    }
    xhr.onerror = function(){
      console.log('Request error...');
    }
    xhr.send(req_body);
}else{
  location.replace("http://localhost");
}
function cancelBtnListener(){
  location.replace("http://localhost/owner.php?id="+urlParams.get('id'));
}
function editEntity(movie_id,title,sDate,eDate,category){
  var req_body = "num_var=1&mode=GET&url="+"/v2/entities/"+movie_id+"&var1=id&price1=id";
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"../request-send/orion_request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      if(data.error==null){// if the error is null that means that the entity with this id exists
        changeEntity(movie_id,title,sDate,eDate,category);
      }
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function changeEntity(movie_id,title,sDate,eDate,category){
  var body = '{"category": {"type": "String","value": "'+category+'","metadata": {}},"end_date": {"type": "Date","value": "'+eDate+'","metadata": {}},"start_date": {"type": "Date","value": "'+sDate+'","metadata": {}},"title": {"type": "String","value": "'+title+'","metadata": {}}}';
  var new_body = "num_var=0&mode=PATCH&url="+"/v2/entities/"+movie_id+"/attrs&body="+body;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"../request-send/orion_request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      //console.log(this.responseText);
    }}
  xhr.onerror = function(){
    console.log('Request error...');}
  xhr.send(new_body);
}
function submitBtnListener(){
  if(title.value==""){
    alert('The title is empty');
  }else if (sDate.value==""){
    alert('The start date is empty');
  }else if (eDate.value==""){
    alert('The end date is empty');
  }else if (category.value==""){
    alert('The category is empty');
  }else{
    if(page=="add"){
          var req_body = "num_var=5&mode=POST&url="+"/movies/cinema_owner_id"+"&var1="+"title"+"&price1="+title.value+
            "&var2=start_date&price2="+sDate.value+"&var3=end_date&price3="+eDate.value+
            "&var4=category&price4="+category.value+"&var5=owner_id&price5="+urlParams.get('id');
          var xhr = new XMLHttpRequest();
          xhr.open('POST',"../request-send/request.php",true);
          xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhr.onload = function(){
            if(this.status==200){
                //console.log(JSON.parse(this.responseText));
                location.replace("http://localhost/owner.php?id="+urlParams.get('id'));
              }
          }
          xhr.onerror = function(){
            console.log('Request error...');
          }
          xhr.send(req_body);
    }else if(page=="edit"){
          var req_body = "num_var=5&mode=PUT&url="+"/movies/edit"+"&var1="+"title"+"&price1="+title.value+
            "&var2=start_date&price2="+sDate.value+"&var3=end_date&price3="+eDate.value+
            "&var4=category&price4="+category.value+"&var5=movie_id&price5="+urlParams.get('movieId');
          var xhr = new XMLHttpRequest();
          xhr.open('POST',"../request-send/request.php",true);
          xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhr.onload = function(){
            if(this.status== 200){
              console.log(this.responseText);
                //console.log(JSON.parse(this.responseText));
                editEntity(urlParams.get('movieId'),title.value,sDate.value,eDate.value,category.value);
                location.replace("http://localhost/owner.php?id="+urlParams.get('id'));
              }
          }
          xhr.onerror = function(){
            console.log('Request error...');
          }
          xhr.send(req_body);
    }
  }
}
cancelBtn.addEventListener('click',cancelBtnListener);
submitBtn.addEventListener('click',submitBtnListener);
</script>
</body>
</html>
