<?php

namespace Castle\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \Castle\Attachment::class => \Castle\Policies\PermissionPolicy::class,
        \Castle\Client::class => \Castle\Policies\PermissionPolicy::class,
        \Castle\Comment::class => \Castle\Policies\PermissionPolicy::class,
        \Castle\Discussion::class => \Castle\Policies\PermissionPolicy::class,
        \Castle\Document::class => \Castle\Policies\PermissionPolicy::class,
        \Castle\Resource::class => \Castle\Policies\PermissionPolicy::class,
        \Castle\ResourceType::class => \Castle\Policies\PermissionPolicy::class,
        \Castle\Tag::class => \Castle\Policies\PermissionPolicy::class,
        \Castle\User::class => \Castle\Policies\PermissionPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
    }
}
