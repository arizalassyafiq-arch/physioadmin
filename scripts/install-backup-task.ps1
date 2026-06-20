param(
    [string] $TaskName = 'PhysioAdmin Daily Backup',
    [string] $At = '21:00'
)

$ErrorActionPreference = 'Stop'

$projectRoot = Resolve-Path (Join-Path $PSScriptRoot '..')
$backupScript = Join-Path $projectRoot 'scripts\backup-full.ps1'

if (-not (Test-Path -LiteralPath $backupScript)) {
    throw "Backup script was not found at $backupScript"
}

$action = New-ScheduledTaskAction `
    -Execute 'powershell.exe' `
    -Argument "-NoProfile -ExecutionPolicy Bypass -File `"$backupScript`""

$trigger = New-ScheduledTaskTrigger -Daily -At $At
$settings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -StartWhenAvailable

Register-ScheduledTask `
    -TaskName $TaskName `
    -Action $action `
    -Trigger $trigger `
    -Settings $settings `
    -Description 'Backup harian PhysioAdmin: database MySQL dan file medis.' `
    -Force | Out-Null

Write-Host "Scheduled task installed: $TaskName at $At"
