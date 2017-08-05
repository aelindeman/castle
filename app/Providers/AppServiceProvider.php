<?php

namespace Castle\Providers;

use Cache;
use Castle\Behaviors\Attachable;
use Castle\Client;
use Castle\Comment;
use Castle\Discussion;
use Castle\Document;
use Castle\Resource;
use Castle\ResourceType;
use Illuminate\Support\ServiceProvider;
use Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $models = [
            Client::class => [
                'deleteCachedContent'
            ],
            Comment::class => [
                'deleteCachedContent'
            ],
            Discussion::class => [
                'deleteAttachments',
                'deleteCachedContent'
            ],
            Document::class => [
                'deleteAttachments',
                'deleteCachedContent'
            ],
            Resource::class => [
                'deleteAttachments',
                'deleteUnusedResourceTypes',
                'deleteCachedContent'
            ],
        ];

        foreach ($models as $model => $events) {
            foreach ($events as $event) {
                switch ($event) {
                    case 'deleteAttachments':
                        $model::deleted([$this, $event]);
                        break;

                    case 'deleteCachedContent':
                    case 'deleteUnusedResourceTypes':
                        $model::updated([$this, $event]);
                        $model::deleted([$this, $event]);
                        break;
                }
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Delete attachments from deleted objects.
     */
    public static function deleteAttachments($item)
    {
        assert(
            array_key_exists(
                Attachable::class,
                class_uses($item)
            ),
            'object does not use Attachable trait'
        );

        if (isset($item->attachments)) {
            foreach ($item->attachments as $attachment) {
                if (Storage::disk('attachments')->has($attachment)) {
                    Storage::disk('attachments')->delete($attachment);
                }
            }
        }
    }

    /**
     * Delete the cached content from an updated or deleted item.
     */
    public static function deleteCachedContent($item)
    {
        if ($item->cacheKey) {
            Cache::forget($item->cacheKey);
        }
    }

    /**
     * Delete ResourceTypes when they are no longer used.
     */
    public static function deleteUnusedResourceTypes()
    {
        ResourceType::whereNotIn('id', function ($query) {
            $query->select('resource_type_id')->distinct()->from('resources');
        })->delete();
    }
}
