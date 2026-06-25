<?php

namespace App\Providers;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('layouts.app', function ($view) {
            $notificationCount = 0;
            $messageCount = 0;

            if (Auth::check()) {
                /** @var \App\Models\User $user */
                $user = Auth::user();

                $notificationCount = $user->unreadNotifications()->count();
                $messageCount = Message::query()
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->count();
            }

            $view->with('notificationCount', $notificationCount)
                ->with('messageCount', $messageCount);
        });
    }
}
