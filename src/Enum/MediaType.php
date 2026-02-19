<?php 

namespace App\Enum;

enum MediaType: string 
{
    case PHOTO = "photo";
    case VIDEO = 'video';
    case AUDIO = "audio";

    public static function getType(): array 
    {
        return [
            self::PHOTO,
            self::VIDEO,
            self::AUDIO
        ];
    }
}