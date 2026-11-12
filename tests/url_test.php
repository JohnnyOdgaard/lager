<?php

require_once __DIR__ . '/../includes/url.php';

$root = dirname(__DIR__);
$cases = 0;
foreach (['', '/lager', '/test/lager'] as $base) {
    foreach (['index.php', 'modules/indkoeb/indkoeb.php', 'system/status.php'] as $script) {
        $_SERVER['SCRIPT_FILENAME'] = $root . '/' . $script;
        $_SERVER['SCRIPT_NAME'] = $base . '/' . $script;
        foreach (['/index.php', '/assets/css/styles.css', '/csv/lagebeholdning.csv',
                  '/modules/bestilling/bestil_update.php', '/modules/indkoeb/indkoeb_fra_fjern.php?varid=1196'] as $path) {
            if (lagerUrl($path) !== $base . $path || lagerUrl(ltrim($path, '/')) !== $base . $path) {
                throw new RuntimeException('Forkert URL for ' . $_SERVER['SCRIPT_NAME']);
            }
            $cases++;
        }

        ob_start();
        include $root . '/includes/header.php';
        include $root . '/includes/footer.php';
        $html = ob_get_clean();
        preg_match_all('/(?:href|src)="(\/[^"]*)"/', $html, $matches);
        foreach ($matches[1] as $url) {
            if (strpos($url, $base . '/') !== 0 || strpos($url, '//') === 0) {
                throw new RuntimeException('Forkert link i layout: ' . $url);
            }
        }
        if (substr_count($html, $base . '/assets/js/bootstrap.bundle.min.js') !== 1) {
            throw new RuntimeException('Bootstrap skal indlaeses praecis en gang.');
        }
    }
}

foreach (['/forkert.php', "//example.com/index.php", "/lager\r\n/index.php"] as $invalid) {
    $_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
    $_SERVER['SCRIPT_NAME'] = $invalid;
    try {
        lagerUrl('/index.php');
    } catch (RuntimeException $e) {
        $cases++;
        continue;
    }
    throw new RuntimeException('Ugyldig script-URL blev accepteret.');
}

echo "$cases URL-kontroller og layout for rod, undermappe og indlejret undermappe OK.\n";
