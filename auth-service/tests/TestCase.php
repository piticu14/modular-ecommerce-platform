<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function api(string $path): string
    {
        return '/api/'.config('api.version').$path;
    }
}
