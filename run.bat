@echo off
setlocal EnableExtensions
title JulyCMS Setup

set "ROOT_DIR=%~dp0"
set "SYSTEM_DIR=%ROOT_DIR%system"
set "COMPOSER_CMD=%ROOT_DIR%..\..\bin\php-all\composer81.bat"
set "LOG_FILE=%ROOT_DIR%run-error.log"
set "FAILED_STEP=Initialize setup"
set "EXIT_CODE=1"

> "%LOG_FILE%" (
    echo [%date% %time%] Setup started
    echo Project directory: "%ROOT_DIR%"
    echo Composer command: "%COMPOSER_CMD%"
)

echo [1/3] Checking the project directory...
cd /d "%SYSTEM_DIR%" 2>> "%LOG_FILE%"
set "EXIT_CODE=%ERRORLEVEL%"
if not "%EXIT_CODE%"=="0" (
    set "FAILED_STEP=Open the system directory"
    goto :failed
)

echo [2/3] Preparing the environment file...
if exist ".env" goto :environment_ready
if not exist ".env.production" (
    set "FAILED_STEP=Prepare the environment file; .env and .env.production are missing"
    set "EXIT_CODE=2"
    goto :failed
)

ren ".env.production" ".env" 2>> "%LOG_FILE%"
set "EXIT_CODE=%ERRORLEVEL%"
if not "%EXIT_CODE%"=="0" (
    set "FAILED_STEP=Rename .env.production to .env"
    goto :failed
)

:environment_ready
echo [3/3] Installing Composer dependencies. Please wait...
if not exist "%COMPOSER_CMD%" (
    set "FAILED_STEP=Find the PHP 8.1 Composer command"
    set "EXIT_CODE=2"
    goto :failed
)

call "%COMPOSER_CMD%" install --no-interaction --prefer-dist --optimize-autoloader
set "EXIT_CODE=%ERRORLEVEL%"
if not "%EXIT_CODE%"=="0" (
    set "FAILED_STEP=Install Composer dependencies"
    goto :failed
)

> "%ROOT_DIR%time.txt" echo %date% %time%
set "EXIT_CODE=%ERRORLEVEL%"
if not "%EXIT_CODE%"=="0" (
    set "FAILED_STEP=Write the completion time"
    goto :failed
)

del /q "%LOG_FILE%" >nul 2>&1
echo.
echo Setup completed successfully.
endlocal
(goto) 2>nul & del /q "%~f0"

:failed
>> "%LOG_FILE%" (
    echo.
    echo [%date% %time%] Setup failed
    echo Failed step: %FAILED_STEP%
    echo Exit code: %EXIT_CODE%
)

echo.
echo Setup failed.
echo Failed step: %FAILED_STEP%
echo Exit code: %EXIT_CODE%
echo Error log: %LOG_FILE%
echo.
type "%LOG_FILE%"
echo.
pause
exit /b %EXIT_CODE%
