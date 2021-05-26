<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Cinema Application</title>
  <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
     <div class="wrapper">
         <header class="header"></header>
        <div class="login-box-outer">
             <div class="login-box-inner">
                  <h1>Sign in</h1>
                       <div class="input-wrap">
                         <button id="login-btn" type="submit" name="loginButton">Login with your Fiware Account</button>
                       </div>
                       <div class="sign-up">
                         <p>New cinema user?<a href="http://localhost:3005/sign_up/">Sign up now</a></p>
                       </div>
             </div>
        </div>
   </div>
</body>
<script>
const logBtn = document.getElementById('login-btn');
function login(){
  window.location.href = "http://localhost:3005/oauth2/authorize?response_type=token&client_id=5be8948f-349d-4e9b-a407-910500a9839c&state=xyz&redirect_uri=http://localhost:2000/welcome.php";
  /*var data = JSON.stringify({"name":"admin@test.com","password":"1234"});
  var xhr = new XMLHttpRequest();
  xhr.withCredentials = true;
  xhr.addEventListener("readystatechange", function() {
    if(this.readyState === 4) {
      console.log(JSON.stringify(this.responseText));
    }
  });
  xhr.open("POST", "http://localhost:3000/v1/auth/tokens",true);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.setRequestHeader("Access-Control-Allow-Origin","http://localhost");
  xhr.setRequestHeader("Access-Control-Allow-Methods","POST, GET, OPTIONS");
  xhr.setRequestHeader("Access-Control-Allow-Headers","*");
  xhr.setRequestHeader("Access-Control-Max-Age","1728000");
  xhr.send(data);*/
}
logBtn.addEventListener('click',login);
</script>
</html>
