<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Student;
use App\Models\AcademicYear;

class FinanceController extends Controller
{
    /**
     * Display financial ledger / invoice list.
     */
    public function index(Request $request)
    {
        $query = Fee::with(['student.user', 'academicYear', 'payments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $like = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->whereHas('student', function ($q) use ($search, $like) {
                $q->where('student_id', $like, "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search, $like) {
                      $uq->where('name', $like, "%{$search}%");
                  });
            });
        }

        $fees = $query->paginate(10);
        return view('finance.index', compact('fees'));
    }

    /**
     * Show form to create a new fee invoice.
     */
    public function create()
    {
        $students = Student::with('user')->where('status', 'active')->get();
        $academicYears = AcademicYear::all();
        return view('finance.create', compact('students', 'academicYears'));
    }

    /**
     * Store a new fee invoice.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'scholarship_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
        ]);

        Fee::create([
            'student_id' => $request->student_id,
            'academic_year_id' => $request->academic_year_id,
            'semester' => $request->semester,
            'total_amount' => $request->total_amount,
            'scholarship_amount' => $request->scholarship_amount ?? 0.00,
            'discount_amount' => $request->discount_amount ?? 0.00,
            'due_date' => $request->due_date,
            'status' => 'unpaid',
        ]);

        return redirect()->route('finance.index')->with('success', 'Fee invoice generated successfully.');
    }

    /**
     * Show payment form for an invoice.
     */
    public function showPaymentForm(Fee $fee)
    {
        $fee->load(['student.user', 'payments']);
        $netAmount = $fee->total_amount - $fee->scholarship_amount - $fee->discount_amount;
        $totalPaid = $fee->payments->sum('amount');
        $outstanding = max(0, $netAmount - $totalPaid);

        return view('finance.payment', compact('fee', 'outstanding'));
    }

    /**
     * Process fee payment.
     */
    public function storePayment(Request $request, Fee $fee)
    {
        $netAmount = $fee->total_amount - $fee->scholarship_amount - $fee->discount_amount;
        $totalPaid = $fee->payments->sum('amount');
        $outstanding = max(0, $netAmount - $totalPaid);

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:' . $outstanding],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,credit_card,mobile_payment'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
        ]);

        // Generate receipt number (REC-YYYYMMDD-XXXX)
        $receiptNo = 'REC-' . date('Ymd') . '-' . str_pad(Payment::count() + 1, 4, '0', STR_PAD_LEFT);

        Payment::create([
            'fee_id' => $fee->id,
            'amount' => $request->amount,
            'payment_date' => date('Y-m-d'),
            'payment_method' => $request->payment_method,
            'transaction_reference' => $request->transaction_reference,
            'receipt_no' => $receiptNo,
        ]);

        // Update fee status
        $newTotalPaid = $totalPaid + $request->amount;
        if ($newTotalPaid >= $netAmount) {
            $fee->status = 'paid';
        } else {
            $fee->status = 'partially_paid';
        }
        $fee->save();

        return redirect()->route('finance.index')->with('success', 'Payment recorded successfully. Receipt No: ' . $receiptNo);
    }

    /**
     * View payment receipt.
     */
    public function receipt(Payment $payment)
    {
        $payment->load(['fee.student.user', 'fee.academicYear']);
        return view('finance.receipt', compact('payment'));
    }
}
