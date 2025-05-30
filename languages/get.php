<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/javascript');

if (!isset($_GET['lang'])) {
    sendError();
}

$language = $_GET['lang'];
if (!preg_match("/^[0-9a-z\-]+$/i", $language)) {
    sendError();
}

if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && 
    strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}

processLanguageFile($language);
ob_end_flush();
function sendError() {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
    echo "?";
    exit;
}

function processLanguageFile($language) {
    $filename = './' . basename($language) . '.json';
    if (!file_exists($filename)) {
        sendError();
    }

    $langJson = file_get_contents($filename);
    if ($langJson === false) {
        sendError();
    }
    $langHash = md5($langJson);
    header('Etag: ' . $langHash);
    $oneWeek = 604800;
    header('Cache-Control: public, max-age=' . $oneWeek);
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $oneWeek) . ' GMT');
    $clientEtag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? 
        trim($_SERVER['HTTP_IF_NONE_MATCH']) : '';
    
    if ($clientEtag === $langHash) {
        http_response_code(304);
        exit;
    }
    echo $langJson;
}