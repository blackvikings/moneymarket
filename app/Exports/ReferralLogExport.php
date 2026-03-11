<?php

namespace App\Exports;

use App\Models\ReferralLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReferralLogExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = ReferralLog::with(['lead', 'paidBy:id,name']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        $referralPerson = $this->filters['referral_person'] ?? null;
        $referralType = $this->filters['referral_type'] ?? null;

        if ($referralType || $referralPerson) {
            $query->whereHas('lead', function ($q) use ($referralType, $referralPerson) {
                if ($referralType) {
                    if ($referralType == 'Employee') {
                        $sourceNames = \App\Models\User::when($referralPerson, function ($query) use ($referralPerson) {
                            $query->where('name', 'like', '%' . $referralPerson . '%');
                        })->pluck('name');
                        $q->whereIn('lead_source_person_name', $sourceNames);
                    } elseif ($referralType == 'Lead') {
                        $sourceNames = \App\Models\Lead::when($referralPerson, function ($query) use ($referralPerson) {
                            $query->where('customer_name', 'like', '%' . $referralPerson . '%');
                        })->pluck('customer_name');
                        $q->whereIn('lead_source_person_name', $sourceNames);
                    } elseif ($referralType == 'Referral') {
                        $sourceNames = \App\Models\ReferralPerson::when($referralPerson, function ($query) use ($referralPerson) {
                            $query->where('name', 'like', '%' . $referralPerson . '%');
                        })->pluck('name');
                        $q->whereIn('lead_source_person_name', $sourceNames);
                    }
                } else {
                    // Only referral_person is filled
                    $q->where('lead_source_person_name', 'like', '%' . $referralPerson . '%');
                }
            });
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            '#',
            'Lead Customer',
            'Referral Person',
            'Amount',
            'Status',
            'Paid At',
            'Paid By',
        ];
    }

    public function map($referral): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $referral->lead->customer_name ?? 'N/A',
            $referral->lead->lead_source_person_name ?? 'N/A',
            $referral->amount,
            $referral->status,
            $referral->paid_at ? $referral->paid_at->format('d-m-Y H:i A') : '-',
            $referral->paidBy ? $referral->paidBy->name : '-',
        ];
    }
}
