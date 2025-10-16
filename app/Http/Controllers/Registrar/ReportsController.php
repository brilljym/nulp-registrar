<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\StudentRequest;
use App\Models\StudentRequestItem;
use App\Models\Document;
use App\Models\OnsiteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        // Get current registrar's performance data
        $registrarId = Auth::id();
        
        // Document Request Statistics (for registrar's assigned requests)
        $totalRequests = StudentRequest::count();
        $pendingRequests = StudentRequest::where('status', 'pending')->count();
        $processingRequests = StudentRequest::where('status', 'processing')->count();
        $readyRequests = StudentRequest::where('status', 'ready_for_release')->count();
        $completedRequests = StudentRequest::where('status', 'completed')->count();
        
        // Onsite Request Statistics (registrar-specific)
        $onsiteRequests = OnsiteRequest::where('assigned_registrar_id', $registrarId)->count();
        $onsiteCompleted = OnsiteRequest::where('assigned_registrar_id', $registrarId)
            ->where('status', 'completed')->count();
        $onsitePending = OnsiteRequest::where('assigned_registrar_id', $registrarId)
            ->where('status', 'pending')->count();
        
        // Performance metrics for this registrar
        $myProcessingTime = $this->getMyProcessingTime($registrarId);
        $myProductivity = $this->getMyProductivity($registrarId);
        
        // Document type statistics
        $documentStats = $this->getDocumentTypeStats();
        
        return view('registrar.reports', compact(
            'totalRequests', 'pendingRequests', 'processingRequests', 'readyRequests', 'completedRequests',
            'onsiteRequests', 'onsiteCompleted', 'onsitePending',
            'myProcessingTime', 'myProductivity', 'documentStats'
        ));
    }

    private function getMyProcessingTime($registrarId)
    {
        $completedRequests = OnsiteRequest::where('assigned_registrar_id', $registrarId)
            ->where('status', 'completed')
            ->whereNotNull('updated_at')
            ->whereNotNull('created_at')
            ->get();
        
        if ($completedRequests->count() === 0) {
            return ['hours' => 0, 'minutes' => 0, 'formatted' => '0 minutes'];
        }
        
        $totalMinutes = 0;
        foreach ($completedRequests as $request) {
            $totalMinutes += $request->created_at->diffInMinutes($request->updated_at);
        }
        
        $avgMinutes = $totalMinutes / $completedRequests->count();
        $hours = floor($avgMinutes / 60);
        $minutes = $avgMinutes % 60;
        
        return [
            'hours' => $hours,
            'minutes' => round($minutes),
            'formatted' => $hours > 0 ? "{$hours} hours " . round($minutes) . " minutes" : round($minutes) . " minutes"
        ];
    }

    private function getMyProductivity($registrarId)
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'today' => OnsiteRequest::where('assigned_registrar_id', $registrarId)
                ->whereDate('updated_at', $today)
                ->where('status', 'completed')->count(),
            'this_week' => OnsiteRequest::where('assigned_registrar_id', $registrarId)
                ->where('updated_at', '>=', $thisWeek)
                ->where('status', 'completed')->count(),
            'this_month' => OnsiteRequest::where('assigned_registrar_id', $registrarId)
                ->where('updated_at', '>=', $thisMonth)
                ->where('status', 'completed')->count(),
            'total' => OnsiteRequest::where('assigned_registrar_id', $registrarId)
                ->where('status', 'completed')->count(),
        ];
    }

    private function getDocumentTypeStats()
    {
        return DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->select('documents.type_document', DB::raw('COUNT(*) as request_count'), DB::raw('SUM(student_request_items.quantity) as total_quantity'))
            ->groupBy('documents.id', 'documents.type_document')
            ->orderBy('request_count', 'desc')
            ->get();
    }
}