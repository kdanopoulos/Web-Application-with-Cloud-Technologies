<?php
header("Content-Type: application/json; charset=UTF-8");
$num_var = $_POST['num_var'];
$mode = $_POST['mode'];
$curl = curl_init();
$target = 'http://orion-proxy:1024';
if($num_var==1){
  if($mode=='GET'){
    curl_setopt_array($curl, array(
      CURLOPT_URL => $target.$_POST['url'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $mode,
      CURLOPT_HTTPHEADER => array(
        //'Content-Type: application/json',
        'X-Auth-Token: 1234',
        $_POST['var1'].": ".$_POST['price1']
        ),
      ));
  }else{
    curl_setopt_array($curl, array(
      CURLOPT_URL => $target.$_POST['url'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $mode,
      CURLOPT_POSTFIELDS =>'{
        "'.$_POST['var1'].'":"'.$_POST['price1'].'"
        }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'X-Auth-Token: 1234'
        ),
      ));
  }
}elseif($num_var==2){
  if($mode=='GET'){
    curl_setopt_array($curl, array(
      CURLOPT_URL => $target.$_POST['url'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $mode,
      CURLOPT_HTTPHEADER => array(
        //'Content-Type: application/json',
        'X-Auth-Token: 1234',
        $_POST['var1'].": ".$_POST['price1'],
        $_POST['var2'].": ".$_POST['price2']
        ),
      ));
  }else{
    curl_setopt_array($curl, array(
      CURLOPT_URL => $target.$_POST['url'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $mode,
      CURLOPT_POSTFIELDS =>'{
        "'.$_POST['var1'].'":"'.$_POST['price1'].'",
        "'.$_POST['var2'].'":"'.$_POST['price2'].'"
        }',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'X-Auth-Token: 1234'
        ),
      ));
  }
}elseif($num_var==3){
  if($mode=='GET'){
    curl_setopt_array($curl, array(
      CURLOPT_URL => $target.$_POST['url'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $mode,
      CURLOPT_HTTPHEADER => array(
      //  'Content-Type: application/json',
        'X-Auth-Token: 1234',
        $_POST['var1'].": ".$_POST['price1'],
        $_POST['var2'].": ".$_POST['price2'],
        $_POST['var3'].": ".$_POST['price3']
        ),
      ));
  }else{
    curl_setopt_array($curl, array(
      CURLOPT_URL => $target.$_POST['url'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $mode,
      CURLOPT_POSTFIELDS =>'{
        "'.$_POST['var1'].'":"'.$_POST['price1'].'",
        "'.$_POST['var2'].'":"'.$_POST['price2'].'",
        "'.$_POST['var3'].'":"'.$_POST['price3'].'"
        }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'X-Auth-Token: 1234'
        ),
      ));
  }
}elseif($num_var==4){
  if($mode=='GET'){
    curl_setopt_array($curl, array(
      CURLOPT_URL => $target.$_POST['url'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $mode,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'X-Auth-Token: 1234',
        $_POST['var1'].": ".$_POST['price1'],
        $_POST['var2'].": ".$_POST['price2'],
        $_POST['var3'].": ".$_POST['price3'],
        $_POST['var4'].": ".$_POST['price4']
        ),
      ));
  }else{
    curl_setopt_array($curl, array(
      CURLOPT_URL => $target.$_POST['url'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $mode,
      CURLOPT_POSTFIELDS =>'{
        "'.$_POST['var1'].'":"'.$_POST['price1'].'",
        "'.$_POST['var2'].'":"'.$_POST['price2'].'",
        "'.$_POST['var3'].'":"'.$_POST['price3'].'",
        "'.$_POST['var4'].'":"'.$_POST['price4'].'"
        }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'X-Auth-Token: 1234'
        ),
      ));
  }
}elseif($num_var==5){
  if($mode=='GET'){
    curl_setopt_array($curl, array(
      CURLOPT_URL => $target.$_POST['url'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $mode,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'X-Auth-Token: 1234',
        $_POST['var1'].": ".$_POST['price1'],
        $_POST['var2'].": ".$_POST['price2'],
        $_POST['var3'].": ".$_POST['price3'],
        $_POST['var4'].": ".$_POST['price4'],
        $_POST['var5'].": ".$_POST['price5']
        ),
      ));
  }else{
    curl_setopt_array($curl, array(
      CURLOPT_URL => $target.$_POST['url'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $mode,
      CURLOPT_POSTFIELDS =>'{
        "'.$_POST['var1'].'":"'.$_POST['price1'].'",
        "'.$_POST['var2'].'":"'.$_POST['price2'].'",
        "'.$_POST['var3'].'":"'.$_POST['price3'].'",
        "'.$_POST['var4'].'":"'.$_POST['price4'].'",
        "'.$_POST['var5'].'":"'.$_POST['price5'].'"
        }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'X-Auth-Token: 1234'
        ),
      ));
  }
}elseif($num_var==0){
  echo $_POST['body'];
  curl_setopt_array($curl, array(
    CURLOPT_URL => $target.$_POST['url'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>$_POST['body'],
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'X-Auth-Token: 1234'
      ),
    ));
}

$response = curl_exec($curl);
curl_close($curl);
echo $response;
