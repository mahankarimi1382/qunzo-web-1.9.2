<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadingRow, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(
        public $transactions,
        public $userRole = 'User',
    ) {}

    public function collection()
    {
        return $this->transactions;
    }

    public function map($transaction): array
    {
        $amountSign = match (true) {
            $this->userRole === 'User' => (isPlusTransaction($transaction->type) == true ? '+' : '-'),
            $this->userRole === 'Agent' => (isAgentPlusTransaction($transaction->type) == true ? '+' : '-'),
            $this->userRole === 'Merchant' => (isMerchantPlusTransaction($transaction->type) == true ? '+' : '-'),
            default => '',
        };

        $chargeSign = match (true) {
            $this->userRole === 'User' || $this->userRole === 'Agent' || $this->userRole === 'Merchant' => '-',
            default => '+',
        };

        $data = [
            $transaction->created_at,
            $transaction->description,
            $transaction->wallet_type === 'default' ? 'Main Wallet' : $transaction->currency?->name ?? 'N/A',
            $transaction->tnx,
            ucfirst(str_replace('_', ' ', $transaction->type->value)),
            $amountSign.formatAmount($transaction->amount, $transaction->currency, showCurrency: true),
            $chargeSign.formatAmount($transaction->charge, $transaction->currency, showCurrency: true),
            $transaction->method,
            ucwords($transaction->status->value),
        ];

        if ($this->userRole === 'Admin') {
            array_unshift($data, $transaction->user->username);
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            'Date',
            'Description',
            'Wallet',
            'Transaction ID',
            'Type',
            'Amount',
            'Charge',
            'Method',
            'Status',
        ];

        if ($this->userRole === 'Admin') {
            array_unshift($headings, 'User');
        }

        return $headings;
    }
}
