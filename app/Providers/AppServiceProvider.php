<?php
namespace App\Providers;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider {
    public function register(): void {}
    public function boot(): void {
        // Restyled pagination (the framework's Tailwind default renders huge
        // unstyled arrows in this non-Tailwind app).
        Paginator::defaultView('pagination::custom');
    }
}
