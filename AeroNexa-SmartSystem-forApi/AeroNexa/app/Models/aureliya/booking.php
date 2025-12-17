<?php
namespace App\Models\aureliya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $connection = 'aureliya'; 
    protected $table = 'bookings';
    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        '_id', 
        'user_id', 
        'property_id', 
        'check_in', 
        'check_out', 
        'guests', 
        'total_price', 
        'status', 
        'payment_status', // <--- ADDED THIS
        'payment_method',
        'transaction_code' 
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}