param(
    [ValidateRange(1, 65535)]
    [int]$Port = 4741
)

# Always use the application router: on Windows, /projetos otherwise resolves
# to the physical legacy Projetos directory before index.php can handle it.
$projectRoot = Split-Path -Parent $PSScriptRoot
$routerPath = Join-Path $PSScriptRoot 'dev-router.php'
& php -S "127.0.0.1:$Port" -t $projectRoot $routerPath
exit $LASTEXITCODE
