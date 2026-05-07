<?php

namespace App\Providers\Filament;

use App\Filament\Pages\CustomDashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Green,
            ])
            ->brandName('SmartShamba Admin')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                CustomDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                'logout' => Pages\Actions\LogoutAction::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Workforce')
                    ->icon('heroicon-o-users')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Farm Operations')
                    ->icon('heroicon-o-academic-cap')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Planning')
                    ->icon('heroicon-o-calendar')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Finance')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Market')
                    ->icon('heroicon-o-shopping-cart')
                    ->collapsible(false),
            ])
            ->navigationItems([
                NavigationItem::make('Users')
                    ->label('Users')
                    ->icon('heroicon-o-users')
                    ->url(fn (): string => route('filament.admin.resources.users.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.users.*'))
                    ->sort(1),

                NavigationItem::make('Dashboard')
                    ->label('Dashboard')
                    ->icon('heroicon-o-home')
                    ->url(fn (): string => route('admin.dashboard'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('admin.dashboard'))
                    ->sort(1),

                // Workforce
                NavigationItem::make('Workers')
                    ->label('Workers')
                    ->icon('heroicon-o-user-group')
                    ->url(fn (): string => route('workers.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('workers.*'))
                    ->group('Workforce')
                    ->sort(2),

                // Farm Operations
                NavigationItem::make('Harvests')
                    ->label('Harvests')
                    ->icon('heroicon-o-archive-box')
                    ->url(fn (): string => route('harvests.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('harvests.*'))
                    ->group('Farm Operations')
                    ->sort(3),
                NavigationItem::make('Food Stocks')
                    ->label('Food Stocks')
                    ->icon('heroicon-o-cube')
                    ->url(fn (): string => route('food-stocks.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('food-stocks.*'))
                    ->group('Farm Operations')
                    ->sort(4),
                NavigationItem::make('Crop Cycles')
                    ->label('Crop Cycles')
                    ->icon('heroicon-o-seedling')
                    ->url(fn (): string => route('filament.admin.resources.crop-cycles.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.crop-cycles.*'))
                    ->group('Farm Operations')
                    ->sort(5),
                NavigationItem::make('Crop Stages')
                    ->label('Crop Stages')
                    ->icon('heroicon-o-list-todo')
                    ->url(fn (): string => route('crop-stages.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('crop-stages.*'))
                    ->group('Farm Operations')
                    ->sort(6),
                NavigationItem::make('Activities')
                    ->label('Activities')
                    ->icon('heroicon-o-wrench')
                    ->url(fn (): string => route('activities.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('activities.*'))
                    ->group('Farm Operations')
                    ->sort(7),
                NavigationItem::make('Inputs')
                    ->label('Inputs')
                    ->icon('heroicon-o-box')
                    ->url(fn (): string => route('inputs.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('inputs.*'))
                    ->group('Farm Operations')
                    ->sort(8),

                // Planning
                NavigationItem::make('Planting Schedule')
                    ->label('Planting Schedule')
                    ->icon('heroicon-o-calendar-alt')
                    ->url(fn (): string => route('planting-schedules.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('planting-schedules.index'))
                    ->group('Planning')
                    ->sort(1),
                NavigationItem::make('Crop Calendar')
                    ->label('Crop Calendar')
                    ->icon('heroicon-o-calendar-days')
                    ->url(fn (): string => route('filament.admin.pages.planting-calendar'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.planting-calendar'))
                    ->group('Planning')
                    ->sort(2),

                // Finance
                NavigationItem::make('Expenses')
                    ->label('Expenses')
                    ->icon('heroicon-o-banknotes')
                    ->url(fn (): string => route('expenses.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('expenses.*'))
                    ->group('Finance')
                    ->sort(9),
                NavigationItem::make('Revenues')
                    ->label('Revenues')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->url(fn (): string => route('revenues.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('revenues.*'))
                    ->group('Finance')
                    ->sort(10),

                // Market
                NavigationItem::make('Buyers')
                    ->label('Buyers')
                    ->icon('heroicon-o-user')
                    ->url(fn (): string => route('buyers.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('buyers.*'))
                    ->group('Market')
                    ->sort(7),
                NavigationItem::make('Orders')
                    ->label('Orders')
                    ->icon('heroicon-o-shopping-bag')
                    ->url(fn (): string => route('orders.index'))
                    ->isActiveWhen(fn (): bool => request()->routeIs('orders.*'))
                    ->group('Market')
                    ->sort(8),
            ]);
    }
}
