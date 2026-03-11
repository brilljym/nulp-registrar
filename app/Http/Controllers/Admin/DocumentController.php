<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\StudentRequestItem;
use App\Models\StudentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents
     */
    public function index()
    {
        $documents = Document::orderBy('id', 'desc')->paginate(15);
        return view('admin.documents.index', compact('documents'));
    }

    /**
     * Store a newly created document
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_document' => 'required|string|max:255|unique:documents,type_document',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            Document::create([
                'type_document' => $request->type_document,
                'price' => $request->price,
                'description' => $request->description,
                'is_active' => $request->has('is_active')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document type created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified document
     */
    public function show($id)
    {
        try {
            $document = Document::findOrFail($id);
            return response()->json($document);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        }
    }

    /**
     * Update the specified document
     */
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type_document' => 'required|string|max:255|unique:documents,type_document,' . $id,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $document->update([
                'type_document' => $request->type_document,
                'price' => $request->price,
                'description' => $request->description,
                'is_active' => $request->has('is_active')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document type updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle document active status
     */
    public function toggleStatus($id)
    {
        try {
            $document = Document::findOrFail($id);
            $document->update([
                'is_active' => !($document->is_active ?? true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document status updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating document status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get document statistics
     */
    public function getStats($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            // Get request statistics
            $totalRequests = StudentRequestItem::where('document_id', $id)->count();
            $totalQuantity = StudentRequestItem::where('document_id', $id)->sum('quantity');
            
            // Get revenue statistics
            $totalRevenue = StudentRequestItem::where('document_id', $id)
                          ->join('student_requests', 'student_request_items.student_request_id', '=', 'student_requests.id')
                          ->where('student_requests.payment_confirmed', true)
                          ->sum(DB::raw('student_request_items.price * student_request_items.quantity'));

            // Get monthly trend (last 6 months)
            $monthlyTrend = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $count = StudentRequestItem::where('document_id', $id)
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                
                $monthlyTrend[] = [
                    'month' => $date->format('M Y'),
                    'count' => $count
                ];
            }

            // Get recent requests
            $recentRequests = StudentRequestItem::where('document_id', $id)
                            ->with(['studentRequest.student.user'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();

            $html = view('admin.documents.stats', compact(
                'document', 'totalRequests', 'totalQuantity', 'totalRevenue', 
                'monthlyTrend', 'recentRequests'
            ))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading document statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified document
     */
    public function destroy($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            // Check if document has been used in requests
            $requestCount = StudentRequestItem::where('document_id', $id)->count();
            
            if ($requestCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete document that has been used in requests. Consider deactivating instead.'
                ], 422);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document type deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting document: ' . $e->getMessage()
            ], 500);
        }
    }
}
