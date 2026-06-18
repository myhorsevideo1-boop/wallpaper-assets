<?php
/**
 * Categories API  (reads local JSON file — no GitHub)
 * Usage:  categories.php
 * Returns the category list JSON:
 *   [ { "id": "...", "title": "...", "image": "..." }, ... ]
 *
 * Expects categories.json to sit next to this PHP file.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$file = __DIR__ . '/categories.json';

if (!is_file($file)) {
    http_response_code(404);
    echo json_encode(['error' => 'categories.json not found']);
    exit;
}

$json = file_get_contents($file);
if ($json === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not read categories.json']);
    exit;
}

echo $json;
