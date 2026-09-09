<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    Gate::before(function ($user, $ability) {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        return null;
    });

View::composer('components.community.sidebar', function ($view) {
        $view->with('categories', Category::all());
        $view->with('tags', Tag::all());
        
        
        $joinedGroups = Auth::check() ? Auth::user()->joinedGroups : collect();
        $view->with('joinedGroups', $joinedGroups);
    });
}
}
