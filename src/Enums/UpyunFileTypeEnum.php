<?php
declare(strict_types=1);
namespace Muqiuren\Hyperf\Flysystem\Enums;

/**
 * @link https://help.upyun.com/knowledge-base/rest_api/#e88eb7e58f96e69687e4bbb6e4bfa1e681af
 */
enum UpyunFileTypeEnum: string
{
    case FILE_TYPE = 'N';

    case FOLDER_TYPE = 'F';
}
