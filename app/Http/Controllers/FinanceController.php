<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['student.school']);
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by school
        if ($request->has('school_id') && $request->school_id) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('school_id', $request->school_id);
            });
        }
        
        // Search by student
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }
        
        $payments = $query->orderBy('created_at', 'desc')->paginate(20);
        $schools = School::all();
        
        // Calculate statistics
        $stats = [
            'total_collected' => Payment::where('status', 'paid')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_payments' => Payment::count(),
        ];
        
        return view('finance.index', compact('payments', 'schools', 'stats'));
    }
    
    public function studentFinances()
    {
        $student = Auth::guard('student')->user();
        $payments = $student->payments()->orderBy('created_at', 'desc')->get();
        
        $totalPaid = $payments->where('status', 'paid')->sum('amount');
        $totalPending = $payments->where('status', 'pending')->sum('amount');
        
        return view('student.finances', compact('payments', 'totalPaid', 'totalPending'));
    }
}
