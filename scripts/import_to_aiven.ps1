<#
.SYNOPSIS
    Imports the local database dump (db/bookstore.sql) to a remote Aiven MySQL server.
    
.DESCRIPTION
    This script connects to an Aiven MySQL instance using SSL and imports the local SQL file.
    It uses the `source` command to ensure correct parsing of delimiters (triggers/procedures).
    
.NOTES
    Requires: Aiven Host, Port, Username, Password.
    Uses: db/ca.pem for SSL verification.
#>

$ErrorActionPreference = "Stop"

# --- Configuration ---
# You can hardcode these if you want, or leave them to be prompted.
$AivenHost = "mysql-251a0588-nguyenanhkhoa26092002-3f50.h.aivencloud.com" 
$AivenPort = "10675"
$AivenUser = "avnadmin"
$AivenDb   = "bookstore" # The target database name on Aiven

# --- Path Resolution ---
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$ProjectDir = Split-Path -Parent $ScriptDir
$CaFile = Join-Path $ProjectDir "db\ca.pem"
$MysqlExe = "D:\Downloads\tools\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe"
$MysqlDumpExe = "D:\Downloads\tools\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe"

# --- Prompts ---
if ([string]::IsNullOrWhiteSpace($AivenHost)) {
    $AivenHost = Read-Host "Enter Aiven Hostname (e.g. mysql-project.aivencloud.com)"
}
if ([string]::IsNullOrWhiteSpace($AivenHost)) {
    Write-Error "Hostname is required."
    exit 1
}

if ([string]::IsNullOrWhiteSpace($AivenPort)) {
    $AivenPort = Read-Host "Enter Aiven Port (default 12345)"
    if ([string]::IsNullOrWhiteSpace($AivenPort)) { $AivenPort = "12345" }
}

if ([string]::IsNullOrWhiteSpace($AivenUser)) {
    $AivenUser = Read-Host "Enter Aiven Username (default avnadmin)"
    if ([string]::IsNullOrWhiteSpace($AivenUser)) { $AivenUser = "avnadmin" }
}

$AivenPass = Read-Host "Enter Aiven Password" -AsSecureString
$PlainPass = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto([System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($AivenPass))

if ([string]::IsNullOrWhiteSpace($AivenDb)) {
    $AivenDb = Read-Host "Enter Target Database Name (default bookstore)"
    if ([string]::IsNullOrWhiteSpace($AivenDb)) { $AivenDb = "bookstore" }
}

# --- Export Local Database ---
$DumpFile = Join-Path $ScriptDir "export_temp.sql"
$DumpFileNormalized = $DumpFile -replace '\\', '/'

Write-Host "`nExporting local database 'bookstore'..." -ForegroundColor Cyan

$DumpArgs = @(
    "-u", "root",
    "--default-character-set=utf8mb4",
    "--routines",
    "--triggers",
    "--events",
    "--single-transaction", # Good for consistency
    "--result-file=$DumpFile",
    "bookstore"
)

try {
    & $MysqlDumpExe @DumpArgs
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Export successful: $DumpFile" -ForegroundColor Green
        
        # --- Pre-process Dump (Remove DEFINER) ---
        # Aiven (and other managed MySQL) doesn't allow SUPER privileges, so we can't create objects owned by root@localhost.
        # We strip the DEFINER clause so objects are created by the current user (avnadmin).
        Write-Host "Removing DEFINER clauses to ensure compatibility..." -ForegroundColor Cyan
        (Get-Content $DumpFile) -replace 'DEFINER=`[^`]+`@`[^`]+`', '' -replace 'DEFINER=\w+@\w+', '' | Set-Content $DumpFile
        
    } else {
        Write-Error "Export failed with exit code $LASTEXITCODE"
        exit 1
    }
} catch {
    Write-Error "Failed to run mysqldump: $_"
    exit 1
}

# --- Execution ---
Write-Host "`nConnecting to $AivenHost and importing..." -ForegroundColor Cyan

$ArgsList = @(
    "-h", "$AivenHost",
    "-P", "$AivenPort",
    "-u", "$AivenUser",
    "--password=$PlainPass",
    "--ssl-ca=$CaFile",
    "--ssl-mode=VERIFY_CA",
    "--default-character-set=utf8mb4",
    "-D", "$AivenDb",
    "-e", "source $DumpFileNormalized"
)

try {
    & $MysqlExe @ArgsList
    if ($LASTEXITCODE -eq 0) {
        Write-Host "`nSuccessfully imported to Aiven!" -ForegroundColor Green
    } else {
        Write-Error "`nImport failed with exit code $LASTEXITCODE"
    }
} catch {
    Write-Error $_
} finally {
    # Cleanup temp file
    if (Test-Path $DumpFile) {
        Remove-Item $DumpFile
        Write-Host "Cleaned up temp file." -ForegroundColor Gray
    }
}
