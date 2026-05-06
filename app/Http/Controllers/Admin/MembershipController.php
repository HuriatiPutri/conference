<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceHistory;
use App\Models\Membership;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $query = Membership::with(['package', 'user', 'invoices']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('institution', 'LIKE', "%{$search}%");
            });
        }

        $memberships = $query->latest()->paginate($request->input('per_page', 15));

        $filters = [
            'status' => $request->status,
            'search' => $request->search,
        ];

        return Inertia::render('Admin/Memberships/Index', [
            'memberships' => $memberships,
            'filters' => $filters,
        ]);
    }

    public function updatePaymentStatus(Request $request, Membership $membership, InvoiceHistory $invoice)
    {
        $request->validate([
            'status' => 'required|in:completed,failed'
        ]);

        if ($invoice->reference_type !== Membership::class || $invoice->reference_id !== $membership->id) {
            abort(403, 'Invalid invoice reference.');
        }

        $invoice->update([
            'status' => $request->status,
            'payment_completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        if ($request->status === 'completed') {
            $membership->activate();

            // Generate token and send email so user can set password
            if (!$membership->user_id) {
                $membership->sendSetPasswordEmail();
            }

            return redirect()->back()->with('success', 'Payment verified and set-password email sent.');
        }
    }
}
