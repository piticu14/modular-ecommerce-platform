<?php

namespace App\Support;

final class InternalRequestSigner
{
    public static function sign(
        string $method,
        string $path,
        string $userId,
        string $correlationId,
        string $nonce,
        string $timestamp,
        string $secret
    ): string {
        $data = implode('|', [
            strtoupper($method),
            $path,
            $userId,
            $correlationId,
            $nonce,
            $timestamp,

        ]);

        return hash_hmac('sha256', $data, $secret);
    }
}
