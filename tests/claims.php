<?php

$root   = dirname( __DIR__ );
$plugin = file_get_contents( $root . '/oilpriceapi-widget.php' );
$readme = file_get_contents( $root . '/readme.txt' );
$block  = file_get_contents( $root . '/blocks/oil-price-widget/block.json' );

preg_match( '/^ \* Version: ([0-9.]+)$/m', $plugin, $plugin_version );
preg_match( '/^Stable tag: ([0-9.]+)$/m', $readme, $stable_tag );

if ( empty( $plugin_version[1] ) || empty( $stable_tag[1] ) || $plugin_version[1] !== $stable_tag[1] ) {
    throw new RuntimeException( 'Plugin header and stable tag must match.' );
}

$active_files = array(
    'oilpriceapi-widget.php',
    'admin/class-settings.php',
    'includes/class-shortcodes.php',
    'blocks/oil-price-widget/block.json',
    'blocks/oil-price-widget/index.js',
);

$forbidden = array(
    '/\b(BRENT|WTI|NATGAS)\b/i'                      => 'encumbered or retired commodity selector',
    '/wp_(register|enqueue)_script\s*\(/'             => 'remote executable script loading',
    '#api\.oilpriceapi\.com/v1/prices/widget#i'       => 'legacy unscoped widget endpoint',
    '/data-commodities|\bcommodities\b/i'             => 'legacy commodity attribute',
    '/\b(real[- ]time|live)\s+(oil|diesel|fuel|price)/i' => 'unsupported freshness claim',
);

foreach ( $active_files as $relative ) {
    $contents = file_get_contents( $root . '/' . $relative );
    foreach ( $forbidden as $pattern => $description ) {
        if ( preg_match( $pattern, $contents ) ) {
            throw new RuntimeException( $relative . ' contains ' . $description . '.' );
        }
    }
}

json_decode( $block, true, 512, JSON_THROW_ON_ERROR );

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS )
);
foreach ( $iterator as $file ) {
    $path = $file->getPathname();
    if ( false !== strpos( $path, DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR ) || $file->getSize() > 1000000 ) {
        continue;
    }
    $contents = file_get_contents( $path );
    if ( false !== $contents && preg_match( '/\boil_[A-Za-z0-9_-]{24,}\b/', $contents ) ) {
        throw new RuntimeException( 'Potential OilPriceAPI credential in ' . substr( $path, strlen( $root ) + 1 ) );
    }
}

echo "PASS: version sync, block JSON, active-surface claims, and credential scan\n";
