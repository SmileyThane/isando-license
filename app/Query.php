<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'queries';

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

    public function scopeFilterBySubjectOrBody($q, $keyword = null)
    {
        if (! $keyword) {
            return $q;
        }

        return $q->where(function($q) use($keyword) {
        	$q1->where('subject', 'like', '%'.$keyword.'%')->orWhere('body', 'like', '%'.$keyword.'%');
        });
    }

    public function scopeFilterByFirstName($q, $first_name = null)
    {
        if (! $first_name) {
            return $q;
        }

        return $q->where('first_name', 'like', '%'.$first_name.'%');
    }

    public function scopeFilterByLastName($q, $last_name = null)
    {
        if (! $last_name) {
            return $q;
        }

        return $q->where('last_name', 'like', '%'.$last_name.'%');
    }

    public function scopeFilterByStatus($q, $status = null)
    {
        if (! $status) {
            return $q;
        }

        return $q->where('status', '=', $status);
    }

    public function scopeFilterByEmail($q, $email = null)
    {
        if (! $email) {
            return $q;
        }

        return $q->where('email', 'like', '%'.$email.'%');
    }

    public function scopeFilterByPhone($q, $phone = null)
    {
        if (! $phone) {
            return $q;
        }

        return $q->where('phone', 'like', '%'.$phone.'%');
    }

    public function scopeDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }
}
