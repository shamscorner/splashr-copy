<?php

namespace App\Models;

use App\Events\CompanyCreditCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_id',
        'company_id',
        'manager_id',
        'reference_number',
        'quantity'
    ];

    protected $table = 'company_credit';

    protected static function booted()
    {
        // fire the credit created event
        static::created(function ($companyCredit) {
            CompanyCreditCreated::dispatch($companyCredit);
        });
    }

    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }
}
