<?php
// Check video file streams
$videosPath = 'videos/basics/';
$files = glob($videosPath . '*.mp4');

echo "=== Video File Diagnostic ===\n\n";

foreach ($files as $file) {
    echo "File: " . basename($file) . "\n";
    echo "Full Path: " . realpath($file) . "\n";
    echo "File Size: " . filesize($file) . " bytes (" . round(filesize($file) / 1048576, 2) . " MB)\n";
    echo "Readable: " . (is_readable($file) ? "Yes" : "No") . "\n";

    // Try to get file info using PHP
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file);
    finfo_close($finfo);
    echo "MIME Type: " . $mimeType . "\n";

    // Check first bytes for MP4 signature
    $handle = fopen($file, 'rb');
    if ($handle) {
        $header = fread($handle, 12);
        fclose($handle);

        // Check for MP4 file signature (ftyp box)
        if (strpos($header, 'ftyp') !== false) {
            echo "MP4 Signature: Valid ✓\n";
        } else {
            echo "MP4 Signature: Invalid ✗\n";
        }
    }

    echo "---\n\n";
}

echo "Note: To check for VIDEO vs AUDIO-ONLY streams, you need ffprobe.\n";
echo "Install ffmpeg/ffprobe to analyze video streams properly.\n";
