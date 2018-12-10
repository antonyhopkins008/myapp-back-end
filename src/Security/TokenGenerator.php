<?php


namespace App\Security;


class TokenGenerator {

    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';

    public function getRandomToken(int $length = 30): string
    {
        $token = '';
        $max = strlen(self::ALPHABET);
        for ($i = 0; $i < $length; $i++) {
            $token .= self::ALPHABET[random_int(0, $max - 1)];
        }

        return $token;
    }
}