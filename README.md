# Flysystem的upyun存储驱动

hyperf框架的flysystem文件系统的upyun(又拍云)的云存储驱动实现

[hyperf文件系统](https://hyperf.wiki/3.1/#/zh-cn/filesystem)

[League Flysystem](https://github.com/thephpleague/flysystem)

[![PHP Version Require](https://poser.pugx.org/muqiuren/hyperf-flysystem-upyun/require/php)](https://packagist.org/packages/muqiuren/hyperf-flysystem-upyun)
[![License](https://poser.pugx.org/muqiuren/hyperf-flysystem-upyun/license)](https://packagist.org/packages/muqiuren/hyperf-flysystem-upyun)
[![Total Downloads](https://poser.pugx.org/muqiuren/hyperf-flysystem-upyun/downloads)](https://packagist.org/packages/muqiuren/hyperf-flysystem-upyun)
[![Latest Stable Version](https://poser.pugx.org/muqiuren/hyperf-flysystem-upyun/v)](https://packagist.org/packages/muqiuren/hyperf-flysystem-upyun)

### 安装

```shell
composer require muqiuren/hyperf-flysystem-upyun
```

### 快速使用

1. hyperf配置config/autoload/file.php,添加upyun云存储配置(操作员名称、密码、存储服务名)，[又拍云文档](https://help.upyun.com/knowledge-base/quick_start/)

```php
...
'storage' => [
    ...
    'upyun' => [
        'driver' => UpyunAdapterFactory::class,
        'username' => env('UPYUN_USERNAME'),
        'password' => env('UPYUN_PASSWORD'),
        'bucket_name' => env('UPYUN_BUCKET'),
        'options' => [
            UpyunHeaderEnum::CONTENT_SECRET->value => 'common_secret'
        ]
    ]
]
```

2. 通过FilesystemFactory调用云存储

```php
// 上传文件
public function putObject(\Hyperf\Filesystem\FilesystemFactory $factory)
{
    $storage = $factory->get('upyun');
    $path = '/test/hello.txt';
    $content = 'hello world';
    $storage->write($path, $content, [
        UpyunHeaderEnum::CONTENT_SECRET->value => 'custom_single_file_secret'
    ]);
}
```

更多示例请参考example目录

### TODO

- [ ] 列出目录文件内容
- [ ] 支持配置header以及meta信息
