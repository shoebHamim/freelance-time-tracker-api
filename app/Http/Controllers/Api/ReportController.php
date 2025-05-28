<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeLog;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function getTotalHoursByProject(Request $request)
    {
        $projectId = $request->input('project_id');
        $timeLogs = TimeLog::whereHas('project.client', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->where('project_id', $projectId)->sum('hours');

        return response()->json(['total_hours' => $timeLogs]);
    }

    public function getTotalHoursByDay(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $timeLogs = TimeLog::whereHas('project.client', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->whereDate('start_time', $date)->sum('hours');

        return response()->json(['total_hours' => $timeLogs]);
    }

    public function getTotalHoursByClient(Request $request)
    {
        $clientId = $request->input('client_id');
        $from = $request->input('from', now()->startOfWeek()->toDateString());
        $to = $request->input('to', now()->endOfWeek()->toDateString());

        $timeLogs = TimeLog::whereHas('project.client', function ($query) use ($request, $clientId) {
            $query->where('user_id', $request->user()->id)->where('id', $clientId);
        })->whereBetween('start_time', [$from, $to])->sum('hours');

        return response()->json(['total_hours' => $timeLogs]);
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $clientId = $request->input('client_id');
        $from = $request->input('from');
        $to = $request->input('to');

        $client = Client::findOrFail($clientId);
        if ($client->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $timeLogs = TimeLog::whereHas('project', function ($query) use ($clientId) {
            $query->where('client_id', $clientId);
        })->whereBetween('start_time', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->with(['project' => function ($query) {
                $query->select('id', 'title', 'client_id');
            }])
            ->get();


        $totalHours = $timeLogs->sum('hours') ?? 0;
        $totalLogs = $timeLogs->count();


        $projectSummary = $timeLogs->groupBy('project_id')->map(function ($logs, $projectId) {
            $project = $logs->first()->project;
            return [
                'project_id' => $projectId,
                'project_title' => $project->title,
                'total_hours' => $logs->sum('hours') ?? 0,
                'total_logs' => $logs->count(),
                'logs' => $logs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'start_time' => $log->start_time,
                        'end_time' => $log->end_time,
                        'hours' => $log->hours,
                        'description' => $log->description,
                    ];
                })
            ];
        })->values();


        $dailySummary = $timeLogs->groupBy(function ($log) {
            return Carbon::parse($log->start_time)->toDateString();
        })->map(function ($logs, $date) {
            return [
                'date' => $date,
                'total_hours' => $logs->sum('hours') ?? 0,
                'total_logs' => $logs->count(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                ],
                'period' => [
                    'from' => $from,
                    'to' => $to,
                ],
                'summary' => [
                    'total_hours' => $totalHours,
                    'total_logs' => $totalLogs,
                    'average_hours_per_day' => $totalLogs > 0 ? round($totalHours / max(1, Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1), 2) : 0,
                ],
                'project_breakdown' => $projectSummary,
                'daily_breakdown' => $dailySummary,
                'detailed_logs' => $timeLogs->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'project_title' => $log->project->title,
                        'start_time' => $log->start_time,
                        'end_time' => $log->end_time,
                        'hours' => $log->hours,
                        'description' => $log->description,
                    ];
                })
            ]
        ]);
    }
}
