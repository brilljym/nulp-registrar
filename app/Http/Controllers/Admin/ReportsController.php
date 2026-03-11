<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Registrar;
use App\Models\Document;
use App\Models\StudentRequest;
use App\Models\StudentRequestItem;
use App\Models\OnsiteRequest;
use App\Models\QueueCounter;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ReportsController extends Controller
{
    public function index()
    {
        // User Statistics
        $totalUsers = User::count();
        $totalStudents = User::whereHas('role', function($q) {
            $q->where('name', 'student');
        })->count();
        $totalRegistrars = User::whereHas('role', function($q) {
            $q->where('name', 'registrar');
        })->count();
        $totalAdmins = User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->count();
        
        // Document Statistics
        $totalDocuments = Document::count();
        
        // Request Statistics
        $totalRequests = StudentRequest::count();
        $totalOnlineRequests = StudentRequest::count();
        $totalOnsiteRequests = OnsiteRequest::count();
        
        // Status Statistics
        $pendingRequests = StudentRequest::where('status', 'pending')->count();
        $processingRequests = StudentRequest::where('status', 'processing')->count();
        $readyRequests = StudentRequest::where('status', 'ready_for_release')->count();
        $completedRequests = StudentRequest::where('status', 'completed')->count();
        
        // Document Type Analytics - Number of requests per document type
        $documentTypeStats = DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->select('documents.type_document', DB::raw('COUNT(*) as request_count'), DB::raw('SUM(student_request_items.quantity) as total_quantity'))
            ->groupBy('documents.id', 'documents.type_document')
            ->orderBy('request_count', 'desc')
            ->get();
        
        // Processing Time Analytics
        $avgProcessingTime = $this->calculateAverageProcessingTime();
        $processingTimeStats = $this->getProcessingTimeBreakdown();
        
        // Queue Performance Metrics
        $queueStats = $this->getQueuePerformanceMetrics();
        
        // Monthly Trends
        $monthlyTrends = $this->getMonthlyTrends();
        
        // Registrar Performance
        $registrarPerformance = $this->getRegistrarPerformance();
        
        // Revenue Analytics
        $revenueStats = $this->getRevenueAnalytics();
        
        // Peak Hours Analysis
        $peakHoursData = $this->getPeakHoursAnalysis();
        
        // 🆕 Enhanced Analytics
        $chartData = $this->getChartData();
        $predictiveAnalytics = $this->getPredictiveAnalytics();
        $documentStatistics = $this->getDetailedDocumentStatistics();
        $trendAnalytics = $this->getTrendAnalytics();
        $performanceMetrics = $this->getPerformanceMetrics();
        
        return view('admin.reports', compact(
            'totalUsers', 'totalStudents', 'totalRegistrars', 'totalAdmins',
            'totalDocuments', 'totalRequests', 'totalOnlineRequests', 'totalOnsiteRequests',
            'pendingRequests', 'processingRequests', 'readyRequests', 'completedRequests',
            'documentTypeStats', 'avgProcessingTime', 'processingTimeStats',
            'queueStats', 'monthlyTrends', 'registrarPerformance', 'revenueStats', 'peakHoursData',
            'chartData', 'predictiveAnalytics', 'documentStatistics', 'trendAnalytics', 'performanceMetrics'
        ));
    }

    private function calculateAverageProcessingTime()
    {
        $completedRequests = StudentRequest::where('status', 'completed')
            ->whereNotNull('updated_at')
            ->whereNotNull('created_at')
            ->get();
        
        if ($completedRequests->count() === 0) {
            return ['hours' => 0, 'minutes' => 0, 'formatted' => '0 hours'];
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

    private function getProcessingTimeBreakdown()
    {
        return [
            'under_1_hour' => StudentRequest::where('status', 'completed')
                ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, updated_at) < 60')->count(),
            'under_4_hours' => StudentRequest::where('status', 'completed')
                ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, updated_at) BETWEEN 60 AND 240')->count(),
            'under_24_hours' => StudentRequest::where('status', 'completed')
                ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, updated_at) BETWEEN 240 AND 1440')->count(),
            'over_24_hours' => StudentRequest::where('status', 'completed')
                ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, updated_at) > 1440')->count(),
        ];
    }

    private function getQueuePerformanceMetrics()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'daily_processed' => StudentRequest::whereDate('updated_at', $today)
                ->where('status', 'completed')->count(),
            'weekly_processed' => StudentRequest::where('updated_at', '>=', $thisWeek)
                ->where('status', 'completed')->count(),
            'monthly_processed' => StudentRequest::where('updated_at', '>=', $thisMonth)
                ->where('status', 'completed')->count(),
            'current_queue_size' => StudentRequest::whereIn('status', ['pending', 'processing'])->count(),
        ];
    }

    private function getMonthlyTrends()
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $requests = StudentRequest::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)->count();
            
            $months->push([
                'month' => $date->format('M Y'),
                'requests' => $requests
            ]);
        }
        return $months;
    }

    private function getRegistrarPerformance()
    {
        // Since student_requests don't have assigned_registrar_id, we'll use onsite_requests instead
        return DB::table('onsite_requests')
            ->join('users', 'onsite_requests.assigned_registrar_id', '=', 'users.id')
            ->select(
                'users.first_name',
                'users.last_name',
                DB::raw('COUNT(*) as total_processed'),
                DB::raw('AVG(TIMESTAMPDIFF(MINUTE, onsite_requests.created_at, onsite_requests.updated_at)) as avg_processing_minutes')
            )
            ->where('onsite_requests.status', 'completed')
            ->whereNotNull('onsite_requests.assigned_registrar_id')
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderBy('total_processed', 'desc')
            ->limit(10)
            ->get();
    }

    private function getRevenueAnalytics()
    {
        $totalRevenue = StudentRequest::where('payment_confirmed', true)->sum('total_cost');
        $monthlyRevenue = StudentRequest::where('payment_confirmed', true)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_cost');
        
        $revenueByDocument = DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->where('student_requests.payment_confirmed', true)
            ->select('documents.type_document', DB::raw('SUM(student_request_items.price * student_request_items.quantity) as revenue'))
            ->groupBy('documents.type_document')
            ->orderBy('revenue', 'desc')
            ->get();
        
        return [
            'total' => $totalRevenue,
            'monthly' => $monthlyRevenue,
            'by_document' => $revenueByDocument
        ];
    }

    private function getPeakHoursAnalysis()
    {
        $hourlyData = collect();
        for ($hour = 8; $hour <= 17; $hour++) {
            $requests = StudentRequest::whereRaw('HOUR(created_at) = ?', [$hour])->count();
            $hourlyData->push([
                'hour' => Carbon::createFromTime($hour)->format('g A'),
                'requests' => $requests
            ]);
        }
        return $hourlyData;
    }

    public function export(Request $request)
    {
        $reportType = $request->get('type', 'summary');
        
        switch ($reportType) {
            case 'document_types':
                return $this->exportDocumentTypeReport();
            case 'processing_times':
                return $this->exportProcessingTimeReport();
            case 'queue_performance':
                return $this->exportQueuePerformanceReport();
            case 'registrar_performance':
                return $this->exportRegistrarPerformanceReport();
            case 'revenue':
                return $this->exportRevenueReport();
            case 'predictive':
                return $this->exportPredictiveReport();
            case 'trends':
                return $this->exportTrendsReport();
            case 'performance':
                return $this->exportPerformanceReport();
            case 'complete':
                return $this->exportCompletePackage();
            default:
                return $this->exportSummaryReport();
        }
    }

    private function exportSummaryReport()
    {
        $filename = 'system_summary_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['NU Lipa Registrar System - Summary Report']);
            fputcsv($file, ['Generated on: ' . Carbon::now()->format('F j, Y g:i A')]);
            fputcsv($file, []);
            
            // User Statistics
            fputcsv($file, ['USER STATISTICS']);
            fputcsv($file, ['Metric', 'Count']);
            fputcsv($file, ['Total Users', User::count()]);
            fputcsv($file, ['Students', User::whereHas('role', function($q) { $q->where('name', 'student'); })->count()]);
            fputcsv($file, ['Registrars', User::whereHas('role', function($q) { $q->where('name', 'registrar'); })->count()]);
            fputcsv($file, ['Administrators', User::whereHas('role', function($q) { $q->where('name', 'admin'); })->count()]);
            fputcsv($file, []);
            
            // Request Statistics
            fputcsv($file, ['REQUEST STATISTICS']);
            fputcsv($file, ['Total Requests', StudentRequest::count()]);
            fputcsv($file, ['Pending', StudentRequest::where('status', 'pending')->count()]);
            fputcsv($file, ['Processing', StudentRequest::where('status', 'processing')->count()]);
            fputcsv($file, ['Ready', StudentRequest::where('status', 'ready_for_release')->count()]);
            fputcsv($file, ['Completed', StudentRequest::where('status', 'completed')->count()]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportDocumentTypeReport()
    {
        $filename = 'document_type_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Document Type Report']);
            fputcsv($file, ['Generated on: ' . Carbon::now()->format('F j, Y g:i A')]);
            fputcsv($file, []);
            fputcsv($file, ['Document Type', 'Request Count', 'Total Quantity']);
            
            $documentTypeStats = DB::table('student_requests')
                ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
                ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
                ->select('documents.type_document', DB::raw('COUNT(*) as request_count'), DB::raw('SUM(student_request_items.quantity) as total_quantity'))
                ->groupBy('documents.id', 'documents.type_document')
                ->orderBy('request_count', 'desc')
                ->get();
            
            foreach ($documentTypeStats as $stat) {
                fputcsv($file, [
                    $stat->type_document,
                    $stat->request_count,
                    $stat->total_quantity
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportProcessingTimeReport()
    {
        $filename = 'processing_time_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Processing Time Analysis Report']);
            fputcsv($file, ['Generated on: ' . Carbon::now()->format('F j, Y g:i A')]);
            fputcsv($file, []);
            
            $avgTime = $this->calculateAverageProcessingTime();
            fputcsv($file, ['Average Processing Time: ' . $avgTime['formatted']]);
            fputcsv($file, []);
            
            fputcsv($file, ['Time Range', 'Number of Requests']);
            $breakdown = $this->getProcessingTimeBreakdown();
            fputcsv($file, ['Under 1 Hour', $breakdown['under_1_hour']]);
            fputcsv($file, ['1-4 Hours', $breakdown['under_4_hours']]);
            fputcsv($file, ['4-24 Hours', $breakdown['under_24_hours']]);
            fputcsv($file, ['Over 24 Hours', $breakdown['over_24_hours']]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportQueuePerformanceReport()
    {
        $filename = 'queue_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Queue Performance Report']);
            fputcsv($file, ['Generated on: ' . Carbon::now()->format('F j, Y g:i A')]);
            fputcsv($file, []);
            
            $queueStats = $this->getQueuePerformanceMetrics();
            fputcsv($file, ['Metric', 'Count']);
            fputcsv($file, ['Daily Processed', $queueStats['daily_processed']]);
            fputcsv($file, ['Weekly Processed', $queueStats['weekly_processed']]);
            fputcsv($file, ['Monthly Processed', $queueStats['monthly_processed']]);
            fputcsv($file, ['Current Queue Size', $queueStats['current_queue_size']]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportRegistrarPerformanceReport()
    {
        $filename = 'registrar_performance_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Registrar Performance Report']);
            fputcsv($file, ['Generated on: ' . Carbon::now()->format('F j, Y g:i A')]);
            fputcsv($file, []);
            fputcsv($file, ['Registrar Name', 'Total Processed', 'Avg Processing Time (minutes)']);
            
            $registrarPerformance = $this->getRegistrarPerformance();
            foreach ($registrarPerformance as $performance) {
                fputcsv($file, [
                    $performance->first_name . ' ' . $performance->last_name,
                    $performance->total_processed,
                    round($performance->avg_processing_minutes, 2)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportRevenueReport()
    {
        $filename = 'revenue_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Revenue Analysis Report']);
            fputcsv($file, ['Generated on: ' . Carbon::now()->format('F j, Y g:i A')]);
            fputcsv($file, []);
            
            $revenueStats = $this->getRevenueAnalytics();
            fputcsv($file, ['Total Revenue', number_format($revenueStats['total'], 2)]);
            fputcsv($file, ['Monthly Revenue', number_format($revenueStats['monthly'], 2)]);
            fputcsv($file, []);
            fputcsv($file, ['Revenue by Document Type']);
            fputcsv($file, ['Document Type', 'Revenue']);
            
            foreach ($revenueStats['by_document'] as $revenue) {
                fputcsv($file, [$revenue->type_document, number_format($revenue->revenue, 2)]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // 🆕 Enhanced Analytics Methods

    private function getChartData()
    {
        // Student Request Status Distribution
        $studentStatuses = StudentRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                return [
                    'label' => ucfirst(str_replace('_', ' ', $item->status)),
                    'value' => $item->count
                ];
            });

        // Onsite Request Status Distribution
        $onsiteStatuses = OnsiteRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                return [
                    'label' => ucfirst(str_replace('_', ' ', $item->status)),
                    'value' => $item->count
                ];
            });

        // Student Request Document Types
        $studentDocumentTypes = DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->select('documents.type_document as label', DB::raw('COUNT(*) as value'))
            ->groupBy('documents.id', 'documents.type_document')
            ->orderBy('value', 'desc')
            ->limit(10)
            ->get();

        // Onsite Request Document Types
        $onsiteDocumentTypes = DB::table('onsite_requests')
            ->join('onsite_request_items', 'onsite_requests.id', '=', 'onsite_request_items.onsite_request_id')
            ->join('documents', 'onsite_request_items.document_id', '=', 'documents.id')
            ->select('documents.type_document as label', DB::raw('COUNT(*) as value'))
            ->groupBy('documents.id', 'documents.type_document')
            ->orderBy('value', 'desc')
            ->limit(10)
            ->get();

        return [
            'studentStatuses' => $studentStatuses,
            'onsiteStatuses' => $onsiteStatuses,
            'studentDocumentTypes' => $studentDocumentTypes,
            'onsiteDocumentTypes' => $onsiteDocumentTypes
        ];
    }

    /**
     * Get Student Request Status Distribution for Charts
     */
    private function getStudentRequestDistribution()
    {
        return collect([
            ['label' => 'Pending', 'value' => StudentRequest::where('status', 'pending')->count()],
            ['label' => 'Processing', 'value' => StudentRequest::where('status', 'processing')->count()],
            ['label' => 'Ready', 'value' => StudentRequest::where('status', 'ready_for_release')->count()],
            ['label' => 'Completed', 'value' => StudentRequest::where('status', 'completed')->count()],
            ['label' => 'Rejected', 'value' => StudentRequest::where('status', 'rejected')->count()],
        ])->filter(function($item) { return $item['value'] > 0; });
    }

    /**
     * Get Onsite Request Status Distribution for Charts
     */
    private function getOnsiteRequestDistribution()
    {
        return collect([
            ['label' => 'Pending', 'value' => OnsiteRequest::where('status', 'pending')->count()],
            ['label' => 'Processing', 'value' => OnsiteRequest::where('status', 'processing')->count()],
            ['label' => 'Completed', 'value' => OnsiteRequest::where('status', 'completed')->count()],
            ['label' => 'Cancelled', 'value' => OnsiteRequest::where('status', 'cancelled')->count()],
        ])->filter(function($item) { return $item['value'] > 0; });
    }

    /**
     * Get Student Request Document Type Distribution
     */
    private function getStudentDocumentTypes()
    {
        return DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->select(
                'documents.type_document as label',
                DB::raw('COUNT(DISTINCT student_requests.id) as value'),
                DB::raw('SUM(student_request_items.quantity) as total_quantity')
            )
            ->groupBy('documents.type_document', 'documents.id')
            ->orderBy('value', 'desc')
            ->get();
    }

    /**
     * Get Onsite Request Document Type Distribution
     */
    private function getOnsiteDocumentTypes()
    {
        return DB::table('onsite_requests')
            ->join('onsite_request_items', 'onsite_requests.id', '=', 'onsite_request_items.onsite_request_id')
            ->join('documents', 'onsite_request_items.document_id', '=', 'documents.id')
            ->select(
                'documents.type_document as label',
                DB::raw('COUNT(DISTINCT onsite_requests.id) as value'),
                DB::raw('SUM(onsite_request_items.quantity) as total_quantity')
            )
            ->groupBy('documents.type_document', 'documents.id')
            ->orderBy('value', 'desc')
            ->get();
    }

    /**
     * Get predictive analytics for release time estimation
     */
    private function getPredictiveAnalytics()
    {
        // Calculate average processing times by document type
        $processingTimes = DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->where('student_requests.status', 'completed')
            ->select(
                'documents.type_document',
                'documents.id as document_id',
                DB::raw('AVG(TIMESTAMPDIFF(MINUTE, student_requests.created_at, student_requests.updated_at)) as avg_minutes'),
                DB::raw('COUNT(*) as sample_size')
            )
            ->groupBy('documents.id', 'documents.type_document')
            ->having('sample_size', '>=', 3) // Only include documents with sufficient data
            ->get();

        // Current queue analysis
        $currentQueue = StudentRequest::whereIn('status', ['pending', 'processing'])
            ->with(['requestItems.document'])
            ->get();

        $predictions = collect();
        foreach ($currentQueue as $request) {
            $estimatedMinutes = 0;
            $confidence = 'high';
            
            foreach ($request->requestItems as $item) {
                $docProcessingTime = $processingTimes->where('document_id', $item->document_id)->first();
                if ($docProcessingTime) {
                    $estimatedMinutes += $docProcessingTime->avg_minutes * $item->quantity;
                    if ($docProcessingTime->sample_size < 10) {
                        $confidence = 'medium';
                    }
                } else {
                    // Default estimate for documents without historical data
                    $estimatedMinutes += 120; // 2 hours default
                    $confidence = 'low';
                }
            }

            // Add queue position factor (assuming FIFO processing)
            $queuePosition = StudentRequest::where('status', 'pending')
                ->where('created_at', '<', $request->created_at)
                ->count() + 1;

            $estimatedReleaseTime = Carbon::now()->addMinutes($estimatedMinutes + ($queuePosition * 30));

            $predictions->push([
                'reference_no' => $request->reference_no,
                'estimated_release' => $estimatedReleaseTime->format('M j, Y g:i A'),
                'estimated_hours' => round($estimatedMinutes / 60, 1),
                'confidence' => $confidence,
                'queue_position' => $queuePosition
            ]);
        }

        // Summary statistics
        $totalPending = StudentRequest::where('status', 'pending')->count();
        $totalProcessing = StudentRequest::where('status', 'processing')->count();
        $avgProcessingHours = $processingTimes->avg('avg_minutes') / 60;

        return [
            'predictions' => $predictions->take(20), // Show top 20 predictions
            'summary' => [
                'total_pending' => $totalPending,
                'total_processing' => $totalProcessing,
                'avg_processing_hours' => round($avgProcessingHours, 1),
                'estimated_queue_clear_time' => Carbon::now()->addHours($totalPending * $avgProcessingHours)->format('M j, Y g:i A')
            ],
            'processing_times_by_document' => $processingTimes
        ];
    }

    /**
     * Get detailed document statistics
     */
    private function getDetailedDocumentStatistics()
    {
        // Most requested documents
        $topDocuments = DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->select(
                'documents.type_document',
                'documents.price',
                DB::raw('COUNT(student_request_items.id) as total_requests'),
                DB::raw('SUM(student_request_items.quantity) as total_quantity'),
                DB::raw('SUM(student_request_items.price * student_request_items.quantity) as total_revenue'),
                DB::raw('AVG(student_request_items.quantity) as avg_quantity_per_request')
            )
            ->groupBy('documents.id', 'documents.type_document', 'documents.price')
            ->orderBy('total_requests', 'desc')
            ->get();

        // Document request trends (current vs previous month)
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $currentMonthStats = DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->whereYear('student_requests.created_at', $currentMonth->year)
            ->whereMonth('student_requests.created_at', $currentMonth->month)
            ->select('documents.type_document', DB::raw('COUNT(*) as current_month_requests'))
            ->groupBy('documents.type_document')
            ->pluck('current_month_requests', 'type_document');

        $previousMonthStats = DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->whereYear('student_requests.created_at', $previousMonth->year)
            ->whereMonth('student_requests.created_at', $previousMonth->month)
            ->select('documents.type_document', DB::raw('COUNT(*) as previous_month_requests'))
            ->groupBy('documents.type_document')
            ->pluck('previous_month_requests', 'type_document');

        // Calculate trends
        $documentTrends = $topDocuments->map(function($doc) use ($currentMonthStats, $previousMonthStats) {
            $current = $currentMonthStats->get($doc->type_document, 0);
            $previous = $previousMonthStats->get($doc->type_document, 0);
            
            $trend = 'stable';
            $percentage = 0;
            
            if ($previous > 0) {
                $percentage = (($current - $previous) / $previous) * 100;
                if ($percentage > 10) $trend = 'increasing';
                elseif ($percentage < -10) $trend = 'decreasing';
            } elseif ($current > 0) {
                $trend = 'new';
                $percentage = 100;
            }

            return array_merge((array)$doc, [
                'trend' => $trend,
                'trend_percentage' => round($percentage, 1),
                'current_month_requests' => $current,
                'previous_month_requests' => $previous
            ]);
        });

        return [
            'top_documents' => $documentTrends,
            'total_document_types' => Document::count(),
            'most_expensive' => Document::orderBy('price', 'desc')->first(),
            'least_expensive' => Document::orderBy('price', 'asc')->first(),
            'avg_document_price' => Document::avg('price')
        ];
    }

    /**
     * Get trend analytics
     */
    private function getTrendAnalytics()
    {
        // Weekly comparison (this week vs last week)
        $thisWeek = Carbon::now()->startOfWeek();
        $lastWeek = Carbon::now()->subWeek()->startOfWeek();

        $thisWeekRequests = StudentRequest::where('created_at', '>=', $thisWeek)->count();
        $lastWeekRequests = StudentRequest::whereBetween('created_at', [$lastWeek, $thisWeek])->count();

        $weeklyTrend = $lastWeekRequests > 0 ? (($thisWeekRequests - $lastWeekRequests) / $lastWeekRequests) * 100 : 0;

        // Daily patterns (day of week analysis)
        $dayPatterns = collect();
        for ($i = 0; $i < 7; $i++) {
            $dayName = Carbon::now()->startOfWeek()->addDays($i)->format('l');
            $requests = StudentRequest::whereRaw('DAYOFWEEK(created_at) = ?', [$i + 2])->count(); // MySQL DAYOFWEEK: 1=Sunday
            $dayPatterns->push([
                'day' => $dayName,
                'requests' => $requests
            ]);
        }

        // Seasonal analysis (quarterly comparison)
        $quarters = collect();
        for ($q = 1; $q <= 4; $q++) {
            $startMonth = ($q - 1) * 3 + 1;
            $endMonth = $q * 3;
            
            $requests = StudentRequest::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', '>=', $startMonth)
                ->whereMonth('created_at', '<=', $endMonth)
                ->count();
                
            $quarters->push([
                'quarter' => "Q{$q} " . Carbon::now()->year,
                'requests' => $requests
            ]);
        }

        return [
            'weekly_trend' => [
                'this_week' => $thisWeekRequests,
                'last_week' => $lastWeekRequests,
                'percentage_change' => round($weeklyTrend, 1),
                'trend_direction' => $weeklyTrend > 0 ? 'up' : ($weeklyTrend < 0 ? 'down' : 'stable')
            ],
            'day_patterns' => $dayPatterns,
            'quarterly_analysis' => $quarters,
            'peak_day' => $dayPatterns->sortByDesc('requests')->first(),
            'slowest_day' => $dayPatterns->sortBy('requests')->first()
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        // System efficiency metrics
        $totalRequests = StudentRequest::count();
        $completedRequests = StudentRequest::where('status', 'completed')->count();
        $completionRate = $totalRequests > 0 ? ($completedRequests / $totalRequests) * 100 : 0;

        // Service level agreements (SLA) metrics
        $slaCompliance = StudentRequest::where('status', 'completed')
            ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, updated_at) <= 24')
            ->count();
        $slaRate = $completedRequests > 0 ? ($slaCompliance / $completedRequests) * 100 : 0;

        // Error rates and quality metrics
        $errorRate = 5; // Placeholder - would need to track actual errors
        $customerSatisfaction = 85; // Placeholder - would come from feedback system

        // Resource utilization - count registrars who have processed requests recently
        $activeRegistrars = User::where('role_id', 2)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('onsite_requests')
                      ->whereColumn('onsite_requests.assigned_registrar_id', 'users.id')
                      ->where('onsite_requests.updated_at', '>=', Carbon::now()->subDays(7));
            })
            ->count();
        $totalRegistrars = User::where('role_id', 2)->count();
        $utilization = $totalRegistrars > 0 ? ($activeRegistrars / $totalRegistrars) * 100 : 0;

        // Peak load handling
        $peakDayRequests = StudentRequest::selectRaw('DATE(created_at) as date, COUNT(*) as requests')
            ->groupBy('date')
            ->orderBy('requests', 'desc')
            ->first();

        return [
            'completion_rate' => round($completionRate, 1),
            'sla_compliance_rate' => round($slaRate, 1),
            'error_rate' => $errorRate,
            'customer_satisfaction' => $customerSatisfaction,
            'resource_utilization' => round($utilization, 1),
            'peak_load' => $peakDayRequests ? [
                'date' => Carbon::parse($peakDayRequests->date)->format('M j, Y'),
                'requests' => $peakDayRequests->requests
            ] : null,
            'avg_daily_requests' => round(StudentRequest::count() / max(1, Carbon::now()->diffInDays(Carbon::parse('2024-01-01'))), 1)
        ];
    }

    // 🆕 New Export Methods

    private function exportPredictiveReport()
    {
        $filename = 'predictive_analytics_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Predictive Analytics Report']);
            fputcsv($file, ['Generated on: ' . Carbon::now()->format('F j, Y g:i A')]);
            fputcsv($file, []);
            
            $predictiveAnalytics = $this->getPredictiveAnalytics();
            
            // Summary
            fputcsv($file, ['PREDICTION SUMMARY']);
            fputcsv($file, ['Total Pending Requests', $predictiveAnalytics['summary']['total_pending']]);
            fputcsv($file, ['Average Processing Hours', $predictiveAnalytics['summary']['avg_processing_hours']]);
            fputcsv($file, ['Estimated Queue Clear Time', $predictiveAnalytics['summary']['estimated_queue_clear_time']]);
            fputcsv($file, []);
            
            // Predictions
            fputcsv($file, ['INDIVIDUAL PREDICTIONS']);
            fputcsv($file, ['Reference No', 'Queue Position', 'Estimated Release', 'Processing Hours', 'Confidence']);
            
            foreach ($predictiveAnalytics['predictions'] as $prediction) {
                fputcsv($file, [
                    $prediction['reference_no'],
                    $prediction['queue_position'],
                    $prediction['estimated_release'],
                    $prediction['estimated_hours'],
                    $prediction['confidence']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportTrendsReport()
    {
        $filename = 'trends_analysis_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Trends Analysis Report']);
            fputcsv($file, ['Generated on: ' . Carbon::now()->format('F j, Y g:i A')]);
            fputcsv($file, []);
            
            $trendAnalytics = $this->getTrendAnalytics();
            
            // Weekly trends
            fputcsv($file, ['WEEKLY TREND ANALYSIS']);
            fputcsv($file, ['This Week Requests', $trendAnalytics['weekly_trend']['this_week']]);
            fputcsv($file, ['Last Week Requests', $trendAnalytics['weekly_trend']['last_week']]);
            fputcsv($file, ['Percentage Change', $trendAnalytics['weekly_trend']['percentage_change'] . '%']);
            fputcsv($file, ['Trend Direction', $trendAnalytics['weekly_trend']['trend_direction']]);
            fputcsv($file, []);
            
            // Day patterns
            fputcsv($file, ['DAY OF WEEK PATTERNS']);
            fputcsv($file, ['Day', 'Total Requests']);
            foreach ($trendAnalytics['day_patterns'] as $dayPattern) {
                fputcsv($file, [$dayPattern['day'], $dayPattern['requests']]);
            }
            fputcsv($file, []);
            
            // Quarterly analysis
            fputcsv($file, ['QUARTERLY ANALYSIS']);
            fputcsv($file, ['Quarter', 'Total Requests']);
            foreach ($trendAnalytics['quarterly_analysis'] as $quarter) {
                fputcsv($file, [$quarter['quarter'], $quarter['requests']]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPerformanceReport()
    {
        $filename = 'performance_metrics_report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Performance Metrics Report']);
            fputcsv($file, ['Generated on: ' . Carbon::now()->format('F j, Y g:i A')]);
            fputcsv($file, []);
            
            $performanceMetrics = $this->getPerformanceMetrics();
            
            fputcsv($file, ['KEY PERFORMANCE INDICATORS']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Completion Rate', $performanceMetrics['completion_rate'] . '%']);
            fputcsv($file, ['SLA Compliance Rate', $performanceMetrics['sla_compliance_rate'] . '%']);
            fputcsv($file, ['Resource Utilization', $performanceMetrics['resource_utilization'] . '%']);
            fputcsv($file, ['Error Rate', $performanceMetrics['error_rate'] . '%']);
            fputcsv($file, ['Customer Satisfaction', $performanceMetrics['customer_satisfaction'] . '%']);
            fputcsv($file, ['Average Daily Requests', $performanceMetrics['avg_daily_requests']]);
            
            if (isset($performanceMetrics['peak_load'])) {
                fputcsv($file, []);
                fputcsv($file, ['PEAK LOAD INFORMATION']);
                fputcsv($file, ['Peak Load Date', $performanceMetrics['peak_load']['date']]);
                fputcsv($file, ['Peak Load Requests', $performanceMetrics['peak_load']['requests']]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCompletePackage()
    {
        // This would ideally create a ZIP file with multiple CSV reports
        // For now, we'll export a comprehensive single CSV file
        
        $filename = 'complete_analytics_package_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['NU LIPA REGISTRAR SYSTEM - COMPLETE ANALYTICS PACKAGE']);
            fputcsv($file, ['Generated on: ' . Carbon::now()->format('F j, Y g:i A')]);
            fputcsv($file, ['Report Period: All time data up to current date']);
            fputcsv($file, []);
            
            // Executive Summary
            fputcsv($file, ['=== EXECUTIVE SUMMARY ===']);
            fputcsv($file, ['Total Users', User::count()]);
            fputcsv($file, ['Total Students', User::where('role_id', 3)->count()]);
            fputcsv($file, ['Total Registrars', User::where('role_id', 2)->count()]);
            fputcsv($file, ['Total Documents', Document::count()]);
            fputcsv($file, ['Total Requests', StudentRequest::count()]);
            fputcsv($file, ['Pending Requests', StudentRequest::where('status', 'pending')->count()]);
            fputcsv($file, ['Completed Requests', StudentRequest::where('status', 'completed')->count()]);
            fputcsv($file, []);
            
            // Document Statistics
            fputcsv($file, ['=== DOCUMENT TYPE STATISTICS ===']);
            fputcsv($file, ['Document Type', 'Request Count', 'Total Quantity', 'Revenue']);
            $documentStats = $this->getDetailedDocumentStatistics();
            foreach ($documentStats['top_documents'] as $doc) {
                fputcsv($file, [
                    $doc['type_document'],
                    $doc['total_requests'],
                    $doc['total_quantity'],
                    'PHP ' . number_format($doc['total_revenue'], 2)
                ]);
            }
            fputcsv($file, []);
            
            // Performance Metrics
            fputcsv($file, ['=== PERFORMANCE METRICS ===']);
            $performanceMetrics = $this->getPerformanceMetrics();
            fputcsv($file, ['Completion Rate', $performanceMetrics['completion_rate'] . '%']);
            fputcsv($file, ['SLA Compliance', $performanceMetrics['sla_compliance_rate'] . '%']);
            fputcsv($file, ['Resource Utilization', $performanceMetrics['resource_utilization'] . '%']);
            fputcsv($file, []);
            
            // Predictive Analytics Summary
            fputcsv($file, ['=== PREDICTIVE ANALYTICS SUMMARY ===']);
            $predictiveAnalytics = $this->getPredictiveAnalytics();
            fputcsv($file, ['Requests in Queue', $predictiveAnalytics['summary']['total_pending']]);
            fputcsv($file, ['Avg Processing Hours', $predictiveAnalytics['summary']['avg_processing_hours']]);
            fputcsv($file, ['Queue Clear Estimate', $predictiveAnalytics['summary']['estimated_queue_clear_time']]);
            fputcsv($file, []);
            
            // Monthly Trends
            fputcsv($file, ['=== MONTHLY TRENDS ===']);
            fputcsv($file, ['Month', 'Requests']);
            $monthlyTrends = $this->getMonthlyTrends();
            foreach ($monthlyTrends as $trend) {
                fputcsv($file, [$trend['month'], $trend['requests']]);
            }
            fputcsv($file, []);
            
            fputcsv($file, ['=== END OF REPORT ===']);
            fputcsv($file, ['For detailed analysis, please export individual report sections.']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get chart data as JSON for AJAX requests
     */
    public function getChartDataJson(Request $request)
    {
        $chartType = $request->get('type', 'all');
        $chartData = $this->getChartData();
        
        if ($chartType === 'all') {
            return response()->json($chartData);
        }
        
        return response()->json($chartData[$chartType] ?? []);
    }

    /**
     * Generate print-friendly report
     */
    public function printReport(Request $request)
    {
        // Use the same data as the index method but add a print flag
        $totalUsers = User::count();
        $totalStudents = User::whereHas('role', function($q) {
            $q->where('name', 'student');
        })->count();
        $totalRegistrars = User::whereHas('role', function($q) {
            $q->where('name', 'registrar');
        })->count();
        $totalAdmins = User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->count();
        
        // Document Statistics
        $totalDocuments = Document::count();
        
        // Request Statistics
        $totalRequests = StudentRequest::count();
        $totalOnlineRequests = StudentRequest::count();
        $totalOnsiteRequests = OnsiteRequest::count();
        
        // Status Statistics
        $pendingRequests = StudentRequest::where('status', 'pending')->count();
        $processingRequests = StudentRequest::where('status', 'processing')->count();
        $readyRequests = StudentRequest::where('status', 'ready_for_release')->count();
        $completedRequests = StudentRequest::where('status', 'completed')->count();
        
        // Document Type Analytics - Number of requests per document type
        $documentTypeStats = DB::table('student_requests')
            ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
            ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
            ->select('documents.type_document', DB::raw('COUNT(*) as request_count'), DB::raw('SUM(student_request_items.quantity) as total_quantity'))
            ->groupBy('documents.id', 'documents.type_document')
            ->orderBy('request_count', 'desc')
            ->get();
        
        // Processing Time Analytics
        $avgProcessingTime = $this->calculateAverageProcessingTime();
        $processingTimeStats = $this->getProcessingTimeBreakdown();
        
        // Queue Performance Metrics
        $queueStats = $this->getQueuePerformanceMetrics();
        
        // Monthly Trends
        $monthlyTrends = $this->getMonthlyTrends();
        
        // Registrar Performance
        $registrarPerformance = $this->getRegistrarPerformance();
        
        // Revenue Analytics
        $revenueStats = $this->getRevenueAnalytics();
        
        // Peak Hours Analysis
        $peakHoursData = $this->getPeakHoursAnalysis();
        
        // 🆕 Enhanced Analytics
        $chartData = $this->getChartData();
        $predictiveAnalytics = $this->getPredictiveAnalytics();
        $documentStatistics = $this->getDetailedDocumentStatistics();
        $trendAnalytics = $this->getTrendAnalytics();
        $performanceMetrics = $this->getPerformanceMetrics();
        
        return view('admin.reports', compact(
            'totalUsers', 'totalStudents', 'totalRegistrars', 'totalAdmins',
            'totalDocuments', 'totalRequests', 'totalOnlineRequests', 'totalOnsiteRequests',
            'pendingRequests', 'processingRequests', 'readyRequests', 'completedRequests',
            'documentTypeStats', 'avgProcessingTime', 'processingTimeStats',
            'queueStats', 'monthlyTrends', 'registrarPerformance', 'revenueStats', 'peakHoursData',
            'chartData', 'predictiveAnalytics', 'documentStatistics', 'trendAnalytics', 'performanceMetrics'
        ))->with('isPrint', true);
    }

    private function getAllReportData()
    {
        // Consolidate all data gathering for print view
        return [
            'totalUsers' => User::count(),
            'totalStudents' => User::where('role_id', 3)->count(),
            'totalRegistrars' => User::where('role_id', 2)->count(),
            'totalDocuments' => Document::count(),
            'totalRequests' => StudentRequest::count(),
            'pendingRequests' => StudentRequest::where('status', 'pending')->count(),
            'processingRequests' => StudentRequest::where('status', 'processing')->count(),
            'readyRequests' => StudentRequest::where('status', 'ready_for_release')->count(),
            'completedRequests' => StudentRequest::where('status', 'completed')->count(),
            'documentTypeStats' => DB::table('student_requests')
                ->join('student_request_items', 'student_requests.id', '=', 'student_request_items.student_request_id')
                ->join('documents', 'student_request_items.document_id', '=', 'documents.id')
                ->select('documents.type_document', DB::raw('COUNT(*) as request_count'), DB::raw('SUM(student_request_items.quantity) as total_quantity'))
                ->groupBy('documents.id', 'documents.type_document')
                ->orderBy('request_count', 'desc')
                ->get(),
            'avgProcessingTime' => $this->calculateAverageProcessingTime(),
            'processingTimeStats' => $this->getProcessingTimeBreakdown(),
            'queueStats' => $this->getQueuePerformanceMetrics(),
            'monthlyTrends' => $this->getMonthlyTrends(),
            'registrarPerformance' => $this->getRegistrarPerformance(),
            'revenueStats' => $this->getRevenueAnalytics(),
            'peakHoursData' => $this->getPeakHoursAnalysis(),
            'chartData' => $this->getChartData(),
            'predictiveAnalytics' => $this->getPredictiveAnalytics(),
            'documentStatistics' => $this->getDetailedDocumentStatistics(),
            'trendAnalytics' => $this->getTrendAnalytics(),
            'performanceMetrics' => $this->getPerformanceMetrics()
        ];
    }
}
