<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Client;
use App\Models\Goal;
use App\Models\GoalSection;
use App\Models\Pod;

trait InteractsWithPods
{
    /** Pods the current user is allowed to see, in display order. */
    protected function visiblePods()
    {
        return Pod::visibleTo(request()->user())->orderBy('id')->get();
    }

    /** Abort with 403 unless the current user may access this pod. */
    protected function authorizePod(Pod $pod): void
    {
        abort_unless(request()->user()->canAccessPod($pod), 403);
    }

    protected function authorizeClient(Client $client): void
    {
        $this->authorizePod($client->pod);
    }

    protected function authorizeGoal(Goal $goal): void
    {
        $this->authorizeClient($goal->client);
    }

    protected function authorizeSection(GoalSection $section): void
    {
        $this->authorizeGoal($section->goal);
    }
}
