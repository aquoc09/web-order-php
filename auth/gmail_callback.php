<?php
require __DIR__ . '/../vendor/autoload.php';

$client = new Google\Client();
$client->setClientId(getenv('GOOGLE_CLIENT_ID_MAIL'));
$client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET_MAIL'));
$client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI_MAIL'));

if (!isset($_GET['code'])) {
    exit("No code provided");
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['refresh_token'])) {
    // Lưu refresh token vào Railway Variables (copy output để dán vào Railway)
    echo "REFRESH TOKEN:<br>";
    echo $token['refresh_token'];
} else {
    echo "Google không trả refresh_token — có thể do bạn đã approve app trước đó.";
}

?>