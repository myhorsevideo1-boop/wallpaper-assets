<?php
/**
 * Categories API
 * Usage:  categories.php
 * Returns the category list JSON:
 *   [ { "id": "...", "title": "...", "image": "..." }, ... ]
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$RAW_BASE = 'https://raw.githubusercontent.com/myhorsevideo1-boop/wallpaper-assets/main';

$url  = $RAW_BASE . '/categories.json';
$json = fetch_url($url);

if ($json === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Could not load categories']);
    exit;
}

echo $json;


/** Fetch a URL using cURL, falling back to file_get_contents. */
function fetch_url($url)
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_USERAGENT      => 'WallpaperApp/1.0',
        ]);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($data !== false && $code === 200) ? $data : false;
    }
    return @file_get_contents($url);
}
