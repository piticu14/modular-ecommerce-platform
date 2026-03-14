<?php

    namespace App\Support;

    final class RequestContext
    {
        public static function userId(): ?int
        {
            $value = request()->header('X-User-Id');

            return is_numeric($value) ? (int) $value : null;
        }

        public static function correlationId(): ?string
        {
            $value = request()->header('X-Correlation-ID');

            return is_string($value) && $value !== '' ? $value : null;
        }

        public static function requestId(): ?string
        {
            $value = request()->header('X-Request-ID');

            return is_string($value) && $value !== '' ? $value : null;
        }
    }
