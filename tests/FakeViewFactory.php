<?php

namespace Tests;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;

class FakeViewFactory implements ViewFactory
{
    public function make($view, $data = [], $mergeData = []): View {}

    public function exists($view): bool
    {
        return false;
    }

    public function file($path, $data = [], $mergeData = []): View {}

    public function share($key, $value = null) {}

    public function composer($views, $callback) {}

    public function creator($views, $callback) {}

    public function addNamespace($namespace, $hints) {}

    public function replaceNamespace($namespace, $hints) {}
}
