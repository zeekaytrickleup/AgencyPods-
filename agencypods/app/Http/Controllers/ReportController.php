<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Pod;
use App\Models\WeeklyTask;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // Routes are gated by the `role:super_admin` middleware.
    public function index(Request $request)
    {
        return view('pods.report', $this->reportData($request));
    }

    public function downloadPdf(Request $request)
    {
        $pdf = Pdf::loadView('pods.pdf.report', $this->reportData($request) + ['generatedAt' => now()]);

        return $pdf->download('trickleup-'.$this->periodSlug($request).'-report.pdf');
    }

    public function downloadPodPdf(Request $request, Pod $pod)
    {
        $period = $this->resolvePeriod($request);
        $pod->load(['manager', 'clients.goals.sections']);

        $tasks = WeeklyTask::whereHas('client', fn ($q) => $q->where('pod_id', $pod->id))
            ->whereBetween('week_start', [$period['start']->toDateString(), $period['end']->toDateString()]);

        $pdf = Pdf::loadView('pods.pdf.pod', [
            'pod' => $pod,
            'period' => $period,
            'weeklyTotal' => (clone $tasks)->count(),
            'weeklyDone' => (clone $tasks)->where('status', 'done')->count(),
            'generatedAt' => now(),
        ]);

        return $pdf->download('pod-'.str($pod->name)->slug().'-'.$this->periodSlug($request).'.pdf');
    }

    /**
     * Resolve the selected reporting window from the request.
     * period = week|month, anchored on an optional `date` (defaults to the
     * most recent week that has tasks).
     */
    private function resolvePeriod(Request $request): array
    {
        $type = in_array($request->query('period'), ['week', 'month'], true)
            ? $request->query('period') : 'month';

        // Default to the current period; the user can page back/forward.
        $ref = $request->filled('date')
            ? Carbon::parse($request->query('date'))
            : Carbon::now();

        if ($type === 'week') {
            $start = $ref->copy()->startOfWeek();
            $end = $ref->copy()->endOfWeek();
            $label = 'Week of '.$start->format('j M Y');
            $prev = $start->copy()->subWeek();
            $next = $start->copy()->addWeek();
        } else {
            $start = $ref->copy()->startOfMonth();
            $end = $ref->copy()->endOfMonth();
            $label = $start->format('F Y');
            $prev = $start->copy()->subMonthNoOverflow();
            $next = $start->copy()->addMonthNoOverflow();
        }

        return [
            'type' => $type,
            'ref' => $ref,
            'refDate' => $start->toDateString(),
            'start' => $start,
            'end' => $end,
            'label' => $label,
            'prevDate' => $prev->toDateString(),
            'nextDate' => $next->toDateString(),
        ];
    }

    private function reportData(Request $request): array
    {
        $period = $this->resolvePeriod($request);
        [$from, $to] = [$period['start'], $period['end']];

        $pods = Pod::with(['clients.goals', 'manager'])->orderBy('id')->get();

        $tasksInPeriod = WeeklyTask::whereBetween('week_start', [$from->toDateString(), $to->toDateString()]);

        $podStats = $pods->map(fn ($pod) => [
            'pod' => $pod,
            'clients' => $pod->clients->count(),
            'goals' => $pod->clients->sum(fn ($c) => $c->goals->count()),
            'tasksDone' => WeeklyTask::whereHas('client', fn ($q) => $q->where('pod_id', $pod->id))
                ->whereBetween('week_start', [$from->toDateString(), $to->toDateString()])
                ->where('status', 'done')->count(),
            'tasksTotal' => WeeklyTask::whereHas('client', fn ($q) => $q->where('pod_id', $pod->id))
                ->whereBetween('week_start', [$from->toDateString(), $to->toDateString()])->count(),
        ]);

        return [
            'period' => $period,
            'pods' => $pods,
            'podStats' => $podStats,
            'totalPods' => $pods->count(),
            'totalClients' => $pods->sum(fn ($p) => $p->clients->count()),
            'goalsCreated' => Goal::whereBetween('created_at', [$from, $to])->count(),
            'weeklyTotal' => (clone $tasksInPeriod)->count(),
            'weeklyDone' => (clone $tasksInPeriod)->where('status', 'done')->count(),
        ];
    }

    private function periodSlug(Request $request): string
    {
        $period = $this->resolvePeriod($request);

        return $period['type'] === 'week'
            ? 'week-'.$period['start']->format('Y-m-d')
            : 'month-'.$period['start']->format('Y-m');
    }
}
