<?php

namespace App\Services\Accounting;

use App\Models\Sale;
use App\Models\Journal;
use App\Models\Coa;
use App\Services\Accounting\DTO\SaleSummary;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use App\Exceptions\Accounting\InvalidCoaException;

class JournalService
{
    public function recordSale(Sale $sale, SaleSummary $summary): void
    {
        $journalNo = $this->generateJournalNo();

        $coa = $this->resolveCoaForSale();

        $entries = [];

        // 1️⃣ Debit Kas / Piutang
        $entries[] = $this->entry(
            journalNo: $journalNo,
            sale: $sale,
            coa: $coa['cash'],
            amount: $summary->grandTotal,
            type: 'debit',
            description: 'Penerimaan penjualan'
        );

        // 2️⃣ Kredit Penjualan
        $entries[] = $this->entry(
            journalNo: $journalNo,
            sale: $sale,
            coa: $coa['revenue'],
            amount: $summary->subtotal,
            type: 'credit',
            description: 'Pendapatan penjualan'
        );

        // 3️⃣ Debit Diskon (opsional)
        if ($summary->hasDiscount()) {
            $entries[] = $this->entry(
                journalNo: $journalNo,
                sale: $sale,
                coa: $coa['discount'],
                amount: $summary->discount,
                type: 'debit',
                description: 'Diskon penjualan'
            );
        }

        $this->assertBalanced($entries);

        Journal::insert($entries);
    }

    protected function generateJournalNo(): string
    {
        return 'JR-' . now()->format('YmdHisv');
    }

    protected function entry(
        string $journalNo,
        Sale $sale,
        Coa $coa,
        float $amount,
        string $type,
        string $description
    ): array {
        $this->assertDebitCreditRule($coa, $type);

        return [
            'journal_no'   => $journalNo,
            // fallback aman jika tgl_jual belum final
            'journal_date' => $sale->tgl_jual ?? $sale->created_at,
            'sale_id'      => $sale->id,
            'coa_id'       => $coa->id,
            'amount'       => $amount,
            'type'         => $type,
            'description' => $description,
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }

    protected function assertBalanced(array $entries): void
    {
        $debit = collect($entries)->where('type', 'debit')->sum('amount');
        $credit = collect($entries)->where('type', 'credit')->sum('amount');

        if (round($debit, 2) !== round($credit, 2)) {
            throw new RuntimeException(
                "Jurnal tidak seimbang. Debit: {$debit} | Credit: {$credit}"
            );
        }
    }

    protected function resolveCoaForSale(): array
    {
        $cash = $this->findCoaOrFail('101');
        $revenue = $this->findCoaOrFail('401');
        $discount = $this->findCoaOrFail('402');

        $this->assertCoaType($cash, 'ASET', 'record_sale');
        $this->assertCoaType($revenue, 'PENDAPATAN', 'record_sale');
        $this->assertCoaType($discount, 'KONTRA PENDAPATAN', 'record_sale');

        return compact('cash', 'revenue', 'discount');
    }

    protected function findCoaOrFail(string $code): Coa
    {
        return Coa::where('code', $code)->firstOrFail();
    }

    protected function assertCoaType(
        Coa $coa,
        string $expectedJenis,
        string $action
    ): void {
        $jenis = $coa->jenis;

        while ($jenis->parent) {
            $jenis = $jenis->parent;
        }

        if (strtoupper($jenis->nama) !== strtoupper($expectedJenis)) {
            throw new InvalidCoaException(
                message: "COA {$coa->code} bukan jenis {$expectedJenis}",
                coa: $coa,
                expectedType: $expectedJenis,
                action: $action
            );
        }
    }

    protected function assertDebitCreditRule(
        Coa $coa,
        string $type
    ): void {
        $rootJenis = strtoupper(
            $coa->jenis->parent
                ? $coa->jenis->parent->nama
                : $coa->jenis->nama
        );

        $rules = [
            'ASET'             => 'debit',
            'PENDAPATAN'       => 'credit',
            'KONTRA PENDAPATAN' => 'debit',
        ];

        if (($rules[$rootJenis] ?? null) !== $type) {
            throw new InvalidCoaException(
                message: "COA {$coa->code} tidak boleh {$type}",
                coa: $coa,
                expectedType: $rootJenis,
                action: 'journal_entry'
            );
        }
    }
}
