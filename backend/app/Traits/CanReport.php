<?php

namespace App\Traits;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait CanReport
{
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function reportUsers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'reportable', 'reports');
    }

    public function createReport(User $user, string $content): Report
    {
        $report = $this->reports()->make(['content' => $content]);
        $report->user()->associate($user);
        $report->save();

        return $report;
    }
}
