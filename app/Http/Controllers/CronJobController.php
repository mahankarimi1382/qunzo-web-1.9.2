<?php

namespace App\Http\Controllers;

use Addons\P2PTrading\Services\OrderService;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\User;
use App\Traits\NotifyTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Remotelywork\Installer\Repository\App;

class CronJobController extends Controller
{
    use NotifyTrait;

    public function runCronJobs()
    {
        $action_id = request('run_action');

        // Get running cron jobs
        if (is_null($action_id)) {
            $jobs = CronJob::where('status', 'running')
                ->where('next_run_at', '<', now())
                ->get();
        } else {
            $jobs = CronJob::whereKey($action_id)->get();
        }

        foreach ($jobs as $job) {

            $error = null;

            $log = new CronJobLog;
            $log->cron_job_id = $job->id;
            $log->started_at = now();

            try {

                if ($job->type == 'system') {
                    $this->{$job->reserved_method}();
                } else {
                    Http::withOptions([
                        'verify' => false,
                    ])->get($job->url);
                }
            } catch (\Throwable $th) {
                $error = $th->getMessage();
            }

            $log->ended_at = now();
            $log->error = $error;
            $log->save();

            $job->update([
                'last_run_at' => now(),
                'next_run_at' => now()->addSeconds($job->schedule),
            ]);
        }

        if ($action_id !== null) {
            notify()->success(__('Cron running successfully!'), 'Success');

            return back();
        }
    }

    public function userInactive()
    {
        if (! setting('inactive_account_disabled', 'inactive_user') == 1) {
            return false;
        }

        try {

            DB::beginTransaction();
            $this->startCron();

            User::whereDoesntHave('activities', function ($query) {
                $query->where('created_at', '>', now()->subDays(30));
            })->where('status', 1)->chunk(500, function ($inactiveUsers) {
                foreach ($inactiveUsers as $user) {
                    $user->update(['status' => 0]);
                }
            });

            DB::commit();

            return '........Inactive users disabled successfully.';
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    protected function startCron()
    {
        if (! App::initApp()) {
            return false;
        }
    }

    public function p2pOrderTimeouts()
    {
        if (! addonActive('p2p-trading')) {
            return false;
        }

        $result = (new OrderService)->processTimeouts();

        return 'P2P orders processed. Expired: ' . $result['expired'] . ', Disputed: ' . $result['disputed'];
    }
}
