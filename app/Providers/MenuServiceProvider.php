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
            $user = auth()->user();

            // Add LMS Menu
            if ($user->hasRole('admin') || $user->can('show lms option')) {
                $event->menu->add([
                    'text' => 'LMS',
                    'icon' => 'fas fa-fw fa-bullhorn',
                    'submenu' => [
                        [
                            'text' => 'Leads',
                            'route' => $prefix . 'leads.index',
                            'can' => 'view lead',
                        ],
                    ],
                ]);
            }

            // Add CRM Menu
            if ($user->hasRole('admin') || $user->can('show crm option')) {
                $event->menu->add([
                    'text' => 'CRM',
                    'icon' => 'fas fa-fw fa-handshake',
                    'submenu' => [
                        [
                            'text' => 'Bank Assign',
                            'route' => $prefix . 'crm.index',
                            'can' => 'view crm',
                        ],
                        [
                            'text' => 'RTO',
                            'route' => $prefix . 'rto.index',
                            'can' => 'view rto',
                        ],
                        [
                            'text' => 'Insurance Leads',
                            'route' => $prefix . 'insurance-leads.index',
                            'can' => 'view insurance lead',
                        ],
                    ],
                ]);
            }

            // Add Expiring Insurance Menu as a standalone item
            if ($user->hasRole('admin') || $user->can('view insurance lead')) {
                $event->menu->add([
                    'text' => 'Expiring Insurance',
                    'icon' => 'fas fa-fw fa-calendar-times',
                    'route' => $prefix . 'insurance-leads.expiring',
                    'can' => 'view insurance lead',
                ]);
            }

            // Add Referral Log Menu (Admin Only)
            if ($user->hasRole('admin')) {
                $event->menu->add([
                    'text' => 'Referral Log',
                    'icon' => 'fas fa-fw fa-money-check-alt',
                    'route' => $prefix . 'referral-logs.index',
                    'can' => 'view referral log',
                ]);
            }

            // Add Referral Person Menu (Admin Only)
            if ($user->hasRole('admin')) {
                $event->menu->add([
                    'text' => 'Referral',
                    'icon' => 'fas fa-fw fa-user-friends',
                    'route' => $prefix . 'referral-persons.index',
                    'can' => 'view referral log', // Assuming same permission
                ]);
            }

            // Payment Release as a standalone menu item at the bottom
            if ($user->hasRole('admin') || $user->can('view crm')) {
                $event->menu->add([
                    'text' => 'Payment Release',
                    'icon' => 'fas fa-fw fa-hand-holding-usd',
                    'route' => $prefix . 'payment-release.index',
                    'can' => 'view crm',
                ]);
            }

            // Add Employees Menu (Admin or Users with permissions)
            if ($user->hasRole('admin') || $user->can('show employee option')) {
                $event->menu->add([
                    'text' => 'Employees',
                    'icon' => 'fas fa-fw fa-users',
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
            }

            // Add Masters Menu (Admin Only or if user has permission)
            // We check if the user has ANY of the master view permissions to show the parent menu
            if ($user->hasRole('admin') || $user->can('show master option')) {

                $mastersMenu = [
                    'text' => 'Masters',
                    'icon' => 'fas fa-fw fa-cogs',
                    'submenu' => [],
                ];

                if ($user->hasRole('admin') || $user->can('view bank')) {
                    $mastersMenu['submenu'][] = [
                        'text' => 'Banks',
                        'route' => 'admin.banks.index',
                        'icon' => 'fas fa-fw fa-university',
                        'can' => 'view bank',
                    ];
                }

                if ($user->hasRole('admin') || $user->can('view firm')) {
                    $mastersMenu['submenu'][] = [
                        'text' => 'Firms',
                        'route' => 'admin.firms.index',
                        'icon' => 'fas fa-fw fa-building',
                        'can' => 'view firm',
                    ];
                }

                if ($user->hasRole('admin') || $user->can('view rto agent')) {
                    $mastersMenu['submenu'][] = [
                        'text' => 'RTO Agents',
                        'route' => 'admin.rto-agents.index',
                        'icon' => 'fas fa-fw fa-user-secret',
                        'can' => 'view rto agent',
                    ];
                }

                if ($user->hasRole('admin') || $user->can('view insurance')) {
                    $mastersMenu['submenu'][] = [
                        'text' => 'Insurances',
                        'route' => 'admin.insurances.index',
                        'icon' => 'fas fa-fw fa-shield-alt',
                        'can' => 'view insurance',
                    ];
                }

                if ($user->hasRole('admin') || $user->can('view vehicle manufacture')) {
                    $mastersMenu['submenu'][] = [
                        'text' => 'Vehicle Manufactures',
                        'route' => 'admin.vehicle-manufactures.index',
                        'icon' => 'fas fa-fw fa-car',
                        'can' => 'view vehicle manufacture',
                    ];
                }

                if (!empty($mastersMenu['submenu'])) {
                    $event->menu->add($mastersMenu);
                }
            }
        });
    }
}
