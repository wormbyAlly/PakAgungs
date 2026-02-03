<?php

namespace App\Services\Sales;

use App\Models\InvoiceSequence;
use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    public function generate(): string
    {
        return DB::transaction(function () {

            $today = now()->toDateString();

            $sequence = InvoiceSequence::lockForUpdate()
                ->firstOrCreate(
                    ['date' => $today],
                    ['last_number' => 0]
                );

            $sequence->increment('last_number');

            return $this->formatInvoice(
                date: $today,
                number: $sequence->last_number
            );
        });
    }

    protected function formatInvoice(string $date, int $number): string
    {
        return sprintf(
            'INV-%s-%04d',
            str_replace('-', '', $date),
            $number
        );
    }
}
