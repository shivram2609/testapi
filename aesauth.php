<?php
/*
Methods to encryption and decryption
iv    = 8FB1A2080C648F95;
key    = 9B307D9DB5EAA3E360338F9AD4218D7E;

Encryption Method :

td = mcrypt_module_open('rijndael-128', '', 'cbc',iv);
mcrypt_generic_init(td, key, iv);
encrypted = mcrypt_generic(td, str);  //str is the value to be encrypted
mcrypt_generic_deinit(td);
mcrypt_module_close(td);
return bin2hex(encrypted); // value need to be converted from binary to hexadecimal


Decryption Method :

// code will be passed as hexadecimal string

code = hex2bin(code);  // code will be converted into binary format from hexadecimal

td = mcrypt_module_open('rijndael-128', '', 'cbc', iv);

mcrypt_generic_init(td, key, iv);
decrypted = mdecrypt_generic(td, code);

mcrypt_generic_deinit(td);
mcrypt_module_close(td);

return utf8_encode(trim(preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '',decrypted)));

*/ 


class AesAuthorization {
                private $iv             = "8FB1A2080C648F95";
                private $key    = "9B307D9DB5EAA3E360338F9AD4218D7E";


                function encrypt($str) {

                  //$key = $this->hex2bin($key);
                  $iv = $this->iv;

                  $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

                  mcrypt_generic_init($td, $this->key, $iv);
                  $encrypted = mcrypt_generic($td, $str);

                  mcrypt_generic_deinit($td);
                  mcrypt_module_close($td);

                  return bin2hex($encrypted);
                }

                function decrypt_bak($code) {
                  //$key = $this->hex2bin($key);
                  $code = $this->hex2bin($code);
                  $iv = $this->iv;

                  $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

                  mcrypt_generic_init($td, $this->key, $iv);
                  $decrypted = mdecrypt_generic($td, $code);

                  mcrypt_generic_deinit($td);
                  mcrypt_module_close($td);
 
                  return bin2hex($decrypted);
                }

                function decrypt($code) {
                  //$key = $this->hex2bin($key);
                  $code = $this->hex2bin($code);
                  $iv = $this->iv;

                  $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

                  mcrypt_generic_init($td, $this->key, $iv);
                  $decrypted = mdecrypt_generic($td, $code);

                  mcrypt_generic_deinit($td);
                  mcrypt_module_close($td);

                  return utf8_encode(trim(preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '',$decrypted)));
                }

                protected function hex2bin($hexdata) {
                  $bindata = '';

                  for ($i = 0; $i < strlen($hexdata); $i += 2) {
                                $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
                  }

                  return $bindata;
                }

}
?>
