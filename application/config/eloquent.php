<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$capsule = new Illuminate\Database\Capsule\Manager();

foreach ($db as $key => $conn) {
    $capsule->addConnection(
        [
            'driver'    => 'mysql',
            'host'      => $conn['hostname'],
            'database'  => $conn['database'],
            'username'  => $conn['username'],
            'password'  => $conn['password'],
            'charset'   => $conn['char_set'],
            'collation' => $conn['dbcollat'],
            'prefix'    => $conn['dbprefix']
        ],
        $key
    );    
}

$capsule->setAsGlobal();
$capsule->bootEloquent();