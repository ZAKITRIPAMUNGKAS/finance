<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        $this->applyBalanceChange($transaction);
    }

    public function updated(Transaction $transaction): void
    {
        // Revert old transaction effect
        $this->revertBalanceChange($transaction->getOriginal());
        // Apply new transaction effect
        $this->applyBalanceChange($transaction);
    }

    public function deleted(Transaction $transaction): void
    {
        $this->revertBalanceChange($transaction->toArray());
    }

    protected function applyBalanceChange(Transaction $transaction): void
    {
        $amount = (float) $transaction->amount;
        $account = Account::find($transaction->account_id);

        if ($account) {
            if ($transaction->type === 'income') {
                $account->increment('current_balance', $amount);
            } elseif ($transaction->type === 'expense') {
                $account->decrement('current_balance', $amount);
            } elseif ($transaction->type === 'transfer') {
                $account->decrement('current_balance', $amount);
                if ($transaction->destination_account_id) {
                    $destAccount = Account::find($transaction->destination_account_id);
                    if ($destAccount) {
                        $destAccount->increment('current_balance', $amount);
                    }
                }
            }
        }
    }

    protected function revertBalanceChange(array $original): void
    {
        $amount = (float) ($original['amount'] ?? 0);
        $type = $original['type'] ?? 'expense';
        $accountId = $original['account_id'] ?? null;
        $destAccountId = $original['destination_account_id'] ?? null;

        if ($accountId) {
            $account = Account::find($accountId);
            if ($account) {
                if ($type === 'income') {
                    $account->decrement('current_balance', $amount);
                } elseif ($type === 'expense') {
                    $account->increment('current_balance', $amount);
                } elseif ($type === 'transfer') {
                    $account->increment('current_balance', $amount);
                    if ($destAccountId) {
                        $destAccount = Account::find($destAccountId);
                        if ($destAccount) {
                            $destAccount->decrement('current_balance', $amount);
                        }
                    }
                }
            }
        }
    }
}
