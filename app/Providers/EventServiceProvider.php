<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Add some items to the menu...
            if(request()->route('tourney_id')) {
                $event->menu->addIn('tournaments', [
                    'key'  => 'pools',
                    'text' => 'Map Pools',
                    'route'  => ['pool.index', ['tourney_id' => request()->route('tourney_id')]],
                    'icon' => 'fas fa-fw fa-file',
                    'active' => ['pools']
                ],
                [
                    'key'  => 'players',
                    'text' => 'Players',
                    'route'  => ['player.index', ['tourney_id' => request()->route('tourney_id')]],
                    'icon' => 'fas fa-fw fa-user',
                    'active' => ['players']
                ]);
            }            
        });
    }
}
