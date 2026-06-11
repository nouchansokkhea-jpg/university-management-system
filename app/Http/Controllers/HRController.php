<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\Staff;
use Illuminate\Validation\Rule;

class HRController extends Controller
{
    /**
     * Display payroll history list.
     */
    public function payrollIndex(Request $request)
    {
        $payrolls = Payroll::with('user')->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10);
        return view('hr.payroll', compact('payrolls'));
    }

    /**
     * Show form to generate/record payroll.
     */
    public function payrollCreate()
    {
        // Get all lecturers and staff users
        $lecturers = Lecturer::with('user')->get();
        $staff = Staff::with('user')->get();
        
        $employees = collect();
        foreach ($lecturers as $l) {
            $employees->push([
                'id' => $l->user->id,
                'name' => $l->user->name . ' (Lecturer)',
                'salary' => $l->salary
            ]);
        }
        foreach ($staff as $s) {
            $employees->push([
                'id' => $s->user->id,
                'name' => $s->user->name . ' (Staff)',
                'salary' => $s->salary
            ]);
        }

        return view('hr.payroll-create', compact('employees'));
    }

    /**
     * Store new payroll item.
     */
    public function payrollStore(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020', 'max:' . date('Y')],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,paid'],
        ]);

        $allowances = $request->allowances ?? 0.00;
        $deductions = $request->deductions ?? 0.00;
        $net = $request->basic_salary + $allowances - $deductions;

        Payroll::create([
            'user_id' => $request->user_id,
            'month' => $request->month,
            'year' => $request->year,
            'basic_salary' => $request->basic_salary,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'net_salary' => $net,
            'payment_date' => $request->status === 'paid' ? date('Y-m-d') : null,
            'status' => $request->status,
        ]);

        return redirect()->route('hr.payroll.index')->with('success', 'Payroll record processed successfully.');
    }

    /**
     * Display leave requests.
     */
    public function leaveIndex()
    {
        $leaves = LeaveRequest::with(['user', 'approver'])->orderBy('created_at', 'desc')->paginate(10);
        return view('hr.leaves', compact('leaves'));
    }

    /**
     * Show leave request submission form.
     */
    public function leaveCreate()
    {
        return view('hr.leave-create');
    }

    /**
     * Store a new leave request.
     */
    public function leaveStore(Request $request)
    {
        $request->validate([
            'leave_type' => ['required', 'string', 'in:sick,casual,annual,maternity,unpaid'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string'],
        ]);

        LeaveRequest::create([
            'user_id' => $request->user()->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Approve or reject a leave request.
     */
    public function leaveApprove(Request $request, LeaveRequest $leave)
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        $leave->update([
            'status' => $request->status,
            'approved_by' => $request->user()->id,
        ]);

        return redirect()->route('hr.leaves.index')->with('success', 'Leave request has been ' . $request->status . '.');
    }
}
