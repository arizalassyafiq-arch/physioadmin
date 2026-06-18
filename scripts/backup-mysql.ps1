param(
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

if ([string]::IsNullOrWhiteSpace($MysqlDumpPath)) {
    $command = Get-Command mysqldump -ErrorAction SilentlyContinue
    if ($command) {
        $MysqlDumpPath = $command.Source
    }
}

if ([string]::IsNullOrWhiteSpace($MysqlDumpPath)) {
    $laragonDump = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe'
    if (Test-Path -LiteralPath $laragonDump) {
        $MysqlDumpPath = $laragonDump
    }
}

if (-not (Test-Path -LiteralPath $MysqlDumpPath)) {
    throw 'mysqldump was not found. Pass -MysqlDumpPath or add it to PATH.'
}

$backupDir = Join-Path $projectRoot 'storage\app\backups\mysql'
New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

$timestamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$backupPath = Join-Path $backupDir "$database-$timestamp.sql"

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
        $database | Set-Content -Encoding UTF8 -LiteralPath $backupPath

    if ($LASTEXITCODE -ne 0) {
        throw "mysqldump failed with exit code $LASTEXITCODE"
    }
} finally {
    Remove-Item Env:\MYSQL_PWD -ErrorAction SilentlyContinue
}

Write-Host "Backup created: $backupPath"
