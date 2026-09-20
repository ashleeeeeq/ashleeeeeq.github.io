<?php

use CodeIgniter\Boot;
use Config\Paths;

if (version_compare(PHP_VERSION, '7.4', '<')) {
    exit('Your PHP version is too old. Please upgrade to PHP 7.4 or later.');
}

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

require FCPATH . 'app/Config/Paths.php';

$paths = new Paths();

require $paths->systemDirectory . '/Boot.php';

exit(Boot::bootWeb($paths));