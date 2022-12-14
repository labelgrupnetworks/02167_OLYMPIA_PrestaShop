<?php 

# Definimos las variables que vamos a emplear
$idInstallation = 9009;
$appKey = '4BEFBB4C8DFF466C92BB610F18034D95';
$secrectKey = '0642D05C-04BDEA1E';
$method = 'POST';

# Ponemos la fecha en formato UTC 
$dateTime = new \DateTime('now');
$timestamp = $dateTime->format('U');

# Generamos en  nonce
$nonce = uniqid();

# Definimos la url encodeada y puesta a minúsculas
$url = 'https://apibasebeta.provis.es/api/login';
$uri = strtolower(urlencode($url));

# Establecemos los parámetros del body
$parameters = array (
    'idInstallation' => $idInstallation,
    'NifNickEmail' => 'NIF_del_usuario',
    'password' => 'password_del_usuario'
);

# Sacamos el hash en MD5 de los parámetros del body y lo encodeamos en base 64
$md5 = base64_encode(md5(json_encode($parameters), true));

# Montamos la cadena $appKey.$method.$uri.$timestamp.$nonce.$md5
# Sacamos el HMAC256 de dicha cadena y encodeamos en base 64 de nuevo
$encode = $appKey.$method.$uri.$timestamp.$nonce.$md5;
$signature = base64_encode(hash_hmac('sha256', $encode, $secrectKey, true));

print_r('uri: '.$uri.'<br>');
print_r('timestamp: '.$timestamp.'<br>');
print_r('nonce: '.$nonce.'<br>');
print_r('jsonencode: '.json_encode($parameters).'<br>');
print_r('md5: '.$md5.'<br>');
print_r('encode: '.$encode.'<br>');
print_r('signature: '.$signature.'<br>');
print_r('Authorization: hmac-256 '.$idInstallation.':'.$appKey.':'.$signature.':'.$nonce.':'.$timestamp.'<br>');

#Realizamos la llamada a la API
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: hmac-256 '.$idInstallation.':'.$appKey.':'.$signature.':'.$nonce.':'.$timestamp,
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));


$response = str_replace('"','',curl_exec($ch));
curl_close($ch);

# Mostramos la respuesta de la API
print_r("token : ".$response);







/*EJEMPLO DE LLAMADAS CON BEARER (CON EL USUARIO LOGUEADO) *******************************************************************/
#decodificamos el token
//print_r(json_decode(base64_decode(str_replace("_", "/", str_replace("-","+",explode(".", $token)[1])))));
print_r('<br><br>new decode<br>');
print_r(base64_decode($response));

//obtenemos que el ID Persona es 273, el ID instalación sabemos que es 9999
$idInstallation2 = 9999;
$idPersona2 = 273;


/*EJEMPLO BEARER***********OPCION 1***************/
$ch2 = curl_init();

curl_setopt($ch2, CURLOPT_URL, "https://apibase.provis.es/api/person/personaldata/?id=273&idinstallation=9999");
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch2, CURLOPT_HEADER, FALSE);
curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer '.$response,'cache-control: no-cache'
));
$response2 = curl_exec($ch2);
curl_close($ch2);

print_r("<br><br>nuevos datos : ".$response2);
print_r('<br><br>FIN<br><br>');



/*EJEMPLO BEARER**********OPCION 2***********************/
$ch3 = curl_init();
curl_setopt_array($ch3, array(
  CURLOPT_URL => "https://apibase.provis.es/api/person/personaldata/?id=273&idinstallation=9999",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json', 'Authorization: Bearer '.$response,'cache-control: no-cache'
  ),
));
$response3 = curl_exec($ch3);
$err = curl_error($ch3);
curl_close($ch3);

print_r("<br><br>nuevos datos2 : ".$response3);
print_r('<br><br>FIN2<br><br>');

$err = curl_error($ch3);
if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response3;
}

?>