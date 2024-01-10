<?php

require_once __DIR__ . '/translate.class.php';

header('Content-Type: application/json');

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (isset($input['text'])) {
    echo json_encode(['translate' => Translate::translate($input['text'])]);
} else {
    echo json_encode(['error' => 'No text provided']);
}
