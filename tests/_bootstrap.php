<?php
// The global bootstrap file, it will apply to all suites.

use Codeception\Util\Autoload;

Autoload::addNamespace( '\\Pngx\\Tests', __DIR__ . '/_support' );
