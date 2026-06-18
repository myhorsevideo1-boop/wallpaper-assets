<?php
/**
 * Wallpapers API  (reads local JSON files — no GitHub)
 * Usage:  wallpapers.php?id=nature
 * Returns the wallpaper list JSON for the given category id:
 *   [ { "id": "...", "title": "...", "url": "..." }, ... ]
 *
 * Server layout expected (put these together):
 *   wallpapers.php
 *   categories.php
 *   data/nature.json, data/cars.json, ...   <-- the per-category JSON files
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // allow your app to call this

// Folder (on THIS server) that holds the per-category JSON files.
// Default: a "data" folder next to this PHP file. Change if you placed it elsewhere.
$DATA_DIR = __DIR__ . '/data';

// Allowed category ids (must match your data/*.json files)
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
// basename() strips any path tricks like ../  — extra safety
$id = basename($id);
if (!in_array($id, $VALID, true)) {
    http_response_code(404);
    echo json_encode(['error' => 'Unknown category id: ' . $id]);
    exit;
}

// 2) Build the local file path and read it
$file = $DATA_DIR . '/' . $id . '.json';
if (!is_file($file)) {
    http_response_code(404);
    echo json_encode(['error' => 'Data file not found for: ' . $id]);
    exit;
}

$json = file_get_contents($file);
if ($json === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not read data for: ' . $id]);
    exit;
}

// 3) Return it as-is (already in the required format)
echo $json;
