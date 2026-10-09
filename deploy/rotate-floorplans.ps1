param(
  [Parameter(Mandatory = $true)][string]$InputDirectory,
  [Parameter(Mandatory = $true)][string]$OutputDirectory,
  [string]$PythonPath
)

$ErrorActionPreference = 'Stop'
if (-not $PythonPath) {
  $casePythonCommand = Get-Command python -ErrorAction SilentlyContinue
  if ($casePythonCommand) {
    $PythonPath = $casePythonCommand.Source
  } else {
    $PythonPath = Join-Path $env:USERPROFILE '.cache\codex-runtimes\codex-primary-runtime\dependencies\python\python.exe'
  }
}
if (-not (Test-Path -LiteralPath $PythonPath -PathType Leaf)) {
  throw 'Informe -PythonPath com um Python que tenha Pillow instalado.'
}
& $PythonPath (Join-Path $PSScriptRoot 'rotate-floorplans.py') --input $InputDirectory --output $OutputDirectory
if ($LASTEXITCODE -ne 0) { throw 'A conversao nao foi concluida. Confira a mensagem acima.' }
