<?php

namespace Castle\Providers;

use Cache;
use Illuminate\Support\ServiceProvider;

class VersionInfoProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('version', function() {

            // set longer version info cache duration if live
            $ttl = $this->app->isLocal() ? 10 : 240;

            $revision = Cache::remember('castle.revision', $ttl, function() {
                exec('git rev-parse --short HEAD', $out, $status);
                return ($status === 0) ? implode('', $out) : null;
            });

            $branch = Cache::remember('castle.branch', $ttl, function() {
                exec('git rev-parse --abbrev-ref HEAD', $out, $status);
                return ($status === 0) ? implode('', $out) : null;
            });

            $tag = Cache::remember('castle.version', $ttl, function() use ($revision) {
                exec('git describe --tags --exact-match --abbrev=0 '.escapeshellarg($revision).' 2>/dev/null', $out, $status);
                return ($status === 0) ? implode('', $out) : null;
            });

            $dirty = Cache::remember('castle.dirty', $ttl, function() {
                exec('git diff-index --quiet --cached HEAD', $out, $status);
                return $status === 1;
            });

            return (object) [
                'revision' => $revision,
                'branch' => $branch,
                'tag' => $tag,
                'dirty' => $dirty,
            ];
        });
    }

    public function boot()
    {
        //
    }
}
