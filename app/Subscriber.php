<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'subscribers';

    function getEmailAttribute($value)
    {
        return isTestMode() ? hideEmail($value) : $value;
    }

    public function scopeFilterById($q, $id = null)
    {
        if (! $id) {
            return $q;
        }

        return $q->whereId($id);
    }
    
    public function scopeFilterByEmail($q, $email)
    {
        if (! $email) {
            return $q;
        }

        return $q->where('email', '=', $email);
    }

    public function scopeFilterByStatus($q, $status = null)
    {
        if (! $status) {
            return $q;
        }

        return $q->where('status', '=', $status);
    }

    public function scopeDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }
}
