<?php
require __DIR__ . '/../vendor/autoload.php';

function sendMailGmail($to, $subject, $bodyHtml)
{
    $client = new Google\Client();
    $client->setClientId(getenv('GOOGLE_CLIENT_ID_MAIL'));
    $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET_MAIL'));
    $client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI_MAIL'));

    $client->addScope(Google\Service\Gmail::GMAIL_SEND);
    $client->setAccessType('offline');

    // Lấy refresh token từ Railway
    $refreshToken = getenv('GMAIL_REFRESH_TOKEN');

    // Cấp access token mới từ refresh token
    $accessToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
    $client->setAccessToken($accessToken);

    $service = new Google\Service\Gmail($client);

    // Raw email theo RFC 2822
    $rawMessageString =
        "From: PQ Restaurant <your-email@gmail.com>\r\n" .
        "To: <$to>\r\n" .
        "Subject: $subject\r\n" .
        "Content-Type: text/html; charset=UTF-8\r\n\r\n" .
        "$bodyHtml";

    $rawMessage = base64_encode($rawMessageString);
    $rawMessage = str_replace(['+', '/', '='], ['-', '_', ''], $rawMessage);

    $message = new Google\Service\Gmail\Message();
    $message->setRaw($rawMessage);

    return $service->users_messages->send("me", $message);
}
