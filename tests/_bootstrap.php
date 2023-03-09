<?php
// The global bootstrap file, it will apply to all suites.

use Codeception\Util\Autoload;

require_once dirname( __FILE__, 2 ) . '/pngx-autoload.php';
require_once dirname( __FILE__, 2 ) . '/src/Pngx/Container.php';
// Load Functions.
$functions = dirname( __FILE__, 2 ) . '/src/functions';
foreach ( glob( $functions . '/*.php', GLOB_NOSORT ) as $file ) {
	require_once $file;
}
Autoload::addNamespace( '\\Pngx\\Tests', __DIR__ . '/_support' );
