<?php

namespace Muqiuren\Hyperf\Flysystem;

use GuzzleHttp\Psr7\Utils;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathPrefixer;
use League\Flysystem\UnableToCheckDirectoryExistence;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use Muqiuren\Hyperf\Flysystem\Enums\UpyunHeaderEnum;
use Throwable;
use Upyun\Upyun;

class UpyunServer implements FilesystemAdapter
{
    private Upyun $client;

    private PathPrefixer $prefixer;

    public function __construct(
        private string $bucketName,
        private string $username,
        private string $password,
        private array $options = [],
    )
    {
        $config = new \Upyun\Config($this->bucketName, $this->username, $this->password);
        $this->client = new Upyun($config);
        $this->prefixer = new PathPrefixer($this->options['prefix'] ?? '');
    }

    /**
     * 携带可用请求头参数
     * @param Config $config
     * @return array
     */
    protected function withAvailableHeaders(Config $config): array
    {
        $availableHeaders = UpyunHeaderEnum::getValues();
        return array_filter($config->toArray(), fn($v, $k) => in_array($k, $availableHeaders, true), ARRAY_FILTER_USE_BOTH);
    }

    /**
     * 检测文件是否存在
     * @param string $path
     * @return bool
     */
    public function fileExists(string $path): bool
    {
        try {
            return $this->client->has($path);
        } catch (Throwable $e) {
            throw UnableToCheckFileExistence::forLocation($path, $e);
        }
    }

    /**
     * 检测目录是否存在
     * @param string $path
     * @return bool
     */
    public function directoryExists(string $path): bool
    {
        try {
            $prefix = $this->prefixer->prefixDirectoryPath($path);
            return $this->client->has($prefix);
        } catch (Throwable $e) {
            throw UnableToCheckDirectoryExistence::forLocation($path, $e);
        }
    }

    /**
     * 通过字符串上传文件
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return void
     */
    public function write(string $path, string $contents, Config $config): void
    {
        try {
            $this->client->write($path, $contents, $this->withAvailableHeaders($config));
        } catch (Throwable $e) {
            throw UnableToWriteFile::atLocation($path, $e);
        }
    }

    /**
     * 通过文件流上传文件
     * @param string $path
     * @param $contents
     * @param Config $config
     * @return void
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        try {
            $resource = is_resource($contents) ? $contents : @fopen($contents, 'r');

            if (!$resource) {
                throw new \RuntimeException('Unable to open stream for reading: ' . $path);
            }

            $this->client->write($path, $resource, $this->withAvailableHeaders($config));

            if ($resource !== $contents) {
                fclose($resource);
            }
        } catch (Throwable $e) {
            throw UnableToWriteFile::atLocation($path, $e);
        }
    }

    /**
     * 读取文件内容
     * @param string $path
     * @return string
     */
    public function read(string $path): string
    {
        try {
            return $this->client->read($path);
        } catch (Throwable $e) {
            throw UnableToReadFile::fromLocation($path, $e);
        }
    }

    /**
     * 读取文件，返回文件流
     * @param string $path
     * @return resource|null
     */
    public function readStream(string $path)
    {
        try {
            return Utils::streamFor($this->read($path))->detach();
        } catch (Throwable $e) {
            throw UnableToReadFile::fromLocation($path, $e);
        }
    }

    /**
     * 删除文件
     * @param string $path
     * @return void
     */
    public function delete(string $path): void
    {
        try {
            $this->client->delete($path);
        } catch (Throwable $e) {
            throw UnableToDeleteFile::atLocation($path, $e);
        }
    }

    /**
     * 删除目录
     * @param string $path
     * @return void
     */
    public function deleteDirectory(string $path): void
    {
        try {
            $this->client->deleteDir($path);
        } catch (Throwable $e) {
            throw UnableToDeleteDirectory::atLocation($path, $e);
        }
    }

    /**
     * 创建目录
     * @param string $path
     * @param Config $config
     * @return void
     */
    public function createDirectory(string $path, Config $config): void
    {
        try {
            $this->client->createDir($path);
        } catch (Throwable $e) {
            throw UnableToCreateDirectory::atLocation($path, $e);
        }
    }

    /**
     * 可见性设置(ps:upyun不支持可见性设置)
     * @param string $path
     * @param string $visibility
     * @return void
     */
    public function setVisibility(string $path, string $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'Upyun unsupported visibility setting');
    }

    /**
     * 获取可见性(ps:upyun不支持可见性设置，默认公开读)
     * @param string $path
     * @return FileAttributes
     */
    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path);
    }

    /**
     * 获取文件mime_type
     * @param string $path
     * @return FileAttributes
     */
    public function mimeType(string $path): FileAttributes
    {
        $mimeType = $this->client->getMimetype($path);
        return new FileAttributes($path, mimeType: $mimeType);
    }

    /**
     * 获取文件mime_type
     * @param string $path
     * @return FileAttributes
     */
    public function lastModified(string $path): FileAttributes
    {
        try {
            $info = $this->client->info($path);
            return new FileAttributes($path, lastModified: $info[UpyunHeaderEnum::FILE_DATE->value] ?? '');
        } catch (Throwable $e) {
            throw new UnableToReadFile($path, $e);
        }
    }

    /**
     * 获取文件大小
     * @param string $path
     * @return FileAttributes
     */
    public function fileSize(string $path): FileAttributes
    {
        try {
            $info = $this->client->info($path);
            return new FileAttributes($path, $info[UpyunHeaderEnum::FILE_SIZE->value] ?? '');
        } catch (Throwable $e) {
            throw new UnableToReadFile($path, $e);
        }
    }

    public function listContents(string $path, bool $deep): iterable
    {
        // TODO: Implement listContents() method.
        return  [];
    }

    /**
     * 移动文件
     * @param string $source
     * @param string $destination
     * @param Config $config
     * @return void
     */
    public function move(string $source, string $destination, Config $config): void
    {
        try {
            $this->client->move($source, $destination);
        } catch (Throwable $e) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $e);
        }
    }

    /**
     * 复制文件
     * @param string $source
     * @param string $destination
     * @param Config $config
     * @return void
     */
    public function copy(string $source, string $destination, Config $config): void
    {
        try {
            $this->client->copy($source, $destination);
        } catch (Throwable $e) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $e);
        }
    }
}
