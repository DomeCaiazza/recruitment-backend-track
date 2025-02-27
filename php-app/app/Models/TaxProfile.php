<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class TaxProfile extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'user_id',
        'tax_code',
        'address',
        'vat_number',
        'business_name'
    ];

    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($model) {
            if (self::where('user_id', $model->user_id)
                    ->where('tax_code', $model->tax_code)
                    ->where('vat_number', $model->vat_number)
                    ->exists()) {
                throw new \Exception('Tax profile and VAT number must be unique for user');
            }  
        });
    }

    protected static function booted()
    {
        static::created(function ($resource) {
            Log::info('TaxProfile created', ['id' => $resource->id, 'user' => $resource->user_id]);
        });

        static::updated(function ($resource) {
            Log::info('TaxProfile updated',
            ['id' => $resource->id,
            'tax_code' => $resource->tax_code,
            'vat_number' => $resource->vat_number,
            'address' => $resource->address,
            'business_name' => $resource->business_name]);
        });

        static::deleted(function ($resource) {
            Log::info('TaxProfile deleted', ['id' => $resource->id]);
        });
    }

}
