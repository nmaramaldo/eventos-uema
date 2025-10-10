<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user', 'model');

        // filtros
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25);

        $modelTypes = AuditLog::select('model_type')->distinct()->pluck('model_type');
        $actions = AuditLog::select('action')->distinct()->pluck('action');

        return view('audit-logs.index', compact('logs', 'modelTypes', 'actions'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'model']);

        return view('audit-logs.show', compact('auditLog'));
    }

    public function forModel(Request $request, $modelType, $modelId = null)
    {
        $query = AuditLog::with('user')
            ->forModel($modelType, $modelId);

        $logs = $query->orderBy('created_at', 'desc')->paginate(25);

        return view('audit-logs.model', compact('logs', 'modelType', 'modelId'));
    }
}