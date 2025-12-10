# Script de test PowerShell pour les exercices du php-framework
# Utilisation (depuis le workspace PowerShell) :
#   pwsh ./app/tests/api_tests.ps1

function Post-Contact {
    param($email, $subject, $message)
    $body = @{ email = $email; subject = $subject; message = $message } | ConvertTo-Json
    Invoke-RestMethod -Uri 'http://127.0.0.1:8080/contact' -Method POST -Body $body -ContentType 'application/json'
}

function Get-Contacts {
    Invoke-RestMethod -Uri 'http://127.0.0.1:8080/contact' -Method GET
}

function Get-Contact-ByFile {
    param($filename)
    $enc = [System.Uri]::EscapeDataString($filename)
    Invoke-RestMethod -Uri ("http://127.0.0.1:8080/contact?filename=$enc") -Method GET
}

function Patch-Contact-Override {
    param($filename, $payload)
    $enc = [System.Uri]::EscapeDataString($filename)
    Invoke-RestMethod -Uri ("http://127.0.0.1:8080/contact?filename=$enc&_method=PATCH") -Method POST -Body ($payload | ConvertTo-Json) -ContentType 'application/json'
}

function Delete-Contact-Override {
    param($filename)
    $enc = [System.Uri]::EscapeDataString($filename)
    Invoke-RestMethod -Uri ("http://127.0.0.1:8080/contact?filename=$enc&_method=DELETE") -Method POST
}

# Example run
Write-Output 'POST creating contact...'
$created = Post-Contact -email 'leia@alderaan.com' -subject 'Help me Obi Wan' -message 'You are my only hope'
Write-Output ($created | ConvertTo-Json)

# Use the returned file name (formatted) if necessary
$file = $created.file
Write-Output ('Returned filename: ' + $file)

Write-Output 'GET list...'
Get-Contacts | ConvertTo-Json | Write-Output

Write-Output 'GET by file (query fallback)...'
try { Get-Contact-ByFile -filename $file | ConvertTo-Json | Write-Output } catch { Write-Output 'GET by file failed (try using the actual stored timestamp filename)'; Write-Output $_ }

Write-Output 'PATCH (override) example...'
try { Patch-Contact-Override -filename $file -payload @{ subject = 'Updated from test script' } | ConvertTo-Json | Write-Output } catch { Write-Output 'PATCH failed'; Write-Output $_ }

Write-Output 'DELETE (override) example...'
try { Delete-Contact-Override -filename $file; Write-Output 'DELETE request sent' } catch { Write-Output 'DELETE failed'; Write-Output $_ }
