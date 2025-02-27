<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Muqiuren\Hyperf\Flysystem\UpyunServer;
use League\Flysystem\Config;

$username = '';
$password = '';
$bucket = '';

$upyun = new UpyunServer($bucket, $username, $password);
$config = new Config();
$upyun->write('/test/hello.txt', 'hello world', $config);

