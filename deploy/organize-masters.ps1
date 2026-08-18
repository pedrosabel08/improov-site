param(
  [Parameter(Mandatory = $true)][string]$SourceRoot,
  [Parameter(Mandatory = $true)][string]$MastersRoot,
  [switch]$Move,
  [switch]$WhatIf
)

$ErrorActionPreference = 'Stop'

$imageExtensions = @('.jpg', '.jpeg', '.png', '.webp', '.avif', '.gif')
$source = (Resolve-Path -LiteralPath $SourceRoot).Path
$destination = [System.IO.Path]::GetFullPath($MastersRoot)

if (-not (Test-Path -LiteralPath $source -PathType Container)) {
  throw "Pasta de origem não encontrada: $source"
}

$folders = @(
  'projetos',
  'escritorio/coworking',
  'escritorio/equipe',
  'escritorio/ambientes',
  'escritorio/detalhes',
  'imagens-gerais/home',
  'imagens-gerais/contato',
  'imagens-gerais/chamadas',
  'imagens-gerais/backgrounds',
  'imagens-gerais/legado',
  'marca/logos',
  'marca/favicon',
  'marca/og-images',
  'videos/projetos',
  'videos/institucionais',
  'videos/posters'
)

foreach ($folder in $folders) {
  $path = Join-Path $destination $folder
  if (-not $WhatIf) { New-Item -ItemType Directory -Path $path -Force | Out-Null }
  Write-Output "Pasta: $path"
}

function IsImage([System.IO.FileInfo]$file) {
  return $imageExtensions -contains $file.Extension.ToLowerInvariant()
}

function PlaceFile([System.IO.FileInfo]$file, [string]$relativeDestination) {
  $targetDirectory = Join-Path $destination $relativeDestination
  $target = Join-Path $targetDirectory $file.Name
  if (-not $WhatIf) {
    New-Item -ItemType Directory -Path $targetDirectory -Force | Out-Null
    if ($Move) {
      Move-Item -LiteralPath $file.FullName -Destination $target -Force
    } else {
      Copy-Item -LiteralPath $file.FullName -Destination $target -Force
    }
  }
  $operation = if ($Move) { 'Movido' } else { 'Copiado' }
  Write-Output "${operation}: $($file.FullName) -> $target"
}

# Project folders already have their own editorial identity. Preserve their names.
$projectSource = Join-Path $source 'projetos'
if (Test-Path -LiteralPath $projectSource -PathType Container) {
  Get-ChildItem -LiteralPath $projectSource -Recurse -File | Where-Object { IsImage $_ } | ForEach-Object {
    $relative = [System.IO.Path]::GetRelativePath($projectSource, $_.DirectoryName)
    $targetRelative = if ($relative -eq '.') { 'projetos' } else { Join-Path 'projetos' $relative }
    PlaceFile $_ $targetRelative
  }
}

# Existing office folder contents are kept under escritorio.
$officeSource = Join-Path $source 'escritorio'
if (Test-Path -LiteralPath $officeSource -PathType Container) {
  Get-ChildItem -LiteralPath $officeSource -Recurse -File | Where-Object { IsImage $_ } | ForEach-Object {
    $relative = [System.IO.Path]::GetRelativePath($officeSource, $_.DirectoryName)
    $targetRelative = if ($relative -eq '.') { 'escritorio' } else { Join-Path 'escritorio' $relative }
    PlaceFile $_ $targetRelative
  }
}

# Root assets are classified conservatively by their current names.
Get-ChildItem -LiteralPath $source -File | Where-Object { IsImage $_ } | ForEach-Object {
  $name = $_.BaseName.ToLowerInvariant()
  if ($name -match 'favicon') {
    PlaceFile $_ 'marca/favicon'
  } elseif ($name -match 'logo') {
    PlaceFile $_ 'marca/logos'
  } elseif ($name -match 'coworking|living|adega|fireplace') {
    PlaceFile $_ 'escritorio/ambientes'
  } else {
    PlaceFile $_ 'imagens-gerais/legado'
  }
}

Write-Output 'Organização concluída. Masters preservados na origem quando -Move não foi informado.'
