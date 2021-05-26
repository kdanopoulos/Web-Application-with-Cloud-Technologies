<?php
session_start();
if($_SESSION['token']==null){
  header("Location: http://localhost?unauthorized_access");
  exit();
}elseif ($_SESSION['access']==null) {
  header("Location: http://localhost?unauthorized_access");
  exit();
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
}
if($json['roles'][0]['name']=='Admin' && $_SESSION['access']=='admins'){
  header('Location: http://localhost:3000/idm');
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Cinema Application</title>
  <link rel="stylesheet" type="text/css" href="deniedAccess/deniedAccess.css">
</head>
<body>
  <div class="wrapper">
      <!-- Header -->
      <header class="header">
          <div class="left-menu">Menu
            <div class="dropdown-menu">
              <?php
              echo "<a href='welcome.php?token_type=".$_SESSION['token_type']."&expires_at=".$_SESSION['expires_at']."&token=".$_SESSION['token']."&state=".$_SESSION['state']."'style='color:#fff;text-decoration: none;'>Home</a>";
              ?>
            </div>
          </div>
          <p class="hello-user" style="color:#fff;position:absolute;top:5px;left:75%;">
            <?php
            echo $json['username']." (".$json['roles'][0]['name'].")";
            ?>
          </p>
          <form action="welcome/logout.php" method="post">
            <button type="submit" name="logout">Logout</button>
          </form>
      </header>
      <div class="background">
        <h1>Denied Access</h1>
        <p>This page is accessible only for <?php
        echo $_SESSION['access'];
        unset($_SESSION['access']);
        ?></p>
    </div>
  </div>
</body>
</html>
