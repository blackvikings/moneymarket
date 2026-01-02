<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            $prefix = get_route_prefix();

            // Add LMS Menu
            $event->menu->add([
                'text' => 'LMS',
                'icon' => 'fas fa-fw fa-bullhorn',
                'can' => 'view lead',
                'submenu' => [
                    [
                        'text' => 'Leads',
                        'route' => $prefix . 'leads.index',
                        'can' => 'view lead',
                    ],
                ],
            ]);

            // Add CRM Menu
            $event->menu->add([
                'text' => 'CRM',
                'icon' => 'fas fa-fw fa-handshake',
                'can' => 'view lead',
                'submenu' => [
                    [
                        'text' => 'Bank Assign',
                        'route' => $prefix . 'crm.index',
                        'can' => 'view lead',
                    ],
                    [
                        'text' => 'RTO',
                        'route' => $prefix . 'rto.index',
                        'can' => 'view lead',
                    ],
                ],
            ]);

            // Add Employees Menu (Admin Only)
            if (auth()->user()->hasRole('admin')) {
                $event->menu->add([
                    'text' => 'Employees',
                    'icon' => 'fas fa-fw fa-users',
                    'can' => 'view employee',
                    'submenu' => [
                        [
                            'text' => 'All Employees',
                            'route' => 'admin.employees.index',
                            'can' => 'view employee',
                        ],
                        [
                            'text' => 'Roles',
                            'route' => 'admin.roles.index',
                            'icon' => 'fas fa-fw fa-user-tag',
                            'can' => 'view role',
                        ],
                        [
                            'text' => 'Permissions',
                            'route' => 'admin.permissions.index',
                            'icon' => 'fas fa-fw fa-key',
                            'can' => 'view permission',
                        ],
                    ],
                ]);

                // Add Masters Menu (Admin Only)
                $event->menu->add([
                    'text' => 'Masters',
                    'icon' => 'fas fa-fw fa-cogs',
                    'submenu' => [
                        [
                            'text' => 'Banks',
                            'route' => 'admin.banks.index',
                            'icon' => 'fas fa-fw fa-university',
                            'can' => 'view bank',
                        ],
                        [
                            'text' => 'Firms',
                            'route' => 'admin.firms.index',
                            'icon' => 'fas fa-fw fa-building',
                            'can' => 'view firm',
                        ],
                        [
                            'text' => 'RTO Agents',
                            'route' => 'admin.rto-agents.index',
                            'icon' => 'fas fa-fw fa-user-secret',
                            'can' => 'view rto agent',
                        ],
                        [
                            'text' => 'Insurances',
                            'route' => 'admin.insurances.index',
                            'icon' => 'fas fa-fw fa-shield-alt',
                            'can' => 'view insurance',
                        ],
                        [
                            'text' => 'Vehicle Manufactures',
                            'route' => 'admin.vehicle-manufactures.index',
                            'icon' => 'fas fa-fw fa-car',
                            'can' => 'view vehicle manufacture',
                        ],
                    ],
                ]);
            }
        });
    }
}
