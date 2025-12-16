<?php

namespace App\Providers\Filament;

use AchyutN\FilamentLogViewer\FilamentLogViewer;
use App\Filament\Pages\Dashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Lang;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\View\View;
use Laravel\Horizon\Console\TerminateCommand;

class AdminPanelProvider extends PanelProvider
{
    public function boot()
    {
        $this->commands([
            TerminateCommand::class,
        ]);
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->navigationItems([
                NavigationItem::make()
                    ->label('Beranda')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->sort(-999)
                    ->url('/', shouldOpenInNewTab: true),
                NavigationItem::make('horizon')
                    ->label('Horizon')
                    ->icon('heroicon-o-cpu-chip')
                    ->group('Pengaturan')
                    ->sort(999)
                    ->url(fn () => url(config('horizon.path')), shouldOpenInNewTab: true)
                    ->hidden(fn (Request $request) => ! $request->user()?->hasRole('super_admin')),
            ])
            ->navigationGroups([
                'Pendukung',
                'Pengaturan',
            ])
            ->font('Barlow')
            ->sidebarWidth('14rem')
            ->maxContentWidth('full')
            ->spa()
            ->renderHook(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE, fn (): View => view('components.total-records'))
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, fn ()  => view('components.select-tahun'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->renderHook(PanelsRenderHook::STYLES_BEFORE, fn (): string => Blade::render(<<<'HTML'
                <style>
                    /** Setting Base Font */
                    html, body{
                        font-size: 14px;
                    }
                </style>
                <!-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> -->
            HTML))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
                (new class
                {
                    public static function make(FilamentLogViewer $plugin): FilamentLogViewer
                    {
                        // force language to indonesian, sometimes filament log viewer uses english language
                        $langs = Arr::dot([
                            'log' => Lang::get('filament-log-viewer::log'),
                        ]);

                        Lang::addLines([
                            ...$langs,
                            'log.navigation.group' => 'Pengaturan',
                        ], 'en', 'filament-log-viewer');

                        $plugin->authorize(fn () => Auth::user()->hasAnyRole('super_admin', 'super-admin'));
                        // this shit not working cause getNavigationGroup() is calling language instead plugins variable.
                        $plugin->navigationGroup('Pengaturan');
                        $plugin->navigationIcon($plugin->getNavigationIcon());
                        $plugin->navigationLabel('Logs');
                        $plugin->navigationSort(11);
                        $plugin->navigationUrl($plugin->getNavigationUrl());
                        $plugin->pollingTime($plugin->getPollingTime());

                        return $plugin;
                    }
                })::make(new FilamentLogViewer),
            ])
            ->globalSearch(false)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
