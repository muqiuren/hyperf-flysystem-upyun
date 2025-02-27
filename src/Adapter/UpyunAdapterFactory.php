<?php
declare(strict_types=1);
namespace Muqiuren\Hyperf\Flysystem\Adapter;

use Hyperf\Filesystem\Contract\AdapterFactoryInterface;
use Muqiuren\Hyperf\Flysystem\UpyunServer;

class UpyunAdapterFactory implements AdapterFactoryInterface
{
    public function make(array $options): UpyunServer
    {
        return new UpyunServer($options['bucket_name'], $options['username'], $options['password'], $options['options']);
    }
}
