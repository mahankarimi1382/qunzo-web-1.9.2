<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CronJob;
use App\Models\CronJobLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CronJobController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:manage-cron-job', ['only' => ['index']]),
            new Middleware('permission:cron-job-create', ['only' => ['store']]),
            new Middleware('permission:cron-job-edit', ['only' => ['update']]),
            new Middleware('permission:cron-job-delete', ['only' => ['destroy']]),
            new Middleware('permission:cron-job-logs', ['only' => ['logs']]),
            new Middleware('permission:cron-job-run', ['only' => ['runNow']]),
        ];
    }

    public function index()
    {
        $jobs = CronJob::latest()->paginate();

        return view('backend.cron-jobs.index', ['jobs' => $jobs]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'schedule' => 'required',
            'next_run_at' => 'required|date',
            'url' => 'required',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            CronJob::create([
                'name' => $request->name,
                'schedule' => $request->integer('schedule'),
                'next_run_at' => Carbon::parse($request->next_run_at)->toDateTimeString(),
                'url' => $request->url,
                'type' => 'custom',
                'status' => $request->status,
            ]);

            $status = 'success';
            $message = __('Cron Job added successfully!');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'schedule' => 'required',
            'next_run_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            notify()->error($validator->errors()->first());

            return back();
        }

        try {
            CronJob::updateOrCreate([
                'id' => $request->id,
            ], [
                'name' => $request->name,
                'schedule' => $request->integer('schedule'),
                'next_run_at' => Carbon::parse($request->next_run_at)->toDateTimeString(),
                'url' => $request->url,
                'status' => $request->status,
            ]);

            $status = 'success';
            $message = __('Cron Job updated successfully!');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function delete($id)
    {
        $cron = CronJob::findOrFail(decrypt($id));

        if ($cron->type == 'system') {
            notify()->error(__("You can't delete system cron job!"));

            return back();
        }

        try {
            $cron->delete();
            $cron->logs()->delete();

            $status = 'success';
            $message = __('Cron Job deleted successfully!');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function runNow($id)
    {
        try {
            $cron = CronJob::find(decrypt($id));

            $response = $this->sendRequestToUrl(url($cron->url));

            if ($response['status'] == 'success') {
                notify()->success(__('Cron run successfully!'));

                return back();
            }

            $status = 'warning';
            $message = __('Cron run failed!');
        } catch (\Exception $exception) {
            $status = 'warning';
            $message = __('something is wrong: ').$exception->getMessage();
        }

        notify()->$status($message, $status);

        return back();
    }

    public function logs($id)
    {
        $logs = CronJobLog::where('cron_job_id', decrypt($id))->paginate();

        return view('backend.cron-jobs.logs', ['logs' => $logs, 'id' => $id]);
    }

    public function clearLogs($id)
    {
        CronJobLog::where('cron_job_id', decrypt($id))->delete();

        notify()->success(__('Cron logs cleared successfully!'));

        return to_route('admin.cron.jobs.index');
    }

    protected function sendRequestToUrl($url)
    {
        try {

            Http::withOptions([
                'verify' => false,
            ])->get($url);

            return [
                'status' => 'success',
            ];
        } catch (\Throwable $throwable) {

            return [
                'status' => 'error',
            ];
        }
    }
}
