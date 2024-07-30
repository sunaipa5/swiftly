<?php
$cpr = "AES-256-CBC";
$ops = 0;
$iv_len = openssl_cipher_iv_length($cpr);

//Encryption key

$enckey =  sha1(md5("key"));
$enc_iv = openssl_random_pseudo_bytes($iv_len);

//Decryption key
$deckey = $enckey ;
$dec_iv = $enc_iv ;

?>
