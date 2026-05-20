<?php

namespace App\Providers;

use App\Repositories\Billing\Contracts\TemporaryBillRepositoryInterface;
use App\Repositories\Billing\TemporaryBillRepository;
use App\Services\Billing\InvoiceService;
use App\Services\Billing\TemporaryBillService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ====================== Repository Bindings ======================
        $this->app->bind(
            TemporaryBillRepositoryInterface::class,
            TemporaryBillRepository::class
        );

        // InvoiceRepository পরে যোগ করলে এখানে বাইন্ড করবে
        // $this->app->bind(
        //     InvoiceRepositoryInterface::class,
        //     InvoiceRepository::class
        // );

        // ====================== Service Bindings ======================
        $this->app->singleton(InvoiceService::class);
        $this->app->singleton(TemporaryBillService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}