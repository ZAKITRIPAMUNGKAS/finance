<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'invoice_number',
        'amount',
        'issue_date',
        'due_date',
        'status',
        'paid_at',
        'paid_to_account_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function paidToAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'paid_to_account_id');
    }

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['sent', 'overdue']) && $this->due_date < now()->startOfDay();
    }

    public function getPublicUrlAttribute(): string
    {
        $hash = base64_encode($this->id . '-' . substr(md5($this->created_at . config('app.key')), 0, 8));
        return route('invoices.public', ['hash' => $hash]);
    }

    public function getWhatsappShareUrlAttribute(): string
    {
        $client = $this->project?->client;
        $clientName = $client?->name ?? 'Klien';
        $projectName = $this->project?->name ?? 'Project';
        $formattedAmount = 'Rp ' . number_format($this->amount, 0, ',', '.');
        $dueDateFormatted = $this->due_date ? $this->due_date->format('d M Y') : '-';
        $publicUrl = $this->public_url;
        $user = $this->project?->user;

        if ($this->status === 'paid') {
            $paidDateFormatted = $this->paid_at ? $this->paid_at->format('d M Y') : now()->format('d M Y');
            $senderName = $user?->name ?? 'kami';

            $message = "Halo kak {$clientName}!\n\n"
                     . "Terima kasih banyak atas pelunasan pembayaran invoice *{$this->invoice_number}* untuk project *{$projectName}*.\n\n"
                     . "Rincian Pembayaran:\n"
                     . "- No. Invoice: *{$this->invoice_number}*\n"
                     . "- Total: *{$formattedAmount}*\n"
                     . "- Status: *LUNAS (PAID)*\n"
                     . "- Tanggal Lunas: {$paidDateFormatted}\n\n"
                     . "Kwitansi / Invoice Resmi:\n"
                     . "{$publicUrl}\n\n"
                     . "Terima kasih banyak sudah mempercayakan project ini kepada {$senderName}. Senang sekali bisa berkolaborasi dan bekerja sama dengan Kak {$clientName} serta tim. Semoga hasilnya memuaskan, berkah, dan sukses selalu untuk usahanya!\n\n"
                     . "Sampai jumpa di project dan kolaborasi seru selanjutnya ya kak!";
        } else {
            // Prefer bank account with valid account number
            $account = null;
            if ($user) {
                $account = Account::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->whereNotNull('account_number')
                    ->where('account_number', '!=', '')
                    ->where('account_number', '!=', 'Hubungi Pengirim')
                    ->first() 
                    ?? Account::where('user_id', $user->id)->where('is_active', true)->first();
            }

            $bankInfo = "";
            if ($account && !empty($account->account_number) && $account->account_number !== 'Hubungi Pengirim') {
                $bankInfo = "\n\n*Rekening Pembayaran:*\nBank: {$account->name} (" . strtoupper($account->type) . ")\nNo. Rek: {$account->account_number}\na.n {$user->name}";
            }

            $message = "Halo {$clientName},\n\n"
                     . "Berikut rincian tagihan invoice untuk project *{$projectName}*:\n\n"
                     . "- *No. Invoice:* {$this->invoice_number}\n"
                     . "- *Total Tagihan:* {$formattedAmount}\n"
                     . "- *Jatuh Tempo:* {$dueDateFormatted}\n\n"
                     . "- *Lihat / Unduh Invoice Lengkap:*\n"
                     . "{$publicUrl}{$bankInfo}\n\n"
                     . "Terima kasih atas kerja samanya!";
        }

        $phone = $client?->phone ? preg_replace('/[^0-9]/', '', $client->phone) : '';
        if ($phone && str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if ($phone) {
            return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
        }

        return 'https://wa.me/?text=' . urlencode($message);
    }
}
