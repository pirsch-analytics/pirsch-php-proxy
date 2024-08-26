<?php

if (!file_exists('scripts')) {
    mkdir('scripts', 0755);
}

function serveFile($name) {
    try {
        $path = 'scripts/'.$name;
        $maxAge = time()-10;

        if (!file_exists($path) || filemtime($path) < $maxAge) {
            file_put_contents($path, fopen('https://api.pirsch.io/'.$name, 'r'));
        }

        header('Content-Type: application/javascript');
        header('Content-Encoding: gzip');
        $content = file_get_contents($path);
        echo gzencode($content);
    } catch (Exception $e) {
        http_response_code(500);
        error_log($e->getMessage());
    }
}
