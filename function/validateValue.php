<?php

// random chuỗi
function randomString($n){
    if($n >= 6){
        $string = 'abcdefghijkmlnopqrstuvxyz0123456789';
        return substr(str_shuffle($string),0,$n);
    }
    return "";
}


//hàm lấy về index
function postIndex($index, $value){
    if(isset($_POST[$index])){
        return $_POST[$index];
    }
    return $value;
}
function getIndex($index, $value){
    if(isset($_GET[$index])){
        return $_GET[$index];
    }
    return $value;
}
function requestIndex($index, $value){
    if(isset($_REQUEST[$index])){
        return $_REQUEST[$index];
    }
    return $value;
}

//hàm kiểm tra
$result = ['status'=>false, 'log'=>""];

function checkUsername($username){
    global $result;
    $patternUsername = '/^[a-zA-Z0-9]{4,}$/';
    if(!preg_match($patternUsername, $username)){
        $result['status'] = false;
        $result['log'] = "Username không hợp lệ! (Dài hơn 4 kí tự, chỉ chứa chữ cái và số)!";
    }else{
        $result['status'] = true;
    }
    return $result;
}

function checkPassword($password){
    global $result;
    if(strlen($password)<6){
        $result['status'] = false;
        $result['log'] = "Password không hợp lệ (Dài hơn 6 kí tự)!";
    }else{
        $result['status'] = true;
    }
    return $result;
}

function checkConfirmPassword($password, $confirmPassword){
    global $result;
    if($password!=$confirmPassword){
        $result['status'] = false;
        $result['log'] = "Password không trùng khớp!";
    }else{
        $result['status'] = true;
    }
    return $result;
}

function checkName($name){
    global $result;
    if(str_word_count($name)<2){
        $result['status'] = false;
        $result['log'] = "Tên không hợp lệ, phải dài hơn 2 từ!";
    }else{
        $result['status'] = true;
    }
    return $result;
}

function checkPhone($phone){
    global $result;
    $patternPhone = '/^[0|+84|084]+[0-9]{9}$/';
    if(!preg_match($patternPhone, $phone)){
        $result['status'] = false;
        $result['log'] = "SĐT không hợp lệ!";
    }else{
        $result['status'] = true;
    }
    return $result;
}

function checkEmail($email){
    global $result;
    $patternEmail = '/^[a-zA-Z0-9.]+@[a-zA-Z0-9.]+\.[a-zA-Z.]{2,5}$/';
    if(!preg_match($patternEmail, $email)){
        $result['status'] = false;
        $result['log'] = "Email không hợp lệ! (email@example.com)!";
    }else{
        $result['status'] = true;
    }
    return $result;
}

function checkImage($indexImg){
    if(!isset($_FILES[$indexImg])){
        $result['status'] = false;
        $result['log']="Không có file ảnh!";
        return $result;
    }else{
        $file = $_FILES[$indexImg];
        $maxSize = 2*1024*1024;
        if($file['size'] > $maxSize){
            $result['status'] = false;
            $result['log'] = "Kích thước không không hợp lệ (Phải <2MB)";
            return $result;
        }
        $allowedExt = ['jpg','jpeg','png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if(!in_array($ext,$allowedExt)){
            $result['status'] = false;
            $result['log'] = "Phải không đúng định dạng (Chấp nhận: png, jpg, jpeg)";
            return $result;
        }
        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
        if(!is_dir($uploadDir)){
            if(!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)){
                $result['status'] = false;
                $result['log'] = "Không thể tạo folder uploads";
                return $result;
            }
        }
        $newName = time() . '_' . randomString(6).'.'.$ext;
        $savePath = $uploadDir . DIRECTORY_SEPARATOR .$newName;
        if(!move_uploaded_file($file['tmp_name'], $savePath)){
            $result['status'] = false;
            $result['log'] = "Không thể lưu ảnh";
            return $result;
        }else{
            $result['status'] = true;
            $result['log'] = "Tải thành công";
            $result['newName'] = $newName;
            $result['oldName'] = $file['name'];
            $result['ext'] = $ext;
            return $result;
        }
    }
}

function checkLogin($username, $password){
    $log = ['flag'=> true, 'errorUsername'=>"", 'errorPassword'=>""];
    $checkUsername = checkUsername($username);
    if(!$checkUsername['status']){
        $log['flag'] = false;
        $log['errorUsername'] = $checkUsername['log'];
    }
    $checkPassword = checkPassword($password);
    if(!$checkPassword['status']){
        $log['flag'] = false;
        $log['errorPassword'] = $checkPassword['log'];
    }
    if(!$log['flag']){
        $errorPassword = $log['errorPassword'];
        $errorUsername = $log['errorUsername'];
        header("Location: ../login-form.php?errorPassword=$errorPassword&errorUsername=$errorUsername");
        exit;
    }
}

function checkRegister($username, $password, $confirmPassword, $fullName, $phone, $email){
    $flag = true;
    $error = [];

    $checkUsername = checkUsername($username);
    if(!$checkUsername['status']){
        $flag = false;
        $error[] = "errorUsername=".$checkUsername['log'];
    }
    $checkPassword = checkPassword($password);
    if(!$checkPassword['status']){
        $flag = false;
        $error[] = "errorPassword=".$checkPassword['log'];
    }
    $checkConfirmPassword = checkConfirmPassword($password, $confirmPassword);
    if(!$checkConfirmPassword['status']){
        $flag = false;
        $error[] = "errorConfirmPassword=".$checkConfirmPassword['log'];
    }
    $checkName = checkName($fullName);
    if(!$checkName['status']){
        $flag = false;
        $error[] = "errorName=".$checkName['log'];
    }
    $checkPhone = checkPhone($phone);
    if(!$checkPhone['status']){
        $flag = false;
        $error[] = "errorPhone=".$checkPhone['log'];
    }
    $checkEmail = checkEmail($email);
    if(!$checkEmail['status']){
        $flag = false;
        $error[] = "errorEmail=".$checkEmail['log'];
    }

    if(!$flag){
        $errorMsg = implode('&',$error);
        header("Location: ../register-form.php?$errorMsg");
        exit;
    }
}

?>