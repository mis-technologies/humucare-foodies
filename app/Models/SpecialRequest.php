<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialRequest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'needed_on'  => 'date',
        'replied_at' => 'datetime',
    ];

    const STATUS_NEW       = 0;
    const STATUS_QUOTED    = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_CLOSED    = 3;

    public static function types()
    {
        return [
            'bulk'   => 'Bulk / large portion',
            'party'  => 'Party or event catering',
            'office' => 'Office / group lunch',
            'other'  => 'Something else',
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_NEW       => ['New', 'warning'],
            self::STATUS_QUOTED    => ['Quoted', 'info'],
            self::STATUS_CONFIRMED => ['Confirmed', 'success'],
            self::STATUS_CLOSED    => ['Closed', 'secondary'],
        ];
    }

    public function getTypeNameAttribute()
    {
        return self::types()[$this->request_type] ?? $this->request_type;
    }

    public function getStatusNameAttribute()
    {
        return self::statuses()[$this->status][0] ?? 'Unknown';
    }

    public function getStatusClassAttribute()
    {
        return self::statuses()[$this->status][1] ?? 'secondary';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Named `pending` rather than `new` — `new` is a PHP reserved word. */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }
}
