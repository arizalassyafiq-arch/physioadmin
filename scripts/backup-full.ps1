param(
    [string] $BackupRoot = '',
    [int] $RetentionDays = 30,
    [string] $MysqlDumpPath = ''
)

$ErrorActionPreference = 'Stop'

$projectRoot = Resolve-Path (Join-Path $PSScriptRoot '..')
$envPath = Join-Path $projectRoot '.env'

if (-not (Test-Path -LiteralPath $envPath)) {
    throw ".env file was not found at $envPath"
}

function Read-DotEnv {
    param([string] $Path)

    $values = @{}

    Get-Content -LiteralPath $Path | ForEach-Object {
        $line = $_.Trim()

        if ($line -eq '' -or $line.StartsWith('#') -or -not $line.Contains('=')) {
            return
        }

        $key, $value = $line.Split('=', 2)
        $values[$key.Trim()] = $value.Trim().Trim('"').Trim("'")
    }

    return $values
}

function Resolve-MysqlDump {
    param([string] $ProvidedPath)

    if (-not [string]::IsNullOrWhiteSpace($ProvidedPath) -and (Test-Path -LiteralPath $ProvidedPath)) {
        return $ProvidedPath
    }

    $command = Get-Command mysqldump -ErrorAction SilentlyContinue
    if ($command) {
        return $command.Source
    }

    $laragonDump = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe'
    if (Test-Path -LiteralPath $laragonDump) {
        return $laragonDump
    }

    throw 'mysqldump was not found. Pass -MysqlDumpPath or add it to PATH.'
}

function Remove-OldBackups {
    param(
        [string] $Root,
        [int] $Days
    )

    if ($Days -le 0 -or -not (Test-Path -LiteralPath $Root)) {
        return
    }

    $rootPath = (Resolve-Path -LiteralPath $Root).Path.TrimEnd('\')
    $cutoff = (Get-Date).AddDays(-$Days)

    Get-ChildItem -LiteralPath $rootPath -Directory | Where-Object {
        $_.LastWriteTime -lt $cutoff -and $_.FullName.StartsWith($rootPath, [System.StringComparison]::OrdinalIgnoreCase)
    } | ForEach-Object {
        Remove-Item -LiteralPath $_.FullName -Recurse -Force
    }
}

$envValues = Read-DotEnv -Path $envPath

$connection = ''
if ($envValues.ContainsKey('DB_CONNECTION')) {
    $connection = $envValues['DB_CONNECTION']
}

if ($connection -ne 'mysql') {
    throw 'DB_CONNECTION must be mysql before running this backup script.'
}

$database = $envValues['DB_DATABASE']
$username = $envValues['DB_USERNAME']
$password = $envValues['DB_PASSWORD']
$hostName = '127.0.0.1'
$port = '3306'

if ($envValues.ContainsKey('DB_HOST') -and -not [string]::IsNullOrWhiteSpace($envValues['DB_HOST'])) {
    $hostName = $envValues['DB_HOST']
}

if ($envValues.ContainsKey('DB_PORT') -and -not [string]::IsNullOrWhiteSpace($envValues['DB_PORT'])) {
    $port = $envValues['DB_PORT']
}

if ([string]::IsNullOrWhiteSpace($database) -or [string]::IsNullOrWhiteSpace($username)) {
    throw 'DB_DATABASE and DB_USERNAME must be configured in .env.'
}

if ($envValues.ContainsKey('BACKUP_PATH') -and -not [string]::IsNullOrWhiteSpace($envValues['BACKUP_PATH']) -and [string]::IsNullOrWhiteSpace($BackupRoot)) {
    $BackupRoot = $envValues['BACKUP_PATH']
}

if ($envValues.ContainsKey('BACKUP_RETENTION_DAYS') -and $envValues['BACKUP_RETENTION_DAYS'] -match '^\d+$' -and $RetentionDays -eq 30) {
    $RetentionDays = [int] $envValues['BACKUP_RETENTION_DAYS']
}

if ([string]::IsNullOrWhiteSpace($BackupRoot)) {
    $BackupRoot = if (Test-Path -LiteralPath 'C:\laragon') {
        'C:\laragon\backups\physioadmin'
    } else {
        Join-Path $projectRoot 'storage\app\backups\full'
    }
}

$MysqlDumpPath = Resolve-MysqlDump -ProvidedPath $MysqlDumpPath

New-Item -ItemType Directory -Force -Path $BackupRoot | Out-Null

$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$backupDir = Join-Path $BackupRoot "$database-$timestamp"
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

$databaseBackup = Join-Path $backupDir "$database.sql"
$medicalBackup = Join-Path $backupDir 'medical-files.zip'
$infoPath = Join-Path $backupDir 'backup-info.txt'

$env:MYSQL_PWD = $password
try {
    & $MysqlDumpPath `
        --protocol=TCP `
        --host=$hostName `
        --port=$port `
        --user=$username `
        --single-transaction `
        --no-tablespaces `
        --routines `
        --triggers `
        $database | Set-Content -Encoding UTF8 -LiteralPath $databaseBackup

    if ($LASTEXITCODE -ne 0) {
        throw "mysqldump failed with exit code $LASTEXITCODE"
    }
} finally {
    Remove-Item Env:\MYSQL_PWD -ErrorAction SilentlyContinue
}

$medicalPath = Join-Path $projectRoot 'storage\app\medical'
$medicalStatus = 'missing'

if (Test-Path -LiteralPath $medicalPath) {
    $medicalItems = Get-ChildItem -LiteralPath $medicalPath -Force
    if ($medicalItems.Count -gt 0) {
        Compress-Archive -Path (Join-Path $medicalPath '*') -DestinationPath $medicalBackup -Force
        $medicalStatus = 'included'
    } else {
        $medicalStatus = 'empty'
    }
}

if ($medicalStatus -ne 'included') {
    $markerDir = Join-Path $backupDir '_medical-empty'
    New-Item -ItemType Directory -Force -Path $markerDir | Out-Null
    "Folder storage/app/medical belum ada atau masih kosong saat backup dibuat." |
        Set-Content -Encoding UTF8 -LiteralPath (Join-Path $markerDir 'README.txt')
    Compress-Archive -Path (Join-Path $markerDir '*') -DestinationPath $medicalBackup -Force
    Remove-Item -LiteralPath $markerDir -Recurse -Force
}

@(
    "Backup dibuat: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')",
    "Project: $projectRoot",
    "Database: $database",
    "Database backup: $databaseBackup",
    "File medis: $medicalStatus",
    "File medis backup: $medicalBackup",
    "Retensi hari: $RetentionDays"
) | Set-Content -Encoding UTF8 -LiteralPath $infoPath

Remove-OldBackups -Root $BackupRoot -Days $RetentionDays

Write-Host "Backup lengkap berhasil dibuat: $backupDir"
