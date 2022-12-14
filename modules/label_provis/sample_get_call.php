<?php 

# Definimos las variables que vamos a emplear
$idInstallation = 9009;
$appKey = '4BEFBB4C8DFF466C92BB610F18034D95';
$secrectKey = '0642D05C-04BDEA1E';
$method = 'GET';

# Ponemos la fecha en formato UTC 
$dateTime = new \DateTime('now');
$timestamp = $dateTime->format('U');

# Generamos en  nonce
$nonce = uniqid();

# Definimos la url encodeada y puesta a minúsculas
$url = 'https://apibasebeta.provis.es/api/ecommerce/items?installationID';
$uri = strtolower(urlencode($url));

# Establecemos los parámetros del body
$parameters = '';

# Sacamos el hash en MD5 de los parámetros del body y lo encodeamos en base 64
#$md5 = base64_encode(md5(json_encode($parameters), true));
        if($parameters){
            $md5 = base64_encode(
                md5(
                    json_encode($parameters),
                    true
                )
            );
        }
$md5 = '';

# Montamos la cadena $appKey.$method.$uri.$timestamp.$nonce.$md5
# Sacamos el HMAC256 de dicha cadena y encodeamos en base 64 de nuevo
$encode = $appKey.$method.$uri.$timestamp.$nonce.$md5;
$signature = base64_encode(hash_hmac('sha256', $encode, $secrectKey, true));

print_r('uri: '.$uri.'<br>');
print_r('url: '.$url.'<br>');
print_r('timestamp: '.$timestamp.'<br>');
print_r('nonce: '.$nonce.'<br>');
print_r('jsonencode: '.json_encode($parameters).'<br>');
print_r('md5: '.$md5.'<br>');
print_r('encode: '.$encode.'<br>');
print_r('signature: '.$signature.'<br>');
print_r('Authorization: hmac-256 '.$idInstallation.':'.$appKey.':'.$signature.':'.$nonce.':'.$timestamp.'<br>');

#Realizamos la llamada a la API

$ch3 = curl_init();
curl_setopt_array($ch3, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_POSTFIELDS => '',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json', 
    'Cache-Control: no-cache',
    'Authorization: hmac-256 '.$idInstallation.':'.$appKey.':'.$signature.':'.$nonce.':'.$timestamp
  ),
));
$response3 = curl_exec($ch3);
$err = curl_error($ch3);
curl_close($ch3);


echo "<pre>";
var_dump($response3);
echo "</pre>";

curl_close($ch3);
?>