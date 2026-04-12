# Face Recognition Models Downloader
# This script downloads the required face-api.js models

$modelsDir = ".\storage\app\public\models\"

# Create directory if it doesn't exist
if (!(Test-Path $modelsDir)) {
    New-Item -Type Directory -Path $modelsDir | Out-Null
}

Write-Host "Downloading face-api.js models..." -ForegroundColor Green
Write-Host "This will take 2-3 minutes depending on your internet speed`n"

# Model URLs from GitHub
$models = @(
    @{
        name = "ssd_mobilenetv1_model.bin"
        url = "https://raw.githubusercontent.com/vladmandic/face-api/master/model/ssd_mobilenetv1_model.bin"
    },
    @{
        name = "ssd_mobilenetv1_model-weights_manifest.json"
        url = "https://raw.githubusercontent.com/vladmandic/face-api/master/model/ssd_mobilenetv1_model-weights_manifest.json"
    },
    @{
        name = "face_recognition_model.bin"
        url = "https://raw.githubusercontent.com/vladmandic/face-api/master/model/face_recognition_model.bin"
    },
    @{
        name = "face_recognition_model-weights_manifest.json"
        url = "https://raw.githubusercontent.com/vladmandic/face-api/master/model/face_recognition_model-weights_manifest.json"
    },
    @{
        name = "face_landmark_68_model.bin"
        url = "https://raw.githubusercontent.com/vladmandic/face-api/master/model/face_landmark_68_model.bin"
    },
    @{
        name = "face_landmark_68_model-weights_manifest.json"
        url = "https://raw.githubusercontent.com/vladmandic/face-api/master/model/face_landmark_68_model-weights_manifest.json"
    }
)

# Download each model
foreach ($model in $models) {
    $outputPath = $modelsDir + $model.name
    
    if (Test-Path $outputPath) {
        Write-Host "[OK] Already exists: $($model.name)" -ForegroundColor Green
    } else {
        Write-Host "[DOWNLOAD] Fetching: $($model.name)..." -ForegroundColor Yellow
        try {
            Invoke-WebRequest -Uri $model.url -OutFile $outputPath -ErrorAction Stop
            Write-Host "[OK] Downloaded: $($model.name)" -ForegroundColor Green
        } catch {
            Write-Host "[ERROR] Failed to download $($model.name)" -ForegroundColor Red
            Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
        }
    }
}

Write-Host "`nDownload complete!" -ForegroundColor Green
Write-Host "`nFiles should now be in: $modelsDir`n"

# List downloaded files
Write-Host "Files in models directory:" -ForegroundColor Cyan
Get-ChildItem -Path $modelsDir | ForEach-Object { Write-Host "  [OK] $($_.Name)" }
