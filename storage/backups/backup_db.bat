@echo off
REM =====================================================
REM  Mian Traders - Hourly Database Backup Script
REM  Runs via Windows Task Scheduler every 1 hour
REM  Keeps last 7 days of backups (168 hourly backups)
REM =====================================================

set MYSQLDUMP=C:\xampp\mysql\bin\mysqldump.exe
set DB_HOST=127.0.0.1
set DB_PORT=3306
set DB_NAME=mian_traders
set DB_USER=root
set DB_PASS=
set BACKUP_DIR=C:\xampp\htdocs\mian-traders\storage\backups
set DAYS_TO_KEEP=7

REM Create backup directory if it doesn't exist
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

REM Generate reliable timestamp using wmic
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /format:list') do set DT=%%I
set TIMESTAMP=%DT:~0,4%-%DT:~4,2%-%DT:~6,2%_%DT:~8,2%-%DT:~10,2%

set BACKUP_FILE=%BACKUP_DIR%\mian_traders_%TIMESTAMP%.sql

REM Run mysqldump
echo [%date% %time%] Starting backup to %BACKUP_FILE%
"%MYSQLDUMP%" -h %DB_HOST% -P %DB_PORT% -u %DB_USER% --single-transaction --routines --triggers --quick "%DB_NAME%" > "%BACKUP_FILE%" 2>&1

if %ERRORLEVEL% EQU 0 (
    echo [%date% %time%] Backup successful: %BACKUP_FILE%
    echo %date% %time% - SUCCESS - %BACKUP_FILE% >> "%BACKUP_DIR%\backup_log.txt"
) else (
    echo [%date% %time%] Backup FAILED!
    echo %date% %time% - FAILED >> "%BACKUP_DIR%\backup_log.txt"
)

REM Clean up backups older than 7 days
forfiles /p "%BACKUP_DIR%" /s /m mian_traders_*.sql /d -%DAYS_TO_KEEP% /c "cmd /c del @path" 2>nul

echo [%date% %time%] Done.
