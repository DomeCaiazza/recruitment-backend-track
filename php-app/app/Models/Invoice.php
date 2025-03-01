<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    public function taxProfile()
    {
        return $this->belongsTo(TaxProfile::class);
    }

    protected $fillable = [
        'tax_profile_id',
        'invoice_date',
        'subtotal',
        'tax_amount',
        'discount',
        'currency',
        'status',
        'paid_at',
        'canceled_at',
        'notes',
    ];

    public function setStatusAttribute($value)
    {
        $allowed = ['pending', 'paid', 'canceled'];
        if (!in_array($value, $allowed)) {
            throw new \InvalidArgumentException("Invalid status: $value, allowed values: " . implode(',', $allowed));
        }

        $this->attributes['status'] = $value;
    }

    public function setCurrencyAttribute($value)
    {
        $allowed = ['EUR', 'USD'];
        if (!in_array($value, $allowed)) {
            throw new \InvalidArgumentException("Invalid currency: $value, allowed values: " . implode(',', $allowed));
        }

        $this->attributes['currency'] = $value;
    }

    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($invoice) {
            $invoice->setInvoiceNumber();
            $invoice->setStatusTimestampsOnCreate();
        });
    
        static::updating(function ($invoice) {
            $invoice->calculateTotal();
            $invoice->updateStatusTimestampsOnUpdate();
        });
    }
    
    public function setInvoiceNumber()
    {
        $this->invoice_number = time() . '_' . Str::upper(Str::random(5));
    }
    
    public function setStatusTimestampsOnCreate()
    {
        if ($this->status === 'paid') {
            $this->paid_at = now();
        }
    
        if ($this->status === 'canceled') {
            $this->canceled_at = now();
        }
    }
    
    public function calculateTotal()
    {
        if ($this->isDirty('subtotal') || $this->isDirty('tax_amount') || $this->isDirty('discount')) {
            $this->total = $this->subtotal + $this->tax_amount - $this->discount;
        }
    }
    
    public function updateStatusTimestampsOnUpdate()
    {
        if ($this->isDirty('status')) {
            if ($this->status === 'paid') {
                $this->paid_at = now();
            } elseif ($this->status === 'canceled') {
                $this->canceled_at = now();
            }
        }
    }
    

    protected static function booted()
    {
        static::created(function ($resource) {
            Log::info('Invoice created', ['id' => $resource->id, 'tax_profile_id' => $resource->tax_profile_id]);
        });

        static::updated(function ($resource) {
            Log::info('Invoice updated',
            ['id' => $resource->id,
            'invoice_number' => $resource->tax_code]);
        });

        static::deleted(function ($resource) {
            Log::info('Invoice deleted', ['id' => $resource->id]);
        });
    }
}
