<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeLog;
use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        $timeLogs = TimeLog::whereHas('project.client', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->get();

        return response()->json([
            'success' => true,
            'data' => $timeLogs
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'description' => 'nullable|string',
        ]);

        $project = Project::findOrFail($request->project_id);
        if ($project->client->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $data = $request->only(['project_id', 'start_time', 'end_time', 'description']);

        // both start_time and end_time are provided
        if ($request->filled('end_time')) {
            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);
            $data['hours'] = $endTime->diffInHours($startTime, true); 
        }

        $timeLog = TimeLog::create($data);

        return response()->json([
            'success' => true,
            'data' => $timeLog,
            'message' => 'Time log created successfully'
        ], 201);
    }

    public function show(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->project->client->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $timeLog
        ]);
    }

    public function update(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->project->client->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'description' => 'nullable|string',
        ]);

        $timeLog->update($request->only(['start_time', 'end_time', 'description']));

        return response()->json([
            'success' => true,
            'data' => $timeLog,
            'message' => 'Time log updated successfully'
        ]);
    }

    public function destroy(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->project->client->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $timeLog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Time log deleted successfully'
        ]);
    }

    public function startTimeLog(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $project = Project::findOrFail($request->project_id);
        if ($project->client->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $timeLog = TimeLog::create([
            'project_id' => $request->project_id,
            'start_time' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $timeLog,
            'message' => 'Time log started successfully'
        ], 201);
    }

    public function endTimeLog(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->project->client->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $timeLog->update([
            'end_time' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $timeLog,
            'message' => 'Time log ended successfully'
        ]);
    }

    public function getTimeLogsByDay(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $timeLogs = TimeLog::whereHas('project.client', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->whereDate('start_time', $date)->get();

        return response()->json([
            'success' => true,
            'data' => $timeLogs
        ]);
    }

    public function getTimeLogsByWeek(Request $request)
    {
        $startOfWeek = $request->input('start_of_week', now()->startOfWeek()->toDateString());
        $endOfWeek = $request->input('end_of_week', now()->endOfWeek()->toDateString());

        $timeLogs = TimeLog::whereHas('project.client', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->whereBetween('start_time', [$startOfWeek, $endOfWeek])->get();

        return response()->json([
            'success' => true,
            'data' => $timeLogs
        ]);
    }
}
