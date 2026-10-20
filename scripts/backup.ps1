param(
    [string]$OutDir = (Join-Path $PSScriptRoot '..\backups'),
    [int]$KeepDays = 30
)

# Backup harian database + file surat. Jadwalkan via Task Scheduler (Windows) atau cron (Linux).
$root   = Split-Path $PSScriptRoot -Parent
$envMap = @{}
Get-Content (Join-Path $root '.env') | ForEach-Object {
    if ($_ -match '^\s*([A-Z_]+)\s*=\s*"?(.*?)"?\s*$') { $envMap[$Matches[1]] = $Matches[2] }
}

$mysqldump = (Get-ChildItem 'C:\laragon\bin\mysql' -Recurse -Filter mysqldump.exe | Select-Object -First 1).FullName
if (-not $mysqldump) { throw 'mysqldump.exe tidak ditemukan' }

New-Item -ItemType Directory -Force -Path $OutDir | Out-Null
$stamp = Get-Date -Format 'yyyyMMdd_HHmmss'
$sql   = Join-Path $OutDir "db_$($envMap['DB_DATABASE'])_$stamp.sql"

# Password lewat file opsi sementara agar tidak tampil di daftar proses
$cnf = Join-Path $env:TEMP "bk_$stamp.cnf"
Set-Content -Path $cnf -Value "[client]`nuser=$($envMap['DB_USERNAME'])`npassword=`"$($envMap['DB_PASSWORD'])`"`nhost=$($envMap['DB_HOST'])`nport=$($envMap['DB_PORT'])"
try {
    & $mysqldump "--defaults-extra-file=$cnf" --single-transaction --routines --triggers --no-tablespaces $envMap['DB_DATABASE'] --result-file=$sql
    if ($LASTEXITCODE -ne 0) { throw "mysqldump gagal (kode $LASTEXITCODE)" }
} finally {
    Remove-Item $cnf -Force -ErrorAction SilentlyContinue
}

$files = Join-Path $root 'storage\app'
if (Test-Path $files) {
    Compress-Archive -Path $files -DestinationPath (Join-Path $OutDir "files_$stamp.zip") -Force
}

Get-ChildItem $OutDir -File | Where-Object LastWriteTime -lt (Get-Date).AddDays(-$KeepDays) | Remove-Item -Force
Write-Output "Backup selesai: $sql"
