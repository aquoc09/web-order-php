<?php
require '../vendor/autoload.php';
include '../application_config.php';

$client = new Google\Client;
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri("http://localhost/web_order_php/auth/google_callback.php");

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();

header("Location: $url");
exit;
?>