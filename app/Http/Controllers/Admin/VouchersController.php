<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class VouchersController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 15);

        $query = Voucher::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('code', 'LIKE', "%{$search}%");
        }

        $vouchers = $query->orderBy('id', 'desc')->paginate($perPage)->appends($request->all());

        return Inertia::render('Admin/Vouchers/Index', [
            'vouchers' => $vouchers,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Vouchers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6|alpha_num|unique:vouchers,code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'quota' => 'required|integer|min:1',
            'applies_to' => 'required|array|min:1',
            'applies_to.*' => 'in:conference_registration,joiv_article,membership_registration',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'discount_description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['used_count'] = 0;
        $validated['created_by'] = Auth::id();

        Voucher::create($validated);

        return Redirect::route('vouchers.index')->with('success', 'Voucher created successfully.');
    }

    public function edit(Voucher $voucher): Response
    {
        return Inertia::render('Admin/Vouchers/Edit', [
            'voucher' => $voucher,
        ]);
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6|alpha_num|unique:vouchers,code,' . $voucher->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'quota' => 'required|integer|min:1',
            'applies_to' => 'required|array|min:1',
            'applies_to.*' => 'in:conference_registration,joiv_article,membership_registration',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'discount_description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['updated_by'] = Auth::id();

        if ($voucher->used_count > $validated['quota']) {
            return Redirect::back()->withErrors([
                'quota' => 'Quota cannot be lower than used count (' . $voucher->used_count . ').',
            ]);
        }

        $voucher->update($validated);

        return Redirect::route('vouchers.index')->with('success', 'Voucher updated successfully.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return Redirect::back()->with('success', 'Voucher deleted successfully.');
    }

    public function report(Request $request): Response
    {
        $perPage = $request->input('per_page', 15);

        $query = \App\Models\VoucherClaim::with('voucher');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by transaction type
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Filter by voucher code
        if ($request->filled('voucher_code')) {
            $query->whereHas('voucher', function ($subQuery) {
                $subQuery->where('code', 'LIKE', '%' . request('voucher_code') . '%');
            });
        }

        // Filter by email
        if ($request->filled('email')) {
            $query->where('email', 'LIKE', '%' . $request->email . '%');
        }

        $claims = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->all());

        // Calculate summary statistics
        $totalClaims = $claims->total();
        $baseQuery = \App\Models\VoucherClaim::query();

        if ($request->filled('start_date')) {
            $baseQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $baseQuery->whereDate('created_at', '<=', $request->end_date);
        }

        $totalDiscountAmount = 0;
        foreach ($baseQuery->get() as $claim) {
            if ($claim->voucher) {
                // Estimate base amount (we need to calculate from registration data)
                // For now, we'll show the voucher discount settings
                if ($claim->voucher->discount_type === 'percent') {
                    // Can't calculate exact discount without base amount
                } elseif ($claim->voucher->discount_type === 'fixed') {
                    $totalDiscountAmount += $claim->voucher->discount_value;
                }
            }
        }

        return Inertia::render('Admin/Vouchers/Report', [
            'claims' => $claims,
            'filters' => $request->only(['start_date', 'end_date', 'transaction_type', 'voucher_code', 'email']),
            'summary' => [
                'total_claims' => $totalClaims,
                'total_discount_amount' => $totalDiscountAmount,
                'claims_this_month' => \App\Models\VoucherClaim::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            ],
        ]);
    }
}
