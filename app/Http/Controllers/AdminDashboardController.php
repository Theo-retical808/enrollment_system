<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Professor;
use App\Models\Student;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_professors' => Professor::count(),
            'total_enrollments' => Enrollment::count(),
            'pending_enrollments' => Enrollment::where('status', 'submitted')->count(),
            'approved_enrollments' => Enrollment::where('status', 'approved')->count(),
            'total_payments' => Payment::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'confirmed_payments' => Payment::where('status', 'paid')->count(),
            'total_revenue' => Payment::where('status', 'paid')->sum('amount'),
        ];

        $recentEnrollments = Enrollment::with(['student.school'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentPayments = Payment::with(['student.school'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentEnrollments', 'recentPayments'));
    }

    /**
     * View all accounts (students, professors, admins).
     */
    public function accounts(Request $request)
    {
        $type = $request->get('type', 'all');
        $search = $request->get('search', '');

        $students = collect();
        $professors = collect();
        $admins = collect();

        if ($type === 'all' || $type === 'students') {
            $studentQuery = Student::with('school');
            if ($search) {
                $studentQuery->where(function ($q) use ($search) {
                    $q->where('student_id', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            $students = $studentQuery->orderBy('student_id')->get();
        }

        if ($type === 'all' || $type === 'professors') {
            $professorQuery = Professor::with('school');
            if ($search) {
                $professorQuery->where(function ($q) use ($search) {
                    $q->where('professor_id', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            $professors = $professorQuery->orderBy('professor_id')->get();
        }

        if ($type === 'all' || $type === 'admins') {
            $adminQuery = Admin::query();
            if ($search) {
                $adminQuery->where(function ($q) use ($search) {
                    $q->where('admin_id', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            $admins = $adminQuery->orderBy('admin_id')->get();
        }

        return view('admin.accounts', compact('students', 'professors', 'admins', 'type', 'search'));
    }

    /**
     * View and manage all payments.
     */
    public function payments(Request $request)
    {
        $query = Payment::with(['student.school']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total_collected' => Payment::where('status', 'paid')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'total_payments' => Payment::count(),
        ];

        return view('admin.payments', compact('payments', 'stats'));
    }

    /**
     * Confirm a payment.
     */
    public function confirmPayment(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $admin = Auth::guard('admin')->user();

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        Log::info('Payment confirmed by admin', [
            'payment_id' => $payment->id,
            'student_id' => $payment->student_id,
            'admin_id' => $admin->id,
            'amount' => $payment->amount,
        ]);

        return redirect()->back()->with('success', 'Payment confirmed successfully.');
    }

    /**
     * Reject a payment.
     */
    public function rejectPayment(Request $request, $paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $admin = Auth::guard('admin')->user();

        $payment->update([
            'status' => 'rejected',
        ]);

        Log::info('Payment rejected by admin', [
            'payment_id' => $payment->id,
            'student_id' => $payment->student_id,
            'admin_id' => $admin->id,
        ]);

        return redirect()->back()->with('success', 'Payment rejected.');
    }

    /**
     * View and manage all enrollments.
     */
    public function enrollments(Request $request)
    {
        $query = Enrollment::with(['student.school', 'courses']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => Enrollment::count(),
            'pending' => Enrollment::where('status', 'submitted')->count(),
            'approved' => Enrollment::where('status', 'approved')->count(),
            'rejected' => Enrollment::where('status', 'rejected')->count(),
        ];

        return view('admin.enrollments', compact('enrollments', 'stats'));
    }

    /**
     * Override enrollment decision (approve or reject regardless of professor decision).
     */
    public function overrideEnrollment(Request $request, $enrollmentId)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_comments' => 'nullable|string|max:1000',
        ]);

        $enrollment = Enrollment::with(['student', 'courses'])->findOrFail($enrollmentId);
        $admin = Auth::guard('admin')->user();
        $action = $request->input('action');
        $comments = $request->input('admin_comments', 'Admin override');

        try {
            DB::beginTransaction();

            $oldStatus = $enrollment->status;

            if ($action === 'approve') {
                $enrollment->update([
                    'status' => 'approved',
                    'review_comments' => '[Admin Override] ' . $comments,
                    'reviewed_at' => now(),
                ]);
                if (method_exists($enrollment, 'finalize')) {
                    $enrollment->finalize();
                }
                $message = 'Enrollment approved (admin override).';
            } else {
                $enrollment->update([
                    'status' => 'rejected',
                    'review_comments' => '[Admin Override] ' . $comments,
                    'reviewed_at' => now(),
                ]);
                if (method_exists($enrollment, 'enableResubmission')) {
                    $enrollment->enableResubmission();
                }
                $message = 'Enrollment rejected (admin override).';
            }

            Log::info('Enrollment overridden by admin', [
                'enrollment_id' => $enrollment->id,
                'student_id' => $enrollment->student_id,
                'admin_id' => $admin->id,
                'action' => $action,
                'old_status' => $oldStatus,
                'new_status' => $enrollment->status,
            ]);

            DB::commit();

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin enrollment override failed', [
                'enrollment_id' => $enrollmentId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to override enrollment. Please try again.');
        }
    }
}
