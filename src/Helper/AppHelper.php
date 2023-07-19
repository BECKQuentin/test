<?php

namespace App\Helper;

use DateTimeInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\String\ByteString;

class AppHelper
{
    const CRYPT_KEY = 'ds4f5sf1cd9z84effdsq684';
    const CRYPT_IV =   '6824521891019121'; //16 bytes
    const CIPHER_METHOD = 'AES-128-CTR';

    const DEFAULT_LOCALE = 'fr';
    const SPONSORSHIP_COOKIE_NAME = 'sponsorship-father';

    const CSV_ENCODER_CONFIG = [
        CsvEncoder::DELIMITER_KEY => ",",
    ];


    public static function generateToken($length = 8): string
    {
        return ByteString::fromRandom($length, '123456789ABCDEFHIJKLMNPQRSTUVWXYZ');
    }

    public static function crypt(string $data): string
    {
        return base64_encode(openssl_encrypt($data, self::CIPHER_METHOD,
            self::CRYPT_KEY, 0, self::CRYPT_IV));
    }

    public static function decrypt(string $data): string
    {
        return trim(openssl_decrypt (base64_decode($data), self::CIPHER_METHOD,
            self::CRYPT_KEY, 0, self::CRYPT_IV));
    }

    public function slugify($text): array|string
    {
        // replace non letter or digits by -
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv'))
        {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }
}