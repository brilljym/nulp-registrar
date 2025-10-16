@echo off
echo ===============================================
echo NU Regis Local Print Service
echo ===============================================
echo.
echo This service will monitor for print jobs and
echo print them to your local thermal printer.
echo.
echo Press Ctrl+C to stop the service
echo.

:start
php simple_print_service.php
if errorlevel 1 (
    echo.
    echo Service encountered an error. Restarting in 10 seconds...
    timeout /t 10 /nobreak > nul
    goto start
)

echo.
echo Service stopped.
pause