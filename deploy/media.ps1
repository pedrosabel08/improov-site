param(
  [Parameter(Mandatory = $true)][string]$MastersRoot,
  [Parameter(Mandatory = $true)][string]$OutputRoot,
  [string]$ManifestPath = (Join-Path $PSScriptRoot 'media-manifest.json'),
  [switch]$ValidateOnly
)

$ErrorActionPreference = 'Stop'
$manifest = Get-Content -LiteralPath $ManifestPath -Raw | ConvertFrom-Json
$missing = [System.Collections.Generic.List[string]]::new()

foreach ($project in $manifest.projects) {
  foreach ($media in $project.media) {
    $source = Join-Path $MastersRoot $media.source
    if (-not (Test-Path -LiteralPath $source -PathType Leaf)) { $missing.Add($source); continue }
    $destination = Join-Path $OutputRoot (Join-Path $project.slug ("v{0}" -f $project.mediaVersion))
    if (-not $ValidateOnly) {
      New-Item -ItemType Directory -Path $destination -Force | Out-Null
      foreach ($width in $manifest.widths) {
        foreach ($format in $manifest.formats) {
          $target = Join-Path $destination ("{0}-{1}.{2}" -f $media.name, $width, $format)
          $quality = 76
          if ($format -eq 'jpg') { $quality = 82 }
          & magick $source -auto-orient -strip -resize ("{0}x>" -f $width) -quality $quality $target
          if ($LASTEXITCODE -ne 0) { throw "Falha ao gerar $target" }
        }
      }
    }
  }
}

if ($missing.Count -gt 0) { throw "Masters ausentes:`n$($missing -join "`n")" }
if ($ValidateOnly) { Write-Output 'Manifesto válido.' } else { Write-Output 'Pacote de mídia gerado.' }
