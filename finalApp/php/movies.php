<?php
session_start();
if($_SESSION['token']==''){
  header('Location: http://localhost?youHaveNoAccess');
  exit;
}
/*$now = new DateTime('now');
//echo $now;
echo $_SESSION['expires_at'];
if($now>$_SESSION['expires_at']){
  //header('Location: http://localhost?tokenExpired');
  //exit;
}*/
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
//echo curl_getinfo($curl) . '<br/>';
//echo curl_errno($curl) . '<br/>';
//echo curl_error($curl) . '<br/>';
curl_close($curl);
$json = json_decode($response, true);
if($json['error']=='invalid_token'){
  header('Location: http://localhost?invalid_token');
  exit;
}elseif($json['roles'][0]['name']!='User'){
  $_SESSION['access'] = 'users';
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
  <link rel="stylesheet" type="text/css" href="movies/movies.css">
  <style>
  #myTable {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 200%;
  }
  #myTable td, #myTable th {
    border: 1px solid #ddd;
    padding: 8px;
  }

  #myTable tr:nth-child(even){background-color: #333;}

  #myTable th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    color: white;
    background-color: #b30217;
  }
  </style>
</head>
<body>
  <div class="wrapper">
      <header class="header">
          <div class="left-menu">Menu
            <div class="dropdown-menu">
              <?php
              echo "<a href='welcome.php?token_type=".$_SESSION['token_type']."&expires_at=".$_SESSION['expires_at']."&token=".$_SESSION['token']."&state=".$_SESSION['state']."'style='color:#fff;text-decoration: none;'>Home</a>";
              ?>
            </div>
            <div class="page"><h1>Movies</h1></div>
          </div>
          <p class="hello-user" style="color:#fff;position:absolute;top:5px;left:75%;">
            <?php
            echo $json['username']." (".$json['roles'][0]['name'].")";
            //$id = $json['id'];
            //echo $response;
            ?>
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
            <option value="cinema">Cinema Name</option>
            <option value="category">Movie Category</option>
            <option value="date">Projection Date (yyyy/mm/dd)</option>
            <option value="title">Title Of Movie</option>
        </select>
      </div>
      <button id="search-btn" style="height: 50px;border-radius: 3px;background: #333;color: #fff;border: 1px solid #333;
      padding-left: 15px;padding-right: 15px;box-sizing: border-box;position:absolute;top:5%;left:62%;" type="submit">Search</button>
      <button id="fav-btn" style="height: 50px;border-radius: 3px;background: #e50914;color: #fff;border: 1px solid #333;
      padding-left: 15px;padding-right: 15px;box-sizing: border-box;position:absolute;top:5%;left:92%;" type="submit">Favorites</button>
      <div id='inside-table' class="table" style="position:absolute;top:15%;left:2%;width:45%;">
  </div>
