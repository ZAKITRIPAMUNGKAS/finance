<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceController extends Controller
{
    /**
     * Display the authenticated printable / downloadable invoice view.
     */
    public function show(int $id)
    {
        $userId = auth()->id();
        $invoice = Invoice::with(['project.client', 'project.user', 'paidToAccount'])
            ->whereHas('project', fn($q) => $q->where('user_id', $userId))
            ->findOrFail($id);

        $user = auth()->user();
        $accounts = Account::where('user_id', $userId)->where('is_active', true)->get();

        return view('invoices.show', compact('invoice', 'user', 'accounts'));
    }

    /**
     * Display the public client invoice preview (shareable link).
     */
    public function publicView(string $hash)
    {
        try {
            $decoded = base64_decode($hash);
            $parts = explode('-', $decoded);
            $invoiceId = (int) ($parts[0] ?? 0);
        } catch (\Exception $e) {
            throw new NotFoundHttpException('Invoice link invalid.');
        }

        $invoice = Invoice::with(['project.client', 'project.user', 'paidToAccount'])
            ->findOrFail($invoiceId);

        // Verify hash integrity
        $expectedHashCheck = substr(md5($invoice->created_at . config('app.key')), 0, 8);
        if (($parts[1] ?? '') !== $expectedHashCheck) {
            throw new NotFoundHttpException('Invoice link expired or invalid.');
        }

        $user = $invoice->project->user;
        $accounts = Account::where('user_id', $user->id)->where('is_active', true)->get();

        return view('invoices.public', compact('invoice', 'user', 'accounts'));
    }
}
