Start-Job -ScriptBlock {
    Start-Sleep -Seconds 2
    Start-Process msedge "http://127.0.0.1:8000"
} | Out-Null

php artisan serve --host=127.0.0.1 --port=8000