</body>
<script>
const searchBtn = document.getElementById('search-btn');
const favBtn = document.getElementById('fav-btn');
const searchBy = document.getElementById('option');
const placeholder = document.getElementById('placeholder');
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const id = urlParams.get('id');
getMyFavoriteMoviesId();
function getMyFavoriteMoviesId(){
  var req_body = "num_var=1&mode=GET&url="+"/movies/all/favorite/id"+"&var1="+"user_id"+"&price1="+id;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      var list_of_movies = [];
      for(let curfav of data){
        list_of_movies.push(curfav.movie_id);
      }
      checkForNotifications(list_of_movies,id);
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');}
  xhr.send(req_body);
}
function checkForNotifications(list_of_movies,user_id){
  var req_body = "num_var=1&mode=GET&url="+"/notifications/all"+"&var1=trash&price1=trash";
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      for(let curNotif of data){
        for(let curMovId of list_of_movies){
          if(curNotif.movie_id==curMovId){
            for(let curUserInside of curNotif.users_id){
              if(curUserInside.id==user_id){
                alert("The movie: "+curNotif.title+" at cinema: "+curNotif.cinema_name+" has been modified!.\nNew information:\n title= "+curNotif.title+", start date= "+curNotif.start_date+",\nend date= "+curNotif.end_date+", category= "+curNotif.category);
                deleteMyNotification(user_id,curMovId);
              }
            }
          }
        }
      }
    }}
  xhr.onerror = function(){
    console.log('Request error...');}
  xhr.send(req_body);
}
function deleteMyNotification(user_id,movie_id){
  var req_body = "num_var=2&mode=DELETE&url="+"/notifications/delete/id"+"&var1=user_id&price1="+user_id+"&var2=movie_id&price2="+movie_id;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    //console.log(this.responseText);
    if(this.status== 200){
      //console.log(this.responseText);
  }}
  xhr.onerror = function(){
    console.log('Request error...');}
  xhr.send(req_body);
}
function createLine(title,start_date,end_date,category,cinema,owner,fav,id){
  var output = '';
  output+="<tr> <td>"+title+"</td> <td>"+start_date.split("T")[0]+"</td> <td>"+end_date.split("T")[0]+"</td> <td>"+
  category+"</td> <td>"+cinema+"</td> <td>"+owner+"</td> ";
  if(fav==1){
    output+="<td><button class='fav-remove' value='"+id+"' style='width: 24px; height: 24px;background: url(images/heart.png);background-color:#333;"+
    "background-position: center;background-size: 24px 24px;background-repeat: no-repeat;border: 1px solid #333;padding-left: 15px;box-sizing: border-box;'></button></td></tr>";
  }else{
    output+="<td><button class='fav-add' value='"+id+"' style='width: 24px; height: 24px;background: url(images/heart2.png);background-color:#333;"+
    "background-position: center;background-size: 24px 24px;background-repeat: no-repeat;border: 1px solid #333;padding-left: 15px;box-sizing: border-box;'></button></td></tr>";
  }
  return output;
}
function printAll(){
  var req_body = "num_var=1&mode=GET&url="+"/movies/all"+"&var1="+"user_id"+"&price1="+id;
  var request = new XMLHttpRequest();
  request.open('POST',"request-send/request.php",true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      var output = '';
      for(let i of data){
        output+=createLine(i.title,i.start_date,i.end_date,i.category,i.cinema,i.owner,i.favorite,i.movie_id);
      }
      output="<table id = 'myTable'><tr> <th>Title</th> <th>Start Date</th> <th>End Date</th> <th>Category</th> <th>Cinema</th> <th>Owner</th><th>&nbsp;</th> </tr>"+output+"</table>";
      document.getElementById('inside-table').innerHTML = output;
      addActionListenerToFavButtons();
    }
  }
  request.onerror = function(){
    console.log('Request error...');
  }
  request.send(req_body);
}
function printAllFavorite(){
  var req_body = "num_var=1&mode=GET&url="+"/movies/all/favorite"+"&var1="+"user_id"+"&price1="+id;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      var output = '';
      for(let i of data){
        output+=createLine(i.title,i.start_date,i.end_date,i.category,i.cinema,i.owner,i.favorite,i.movie_id);
      }
      output="<table id = 'myTable'><tr> <th>Title</th> <th>Start Date</th> <th>End Date</th> <th>Category</th> <th>Cinema</th> <th>Owner</th><th>&nbsp;</th> </tr>"+output+"</table>";
      document.getElementById('inside-table').innerHTML = output;
      addActionListenerToFavButtons();
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function searchByCinema(cinema){
  var req_body = "num_var=2&mode=GET&url="+"/movies/cinemaName"+"&var1="+"user_id"+"&price1="+id+
  "&var2=cinema_name&price2="+cinema;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      var output = '';
      for(let i of data){
        output+=createLine(i.title,i.start_date,i.end_date,i.category,i.cinema,i.owner,i.favorite,i.movie_id);
      }
      output="<table id = 'myTable'><tr> <th>Title</th> <th>Start Date</th> <th>End Date</th> <th>Category</th> <th>Cinema</th> <th>Owner</th><th>&nbsp;</th> </tr>"+output+"</table>";
      document.getElementById('inside-table').innerHTML = output;
      addActionListenerToFavButtons();
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function searchByCategory(category){
  var req_body = "num_var=2&mode=GET&url="+"/movies/category"+"&var1="+"user_id"+"&price1="+id+
  "&var2=category&price2="+category;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      var output = '';
      for(let i of data){
        output+=createLine(i.title,i.start_date,i.end_date,i.category,i.cinema,i.owner,i.favorite,i.movie_id);
      }
      output="<table id = 'myTable'><tr> <th>Title</th> <th>Start Date</th> <th>End Date</th> <th>Category</th> <th>Cinema</th> <th>Owner</th><th>&nbsp;</th> </tr>"+output+"</table>";
      document.getElementById('inside-table').innerHTML = output;
      addActionListenerToFavButtons();
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function searchByDate(date){
  var req_body = "num_var=2&mode=GET&url="+"/movies/date"+"&var1="+"user_id"+"&price1="+id+
  "&var2=ddate&price2="+date;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      var output = '';
      for(let i of data){
        output+=createLine(i.title,i.start_date,i.end_date,i.category,i.cinema,i.owner,i.favorite,i.movie_id);
      }
      output="<table id = 'myTable'><tr> <th>Title</th> <th>Start Date</th> <th>End Date</th> <th>Category</th> <th>Cinema</th> <th>Owner</th><th>&nbsp;</th> </tr>"+output+"</table>";
      document.getElementById('inside-table').innerHTML = output;
      addActionListenerToFavButtons();
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function searchByTitle(title){
  var req_body = "num_var=2&mode=GET&url="+"/movies/title"+"&var1="+"user_id"+"&price1="+id+
  "&var2=title&price2="+title;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status==200){
      var data = JSON.parse(this.responseText);
      var output = '';
      for(let i of data){
        output+=createLine(i.title,i.start_date,i.end_date,i.category,i.cinema,i.owner,i.favorite,i.movie_id);
      }
      output="<table id = 'myTable'><tr> <th>Title</th> <th>Start Date</th> <th>End Date</th> <th>Category</th> <th>Cinema</th> <th>Owner</th><th>&nbsp;</th> </tr>"+output+"</table>";
      document.getElementById('inside-table').innerHTML = output;
      addActionListenerToFavButtons();
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
// HTTP Statuses
// 200: Ok
// 403: Forbidden
// 404: Not Found
function favBtnListener(){
  printAllFavorite();
}
function searchBtnListener(){
  if(placeholder.value==''){ // placeholder is empty
    printAll();
  }
  else{ //placeholder is not empty
    if(searchBy.value == "empty"){ //searchBy is empty
      alert('Search by option is not selected!');
    }
    else if(searchBy.value == "cinema"){
      searchByCinema(placeholder.value);
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
function addFavorite(){
  var movie_id = this.value;
  var req_body = "num_var=2&mode=POST&url="+"/favorites"+"&var1="+"user_id"+"&price1="+id+
  "&var2=movie_id&price2="+movie_id;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      //console.log(this.responseText);
      //var data = JSON.parse(this.responseText);
      printAll(id);
      addNotification(movie_id);
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function removeFavorite(){
  var movie_id = this.value;
  var req_body = "num_var=2&mode=DELETE&url="+"/favorites/delete"+"&var1="+"user_id"+"&price1="+id+
  "&var2=movie_id&price2="+movie_id;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      //console.log(this.responseText);
      //var data = JSON.parse(this.responseText);
      printAll(id);
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
function addActionListenerToFavButtons(){
  var favButtons = document.getElementsByClassName("fav-add");
  for(var i = 0; i < favButtons.length; i++) {
    favButtons[i].addEventListener('click',addFavorite);
  }
  favButtons = document.getElementsByClassName("fav-remove");
  for(var i = 0; i < favButtons.length; i++) {
    favButtons[i].addEventListener('click',removeFavorite);
  }
}

searchBtn.addEventListener('click',searchBtnListener);
favBtn.addEventListener('click',favBtnListener);
// Orion Functions
function addEntity(body){
  var new_body = "num_var=0&mode=POST&url="+"/v2/entities&body="+body;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/orion_request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      //console.log(this.responseText);
    }}
  xhr.onerror = function(){
    console.log('Request error...');}
  xhr.send(new_body);
}
function addSubscription(body){
  var new_body = "num_var=0&mode=POST&url="+"/v2/subscriptions&body="+body;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/orion_request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      //console.log(this.responseText);
    }}
  xhr.onerror = function(){
    console.log('Request error...');}
  xhr.send(new_body);
}
function deleteSubscription(){
}
function deleteEntity(id){
  var req_body = "num_var=1&mode=DELETE&url="+"/v2/entities/"+id+"&var1=trash&price1=trash";
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/orion_request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      //console.log(this.responseText);
    }}
  xhr.onerror = function(){
    console.log('Request error...');}
  xhr.send(req_body);
}
function createSubscription(movie_id){
  var req_body = "num_var=1&mode=GET&url="+"/movies/get/id"+"&var1="+"id"+"&price1="+movie_id;
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      //console.log(this.responseText);
      var data = JSON.parse(this.responseText);
      var entity = '{"id": "'+movie_id+'","type": "Movie",'+
        '"title": { "type": "String", "value": "'+data.title+'" },'+
        '"start_date": { "type": "Date", "value": "'+data.start_date+'" },'+
        '"end_date": { "type": "Date", "value": "'+data.end_date+'" },'+
        '"category": { "type": "String","value": "'+data.category+'"}'+
        '}';
      addEntity(entity);
       entity = '{'+
        '"description": "favorite","subject": {"entities": [{"id": "'+movie_id+'","type": "Movie"}],'+
        '"condition": {"attrs": ["title","start_date","end_date","category"]}},'+
        '"notification": {"http": {"url": "http://172.18.1.4/request-send/orion_recive.php"},"attrs": ["title","start_date","end_date","category"]},'+
        '"expires": "2040-01-01T14:00:00.00Z","throttling": 5'+
        '}'
      addSubscription(entity);
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}

function addNotification(movie_id){
  var req_body = "num_var=1&mode=GET&url="+"/v2/entities/"+movie_id+"&var1=id&price1=id";
  var xhr = new XMLHttpRequest();
  xhr.open('POST',"request-send/orion_request.php",true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function(){
    if(this.status== 200){
      var data = JSON.parse(this.responseText);
      if(data.error!=null){ // if does not exist = notFound so it is not null
        createSubscription(movie_id);
      }
    }
  }
  xhr.onerror = function(){
    console.log('Request error...');
  }
  xhr.send(req_body);
}
</script>
</html>
