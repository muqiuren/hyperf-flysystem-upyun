<?php
declare(strict_types=1);
namespace Muqiuren\Hyperf\Flysystem\Enums;

enum UpyunHeaderEnum: string
{
    case CONTENT_MD5 = 'Content-MD5';

    case CONTENT_TYPE = 'Content-Type';

    case CONTENT_SECRET = 'Content-Secret';

    case META_X = 'x-upyun-meta-x';

    case META_TTL = 'x-upyun-meta-ttl';

    case META_THUMB = 'x-gmkerl-thumb';

    case MULTI_PART_SIZE = 'X-Upyun-Multi-Part-Size';

    case MULTI_TYPE = 'X-Upyun-Multi-Type';

    case METADATA_DIRECTIVE = 'X-Upyun-Metadata-Directive';

    case LIST_ITER = 'x-list-iter';

    case LIST_LIMIT = 'x-list-limit';

    case LIST_ORDER = 'x-list-order';

    case LIST_ACCEPT = 'Accept';

    case FILE_TYPE = 'x-upyun-file-type';

    case FILE_SIZE = 'x-upyun-file-size';

    case FILE_DATE = 'x-upyun-file-date';

    /**
     * 获取所有enums值
     * @return array
     */
    public static function getValues(): array
    {
        $cases = static::cases();
        foreach ($cases as &$item) {
            $item = $item->value;
        }
        unset($item);
        return $cases;
    }
}
