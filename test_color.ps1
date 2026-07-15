Add-Type -AssemblyName System.Drawing
$imagePath = "c:\xampp\htdocs\cardStudio\admin\uploads\fondo_fb3b919f8887.jpg"
if (Test-Path $imagePath) {
    $bmp = New-Object System.Drawing.Bitmap $imagePath
    $r = 0; $g = 0; $b = 0; $count = 0
    for ($x = 0; $x -lt $bmp.Width; $x += 50) {
        for ($y = 0; $y -lt $bmp.Height; $y += 50) {
            $pixel = $bmp.GetPixel($x, $y)
            $r += $pixel.R
            $g += $pixel.G
            $b += $pixel.B
            $count++
        }
    }
    $r = [Math]::Round($r / $count)
    $g = [Math]::Round($g / $count)
    $b = [Math]::Round($b / $count)
    Write-Host "AvgColor: $r,$g,$b"
    $bmp.Dispose()
} else {
    Write-Host "File not found"
}
