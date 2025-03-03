<?php

require_once __DIR__ . "/../vendor/autoload.php";

use League\Flysystem\Config;
use Muqiuren\Hyperf\Flysystem\UpyunServer;
use Muqiuren\Hyperf\Flysystem\Enums\UpyunHeaderEnum;


$username = '';
$password = '';
$bucket = '';
$options = [
    // 分页每页数量，默认100，最多10000。参考：https://help.upyun.com/knowledge-base/rest_api/#e88eb7e58f96e69687e4bbb6e4bfa1e681af
    UpyunHeaderEnum::LIST_LIMIT->value => 100,
    // 文件访问密钥，(ps: 通过write设置，可以对单个文件进行设置)。参考：https://help.upyun.com/knowledge-base/rest_api/#e4b88ae4bca0e69687e4bbb6
    UpyunHeaderEnum::CONTENT_SECRET->value => 'common_secret',
];

$upyun = new UpyunServer($bucket, $username, $password, $options);

// 删除文件
/*$upyun->delete('/test/hello3.txt');*/

// 读取文件
/*$content = $upyun->read('/test/hello2.txt');*/

// 上传文件
/*$upyun->write('/test/hello4.txt', 'hello world2', new Config([
    UpyunHeaderEnum::CONTENT_SECRET->value => '123456',
]));*/


// 获取指定目录文件列表
/*foreach ($upyun->listContents('test/', true) as $item) {
    echo sprintf("file: %s\nsize: %d\ntimestamp: %s\n", $item->path(), $item->fileSize(), $item->lastModified()) . str_repeat("-", 40) . PHP_EOL;
}*/


