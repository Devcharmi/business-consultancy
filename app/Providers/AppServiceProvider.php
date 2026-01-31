<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\TaskCommitment;
use App\Models\TaskDeliverable;
use App\Observers\TaskCommitmentObserver;
use App\Observers\TaskDeliverableObserver;
use App\Observers\TaskObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        TaskCommitment::observe(TaskCommitmentObserver::class);
        TaskDeliverable::observe(TaskDeliverableObserver::class);
    }
}
