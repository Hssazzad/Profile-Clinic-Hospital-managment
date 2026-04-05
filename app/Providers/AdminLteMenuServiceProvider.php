<?php

namespace App\Providers;

use App\Models\ParentMenu;
use App\Models\SubMenu;
use App\Models\UserMenu;
use App\Models\UserSubMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AdminLteMenuServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Skip when running CLI (e.g. artisan) where Auth may be null
            if (app()->runningInConsole()) {
                return;
            }

            // --- Identify the current user ---
            $user = Auth::user();
            // If you have userpin column on users, prefer that; else fallback to session() or id()
            $userpin = $user->userpin ?? session('userpin') ?? Auth::id();

            // Not logged in: show minimal menu (or do nothing)
            if (!$userpin) {
                $event->menu->add(
                    ['header' => 'MAIN NAVIGATION'],
                    ['text' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fas fa-tachometer-alt'],
                );
                return;
            }

            // --- Build & cache menu per userpin ---
            $items = Cache::remember("adminlte.menu.userpin.$userpin", 300, function () use ($userpin) {
                // Read allowed parent codes for this user (supports comma-separated)
                $parentCodes = UserMenu::where('userpin', $userpin)
                    ->orderBy('position')
                    ->pluck('menuparentcode')
                    ->flatMap(function ($raw) {
                        return collect(explode(',', (string) $raw))
                            ->map(fn($v) => trim($v))
                            ->filter(fn($v) => $v !== '');
                    })
                    ->map(fn($v) => (string)$v)
                    ->unique()
                    ->values();

                if ($parentCodes->isEmpty()) {
                    return [];
                }

                // Load parent rows and keep the same order as $parentCodes
                $parents = ParentMenu::whereIn('parentcode', $parentCodes->map(fn($c)=>(int)$c)->all())
                    ->get()
                    ->sortBy(fn ($p) => $parentCodes->search((string)$p->parentcode));

                // Group allowed submenus (by parent) for this user
                $userSubs = UserSubMenu::where('userpin', $userpin)
                    ->whereIn('menuparentcode', $parentCodes->all())
                    ->orderBy('position')
                    ->get()
                    ->groupBy(fn($row) => (string)$row->menuparentcode);

                $built = [];

                foreach ($parents as $parent) {
                    $pcode = (string) $parent->parentcode;

                    // Allowed submenucodes under this parent for this user
                    $allowedSubCodes = collect($userSubs->get($pcode, collect()))
                        ->pluck('submenucode')
                        ->map(fn($v) => (string)$v)
                        ->values();

                    if ($allowedSubCodes->isEmpty()) {
                        // No permitted submenu for this parent; skip the parent
                        continue;
                    }

                    // Fetch submenu rows for those codes, for this parent
                    $subs = SubMenu::where('menuparentcode', (int)$parent->parentcode)
                        ->whereIn('submenucode', $allowedSubCodes->map(fn($c)=>(int)$c)->all())
                        ->get()
                        ->sortBy(fn ($sm) => $allowedSubCodes->search((string)$sm->submenucode));

                    // Map submenu rows to AdminLTE items
                    $submenuItems = $subs->map(function (SubMenu $sm) use ($parent) {
                        // Prefer a named route (e.g., 'subject.create'), else fallback to /Controller/method
                        $routeName = $this->guessRouteName($parent->mainroute, $sm->method);
                        if ($routeName && Route::has($routeName)) {
                            return [
                                'text'  => $sm->submenuname,
                                'route' => $routeName,
                                'icon'  => 'far fa-circle',
                            ];
                        }

                        return [
                            'text' => $sm->submenuname,
                            'url'  => url($this->guessUrlPath($parent->mainroute, $sm->method)),
                            'icon' => 'far fa-circle',
                        ];
                    })->values()->all();

                    // Parent node
                    $built[] = [
                        'text'    => $parent->parentname,
                        'icon'    => 'fas fa-folder', // optional: add an icon column in parentmenu later
                        'submenu' => $submenuItems,
                    ];
                }

                return $built;
            });

            // Add a header + dashboard, then append DB-driven items
            $event->menu->add(
                ['header' => 'MAIN NAVIGATION'],
                ['text' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fas fa-tachometer-alt'],
                ...$items
            );
        });
    }

    private function guessRouteName(string $controller, string $method): ?string
    {
        // Heuristic: Subject/create -> subject.create
        $c = strtolower(preg_replace('/Controller$/', '', $controller));
        $m = strtolower($method);
        $map = ['list' => 'index', 'add' => 'create', 'new' => 'create'];
        $m = $map[$m] ?? $m;
        return "$c.$m"; // Only works if such named routes actually exist
    }

    private function guessUrlPath(string $controller, string $method): string
    {
        // Fallback to CI-style URL: /Controller/method
        return trim($controller, '/').'/'.trim($method, '/');
    }
}
