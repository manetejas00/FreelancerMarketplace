<?php

namespace App\Helpers;

class EncryptionHelper
{
    public static function decodeId($encodedId)
    {
        return base64_decode($encodedId);
    }
}
