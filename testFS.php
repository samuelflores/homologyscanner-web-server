<?php
define('flag', TRUE);
include (__DIR__ . '/../contents/dtypes.php');
include('fs.php');

$remotef = '/pica/h1/fredrw/private/project/A-37-M/last.2.pdb';
$localf = __DIR__ . '/temp/last.2.pdb';

$fs = new FileServer(key5, key8, key6);

$fs->ssh_connection();
$fs->scp_bring($remotef, $localf);
$fs->disconnect();
?>