<?php
require __DIR__ . '/../vendor/autoload.php';

include '../function/generateToken.php';
include '../repository/userRepository.php';
include '../database/conf.php';
include '../application_config.php';

$client = new Google\Client;
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

$client->addScope("email");
$client->addScope("profile");

if(!isset($_GET['code'])){
    header("Location: ../login-form.php?message='Đăng nhập google thất bại'");
    exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$client->setAccessToken($token['access_token']);

$oauth = new Google\Service\Oauth2($client);
$userInfo = $oauth->userinfo->get();

$name = $userInfo->name;
$email = $userInfo->email;
$googleAccountId = $userInfo->id;

$user = findUserByGoogleId($googleAccountId, $conn);

if($user!=null){
    $user = findUserByEmail($email, $conn);
}else{
    // Tìm theo email
    $userByEmail = findUserByEmail($email, $conn);

    if ($userByEmail != null) {
        // TH2: Email tồn tại nhưng chưa có googleAccountId
        updateGoogleId($userByEmail['id'], $googleAccountId, $conn);
        $user = findUserByGoogleId($googleAccountId, $conn); 
    } else {
        // TH3: Email chưa tồn tại → tạo user mới
        $user_tmp = [
            'fullName' => $name,
            'email' => $email,
            'googleAccountId' => $googleAccountId,
            'password' => null,
            'username' => null,
            'active' => 1,
            'role' => 'USER'
        ];
        createUser($user_tmp, $conn);
        $user = findUserByGoogleId($googleAccountId, $conn);
    }
}

$token = generateToken($conn, $user['id']);
setcookie("auth_token", $token, time() + 86400, "/", "", false, true); // httponly = true

header("Location: ../index.php?message=Đăng nhập thành công");
exit;

?>