<?php

function lagerUrl(string $path): string
{
    $root = str_replace('\\', '/', dirname(__DIR__));
    $scriptFile = realpath($_SERVER['SCRIPT_FILENAME'] ?? '');
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptFile = $scriptFile === false ? '' : str_replace('\\', '/', $scriptFile);

    if (strpos($scriptFile, $root . '/') !== 0) {
        throw new RuntimeException('Kunne ikke bestemme lager-sitets URL-rod.');
    }

    $relativeScript = substr($scriptFile, strlen($root));
    if (substr($scriptName, -strlen($relativeScript)) !== $relativeScript) {
        throw new RuntimeException('Scriptets URL svarer ikke til lager-sitets mappestruktur.');
    }

    $base = substr($scriptName, 0, strlen($scriptName) - strlen($relativeScript));
    if (preg_match('/[\x00-\x20"\'<>?#]/', $base) || strpos($base, '//') !== false) {
        throw new RuntimeException('Ugyldig URL-rod for lager-sitet.');
    }

    return $base . '/' . ltrim($path, '/');
}
