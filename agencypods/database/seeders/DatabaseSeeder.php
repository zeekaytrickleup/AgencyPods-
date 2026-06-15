<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Goal;
use App\Models\Pod;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users -------------------------------------------------------
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@agencypods.test',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        $manager1 = User::create([
            'name' => 'Maya Manager',
            'email' => 'manager1@agencypods.test',
            'password' => Hash::make('password'),
            'role' => 'pod_manager',
        ]);

        $manager2 = User::create([
            'name' => 'Marco Manager',
            'email' => 'manager2@agencypods.test',
            'password' => Hash::make('password'),
            'role' => 'pod_manager',
        ]);

        // --- Pods (manager1 owns several: Crimson + Cobalt) --------------
        $crimson = Pod::create(['name' => 'Crimson', 'color' => '#C8102E', 'manager_id' => $manager1->id]);
        $ember   = Pod::create(['name' => 'Ember',   'color' => '#E2571E', 'manager_id' => $manager2->id]);
        $cobalt  = Pod::create(['name' => 'Cobalt',  'color' => '#2A5BD7', 'manager_id' => $manager1->id]);
        $dusk    = Pod::create(['name' => 'Dusk',    'color' => '#6E5DA8', 'manager_id' => $manager2->id]);

        // --- Clients, goals, sections, attachments -----------------------
        $bloom = $this->client($crimson, 'Bloom Bakery', 'F&B');
        $core  = $this->client($crimson, 'CoreFit Gym', 'Health');
        $lex   = $this->client($ember, 'LexLaw Solicitors', 'Legal');

        $thisMonth = now()->startOfMonth()->toDateString();
        $lastMonth = now()->subMonthNoOverflow()->startOfMonth()->toDateString();

        $this->goal($bloom, 'Website redesign', $thisMonth, [
            'goal' => ['Redesign the full website with a new brand identity. Deliver responsive layout with booking integration and updated menu pages.',
                [['brief_v2.pdf', '340 KB', 'pdf'], ['brand_assets.zip', '12 MB', 'doc']]],
            'stop' => ['Do not change existing logo or brand colours. Avoid adding third-party chat widgets.', []],
            'start' => ['Kick off discovery call with client. Begin wireframes for homepage and menu page.',
                [['wireframe_draft.fig', '2.1 MB', 'doc']]],
            'continue' => ['Monitor uptime weekly. Check contact form submissions every Monday.',
                [['uptime_report_may.pdf', '180 KB', 'pdf']]],
        ]);

        $this->goal($bloom, 'Google Ads campaign', $lastMonth, [
            'goal' => ['Run targeted Google Ads for summer promotions. Target radius 10km around store location.',
                [['ads_strategy.pdf', '520 KB', 'pdf']]],
            'stop' => ['Do not run ads on competitor brand terms. Stay within £500/month budget.', []],
            'start' => ['Set up conversion tracking. Create 3 ad variations for A/B testing.',
                [['ad_copy_v1.docx', '45 KB', 'doc']]],
            'continue' => ['Review ad performance every Friday. Send client report on 1st of each month.',
                [['june_report.xlsx', '210 KB', 'xls'], ['ad_screenshot.png', '890 KB', 'img']]],
        ]);

        $this->goal($core, 'SEO growth plan', $thisMonth, [
            'goal' => ['Improve organic rankings for 10 target keywords in 90 days. Focus on local SEO.',
                [['keyword_research.xlsx', '320 KB', 'xls']]],
            'stop' => ['Do not build low-quality backlinks. Avoid keyword stuffing in content.', []],
            'start' => ['Complete full site audit. Submit updated sitemap to Google Search Console.',
                [['site_audit.pdf', '1.2 MB', 'pdf']]],
            'continue' => ['Track keyword positions weekly. Monitor Google My Business reviews.', []],
        ]);

        $this->goal($lex, 'Brand identity refresh', $lastMonth, [
            'goal' => ['Modernise brand identity including logo, colour palette, and typography. Deliver brand guidelines document.',
                [['brand_guidelines_draft.pdf', '4.5 MB', 'pdf']]],
            'stop' => ['Do not make the brand look startup-casual — must remain professional and trustworthy.', []],
            'start' => ['Present 3 mood boards to client. Get sign-off on direction before design begins.',
                [['moodboard_options.pdf', '8.2 MB', 'pdf'], ['reference_logos.png', '1.1 MB', 'img']]],
            'continue' => ['Review brand consistency across all client touchpoints monthly.', []],
        ]);

        // --- Weekly tasks (client-linked, current week) ------------------
        $week = now()->startOfWeek()->toDateString();
        $this->task($bloom, 'Create new landing page for summer menu', 'done', $week);
        $this->task($core, 'Fix broken contact form on mobile', 'done', $week);
        $this->task($lex, 'SEO audit — identify top 5 issues', 'pending', $week);
        $this->task($bloom, 'Monthly ads performance report', 'pending', $week);
        $this->task($core, 'Update Google My Business photos', 'done', $week);
    }

    private function client(Pod $pod, string $name, string $industry): Client
    {
        return $pod->clients()->create(['name' => $name, 'industry' => $industry]);
    }

    /**
     * @param  array<string, array{0:string, 1:array<int, array{0:string,1:string,2:string}>}>  $sections
     */
    private function goal(Client $client, string $title, string $created, array $sections): void
    {
        $goal = $client->goals()->create(['title' => $title]);
        $goal->created_at = $created;
        $goal->save();

        foreach (Goal::SECTION_TYPES as $type) {
            [$text, $files] = $sections[$type];
            $section = $goal->sections()->create(['type' => $type, 'content' => $text]);

            foreach ($files as [$name, $size, $fileType]) {
                $section->attachments()->create([
                    'original_name' => $name,
                    // Seed metadata only — real bytes are uploaded in Phase 4.
                    'stored_path' => '',
                    'size' => $this->bytes($size),
                    'mime' => null,
                    'file_type' => $fileType,
                ]);
            }
        }
    }

    private function task(Client $client, string $task, string $status, string $week): void
    {
        $client->weeklyTasks()->create([
            'task' => $task,
            'status' => $status,
            'week_start' => $week,
        ]);
    }

    /** Convert a display size like "2.1 MB" / "340 KB" to bytes. */
    private function bytes(string $display): int
    {
        [$value, $unit] = explode(' ', $display);
        $multiplier = strtoupper($unit) === 'MB' ? 1024 * 1024 : 1024;

        return (int) round((float) $value * $multiplier);
    }
}
