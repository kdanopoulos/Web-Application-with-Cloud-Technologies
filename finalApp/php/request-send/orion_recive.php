<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
$json = file_get_contents('php://input');


// Converts it into a PHP object
$data = json_decode($json);

$json = '{"foo-bar": 12345}';
$obj = json_decode($json);
print $obj->{'foo-bar'}; // 12345

$curl = curl_init();
$target = 'http://rest-proxy:1027/notifications/add';
curl_setopt_array($curl, array(
  CURLOPT_URL => $target,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "movie_id":"'.$data->{'data'}[0]->{'id'}.'",
    "title":"'.$data->{'data'}[0]->{'title'}->{'value'}.'",
    "start_date":"'.$data->{'data'}[0]->{'start_date'}->{'value'}.'",
    "end_date":"'.$data->{'data'}[0]->{'end_date'}->{'value'}.'",
    "category":"'.$data->{'data'}[0]->{'category'}->{'value'}.'"
}',
  CURLOPT_HTTPHEADER => array(
    'X-Auth-Token: 1234',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
