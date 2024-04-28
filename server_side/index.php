<?php

require_once __DIR__ . '/base.class.php';
require_once __DIR__ . '/translate.class.php';
require_once __DIR__ . '/favorites.class.php';

header('Content-Type: application/json');

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

function prepareText(string $text)
{
    if (preg_match('/^([0-9A-F]\.\s)(.*)$/', $text, $matches)) {
        $prefix = $matches[1];
        $text = trim($matches[2]);
    } else {
        $prefix = '';
        $text = trim($text);
    }

    return ['text' => $text, 'prefix' => $prefix];
}

$favorites = new Favorites();
$translate = new Translate();

if (isset($input['text'])) {
    $prepared = prepareText($input['text']);
    $result = $translate->performTranslation($prepared['text']);

    if ($result['translate']) {
        $result['translate'] = $prepared['prefix'] . $result['translate'];
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} elseif (isset($input['approve'])) {
    $prepared = prepareText($input['approve']);

    echo json_encode(
        ['error' => null, 'success' => $translate->approveTranslation($prepared['text'])],
        JSON_UNESCAPED_UNICODE
    );
} elseif (isset($input['mark_incorrect'])) {
    $prepared = prepareText($input['mark_incorrect']);

    echo json_encode(
        ['error' => null, 'success' => $translate->markTranslationAsIncorrect($prepared['text'])],
        JSON_UNESCAPED_UNICODE
    );
} elseif (isset($input['add_to_favorites'])) {
    $favorites->saveToFavorites($input['add_to_favorites']);

    echo json_encode(
        ['error' => null, 'success' => true, 'favorites' => $favorites->getFavorites()],
        JSON_UNESCAPED_UNICODE
    );
} elseif (isset($input['remove_from_favorites'])) {
    $favorites->removeFromFavorites($input['remove_from_favorites']);

    echo json_encode(
        ['error' => null, 'success' => true, 'favorites' => $favorites->getFavorites()],
        JSON_UNESCAPED_UNICODE
    );
} else {
    echo json_encode(
        ['error' => null, 'favorites' => $favorites->getFavorites()],
        JSON_UNESCAPED_UNICODE
    );
}
