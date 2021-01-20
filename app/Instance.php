<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'instances';
    protected $hidden = ['activation_token'];
    
    public function database()
    {
        return $this->belongsTo('App\InstanceDatabaseRecord','db_id');
    }
    
    public function plan()
    {
        return $this->belongsTo('App\Plan');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Subscription');
    }

    public function getNameAttribute()
    {
    	return $this->first_name.' '.$this->last_name;
    }

    function getEmailAttribute($value)
    {
        return isTestMode() ? hideEmail($value) : $value;
    }

    public function scopeFilterByPlanId($q, $plan_id)
    {
        if (! $plan_id) {
            return $q;
        }

        return $q->where('plan_id', '=', $plan_id);
    }

    public function scopeFilterByInstanceId($q, $instance_id)
    {
        if (! $instance_id) {
            return $q;
        }

        return $q->where('instance_id', '=', $instance_id);
    }

    public function scopeFilterByCurrencyId($q, $currency_id)
    {
        if (! $currency_id) {
            return $q;
        }

        return $q->where('currency_id', '=', $currency_id);
    }

    public function scopeFilterByEmail($q, $email)
    {
        if (! $email) {
            return $q;
        }

        return $q->where('email', 'like', '%'.$email.'%');
    }

    public function scopeFilterbyStatus($q, $status)
    {
    	if(! $status) {
    		return $q;
    	}

    	if ($status === 'trial')
    		return $q->whereIsTrial(1);
    	elseif ($status === 'expired')
    		return $q->where('date_of_expiry','<',date('Y-m-d'));
        elseif ($status === 'running') {
            return $q->where(function($q1){
                $q1->whereNotIn('status',['pending','suspended','terminated'])->where('date_of_expiry','>',date('Y-m-d'));
            });
        }
    	else
    		return $q->whereStatus($status);
    }

    public function scopeFilterByDomain($q, $domain)
    {
        if (! $domain) {
            return $q;
        }

        return $q->where('domain', 'like', '%'.$domain.'%');
    }

    public function scopeDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }

    public function scopeExpiryDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('date_of_expiry', '>=', getStartOfDate($dates['start_date']))->where('date_of_expiry', '<=', getEndOfDate($dates['end_date']));
    }
}
