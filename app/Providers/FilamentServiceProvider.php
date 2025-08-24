<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Filament::serving(function () {
            // Register navigation groups
            Filament::registerNavigationGroups([
                NavigationGroup::make('Inventory Management')
                    ->icon('heroicon-o-cube')
                    ->collapsed(),
                NavigationGroup::make('Production')
                    ->icon('heroicon-o-cog')
                    ->collapsed(),
                NavigationGroup::make('Sales & Retail')
                    ->icon('heroicon-o-shopping-cart')
                    ->collapsed(),
                NavigationGroup::make('Reports')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsed(),
                NavigationGroup::make('System')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ]);

            // Add mobile-optimized navigation
            Filament::registerNavigationItems([
                NavigationItem::make('Mobile Dashboard')
                    ->url('/mobile')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->group('Mobile Access')
                    ->sort(1),
            ]);
        });

        // Configure for mobile optimization
        Filament::registerTheme(
            app()->resourcePath('css/filament.css')
        );
    }
}