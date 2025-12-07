<?php
require __DIR__ . '/../vendor/autoload.php';

$client = new Google\Client();
$client->setClientId(getenv('GOOGLE_CLIENT_ID_MAIL'));
$client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET_MAIL'));
$client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI_MAIL'));
$client->addScope(Google\Service\Gmail::GMAIL_SEND);
$client->setAccessType('offline');
$client->setPrompt('consent');

$authUrl = $client->createAuthUrl();
header("Location: $authUrl");
exit;
?>
