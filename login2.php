<?php

include 'init.php';

//initialization
$crypter = Crypter::init();
$privatekey = readFileData("Keys/PrivateKey.prk");

function tokenResponse($data){
    global $crypter, $privatekey;
    $data = toJson($data);
    $datahash = sha256($data);
    $acktoken = array(
        "Data" => profileEncrypt($data, $datahash),
        "Sign" => toBase64($crypter->signByPrivate($privatekey, $data)),
        "Hash" => $datahash
    );
    return toBase64(toJson($acktoken));
}

//token data
$token = fromBase64($_POST['token']);
$tokarr = fromJson($token, true);

//Data section decrypter
$encdata = $tokarr['Data'];
$decdata = trim($crypter->decryptByPrivate($privatekey, fromBase64($encdata)));
$data = fromJson($decdata);

//Hash Validator
$tokhash = $tokarr['Hash'];
$newhash = sha256($encdata);

if (strcmp($tokhash, $newhash) == 0) {
    PlainDie();
}


//Username Validator
$uname = $data["uname"];
if($uname == null || preg_match("([a-zA-Z0-9]+)", $uname) === 0){
    $ackdata = array(
        "Status" => "Failed",
        "MessageString" => "Usuario Invalido",
        "SubscriptionLeft" => "0"
    );
    PlainDie(tokenResponse($ackdata));
}

//Password Validator
$pass = $data["pass"];
if($pass == null || !preg_match("([a-zA-Z0-9]+)", $pass) === 0){
    $ackdata = array(
        "Status" => "Failed",
        "MessageString" => "Senha Invalida",
        "SubscriptionLeft" => "0"
    );
    PlainDie(tokenResponse($ackdata));
}



$query = $con->query("SELECT * FROM `users` WHERE `username` = '".$uname."' AND `password` = '".$pass."'");
if($query->num_rows < 1){
    $ackdata = array(
        "Status" => "Failed",
        "MessageString" => "Usuario ou senha incorretos!",
        "SubscriptionLeft" => "0"
    );
    PlainDie(tokenResponse($ackdata));
}

$res = $query->fetch_assoc();

if($res["StartDate"] == '0000-00-00 00:00:00'){
    $query = $con->query("UPDATE `users` SET `registered` = CURRENT_TIMESTAMP WHERE `username` = '$uname'");
}

if($res["EndDate"] == '0000-00-00 00:00:00'){
    
    $date2 = date("Y/m/d h:i");
    $dias = 30;
    $mod_date = strtotime($date2."+ $dias days");
    $adicionardias = date("Y/m/d h:i",$mod_date);

    $query = $con->query("UPDATE `users` SET `expired` = '$adicionardias' WHERE `username` = '$uname'");
}

$uidup = $data["cs"];

if($res["UID"] == NULL){
    $query = $con->query("UPDATE `users` SET `UID` = '$uidup' WHERE `username` = '".$uname."' AND `password` = '".$pass."'");
}

else if($res["UID"] != $uidup) {
    $ackdata = array(
        "Status" => "Failed",
        "MessageString" => "Seu UID foi alterado!",
        "SubscriptionLeft" => "0"
    );
    PlainDie(tokenResponse($ackdata));
}

$ackdata = array(
    "Status" => "Success",
    "MessageString" => "",
    "SubscriptionLeft" => $res["expired"],
    "Validade" => $res["expired"],
    "Title" => $title,
   "icon" => $icon,
   "isactive" => $isactive,
  "Username" => $res["username"],
    "Vendedor" => $res["reseller"],
    "RegisterDate" => $res["registered"],
    $database = date_create($res["expired"]),
$datadehoje = date_create(),
$resultado = date_diff($database, $datadehoje),
$dias = date_interval_format($resultado, '%a'),
"Dias" => " $dias days Trial"
);

echo tokenResponse($ackdata);
