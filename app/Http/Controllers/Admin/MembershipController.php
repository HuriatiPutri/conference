<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceHistory;
use App\Models\Membership;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::with(['package', 'user', 'invoices'])
            ->latest()
            ->paginate(15);
            
        return Inertia::render('Admin/Memberships/Index', [
            'memberships' => $memberships
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

        return redirect()->back()->with('success', 'Payment marked as failed.');
    }
}
