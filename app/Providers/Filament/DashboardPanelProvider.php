<?php

namespace App\Providers\Filament;

use App\Filament\Auth\EditProfile;
use App\Filament\Auth\Register;
use App\Filament\AvatarProviders\InitialsAvatarProvider;
use App\Filament\Navigation\NavigationGroup;
use App\Filament\Pages\Dashboard;
use App\Http\Middleware\SetLocale;
use Filament\Actions\Action;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * The one and only panel: every registered user lands here after login and is the
 * super admin of their own habits. Each resource and page scopes its queries to
 * the signed-in user, so nobody can see anyone else's data.
 */
class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->registration(Register::class)
            ->passwordReset()
            ->profile(EditProfile::class, isSimple: false)
            ->brandName(fn (): string => __('habits.brand'))
            ->brandLogo(fn (): View => view('filament.brand'))
            ->homeUrl(fn (): string => Dashboard::getUrl())
            ->favicon(asset('favicon.svg'))
            ->defaultAvatarProvider(InitialsAvatarProvider::class)
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
            ])
            ->font('Vazirmatn', provider: GoogleFontProvider::class)
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->pages([
                Dashboard::class,
            ])
            ->navigationItems($this->navigationItems())
            ->userMenuItems($this->userMenuItems())
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, fn (): View => view('filament.locale-switcher'))
            ->renderHook(PanelsRenderHook::SIMPLE_LAYOUT_START, fn (): View => view('filament.locale-switcher', ['floating' => true]))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                SetLocale::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Extra sidebar entries: the settings page (Filament's profile page).
     *
     * @return array<int, NavigationItem>
     */
    protected function navigationItems(): array
    {
        return [
            NavigationItem::make('settings')
                ->label(fn (): string => __('habits.nav.settings'))
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->group(NavigationGroup::Manage)
                ->sort(10)
                ->url(fn (): string => EditProfile::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs(EditProfile::getRouteName())),
        ];
    }

    /**
     * Extra entries for the user menu: a link back to the landing page.
     *
     * @return array<int, Action>
     */
    protected function userMenuItems(): array
    {
        return [
            Action::make('home')
                ->label(fn (): string => __('habits.nav.landing'))
                ->icon('heroicon-o-home')
                ->url(fn (): string => route('home')),
        ];
    }
}
