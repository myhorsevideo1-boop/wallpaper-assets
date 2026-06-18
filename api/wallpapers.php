<?php
/**
 * Wallpapers API
 * Usage:  wallpapers.php?id=nature
 * Returns the wallpaper list JSON for the given category id:
 *   [ { "id": "...", "title": "...", "url": "..." }, ... ]
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // allow your app to call this

// Base location of the JSON data on GitHub (raw)
$RAW_BASE = 'https://raw.githubusercontent.com/myhorsevideo1-boop/wallpaper-assets/main';

// Allowed category ids (must match categories.json) — protects against bad input
$VALID = [
    'nature', 'mountains', 'space', 'abstract', 'city', 'ocean',
    'animals', 'cars', 'flowers', 'dark', 'art', 'sports',
    'food', 'architecture', 'minimal',
];

// 1) Read & validate the id param
$id = isset($_GET['id']) ? trim($_GET['id']) : '';
if ($id === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameter: id']);
    exit;
}
if (!in_array($id, $VALID, true)) {
    http_response_code(404);
    echo json_encode(['error' => 'Unknown category id: ' . $id]);
    exit;
}

// 2) Fetch that category's wallpaper list
$url  = $RAW_BASE . '/data/' . $id . '.json';
$json = fetch_url($url);

if ($json === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Could not load wallpapers for: ' . $id]);
    exit;
}

// 3) Return it as-is (already in the required format)
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
