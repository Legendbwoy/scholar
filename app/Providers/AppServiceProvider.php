<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('Helpers/settings.php'))) {
            require_once app_path('Helpers/settings.php');
        }
        spl_autoload_register(function ($class) {
            $prefixes = [
                'PhpOffice\\PhpSpreadsheet\\' => base_path('vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/'),
                'ZipStream\\' => base_path('vendor/maennchen/zipstream-php/src/'),
                'Matrix\\' => base_path('vendor/markbaker/matrix/classes/src/'),
                'Complex\\' => base_path('vendor/markbaker/complex/classes/src/'),
                'Composer\\Pcre\\' => base_path('vendor/composer/pcre/src/'),
            ];

            foreach ($prefixes as $prefix => $baseDir) {
                if (str_starts_with($class, $prefix)) {
                    $relativeClass = substr($class, strlen($prefix));
                    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
                    if (file_exists($file)) {
                        require_once $file;
                        return;
                    }
                }
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        view()->composer('layouts.portal', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $recentAlerts = \App\Models\BroadcastMessage::query()
                    ->where(function ($q) use ($user) {
                        $q->where('target_audience', 'all');
                        if ($user->isTeacher()) {
                            $q->orWhere('target_audience', 'teachers');
                        } elseif ($user->isStudent()) {
                            $q->orWhere('target_audience', 'students');
                            if ($user->student && $user->student->school_class_id) {
                                $q->orWhere(function ($sub) use ($user) {
                                    $sub->where('target_audience', 'class')
                                        ->where('school_class_id', $user->student->school_class_id);
                                });
                            }
                        } elseif ($user->isParent()) {
                            $q->orWhere('target_audience', 'parents');
                        }
                    })
                    ->latest()
                    ->take(5)
                    ->get();

                $view->with('recentAlerts', $recentAlerts);
            } else {
                $view->with('recentAlerts', collect());
            }
        });
    }
}