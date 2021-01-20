<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'subscriptions';
    
    public function plan()
    {
        return $this->belongsTo('App\Plan');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function instance()
    {
        return $this->belongsTo('App\Instance');
    }

    public function getSubscriptionNumberAttribute()
    {
        return ($this->number) ? $this->prefix.str_pad($this->number, config('config.subscription_invoice_number_digit'), '0', STR_PAD_LEFT) : '';
    }

    public function scopeFilterId($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByUuid($q, $uuid)
    {
        if (! $uuid) {
            return $q;
        }

        return $q->where('uuid', '=', $uuid);
    }

    public function scopeFilterByStatus($q, $status = null)
    {
        if (! $status) {
            return $q;
        }

        return $q->where('status', '=', $status);
    }

    public function scopeFilterByInstanceId($q, $instance_id)
    {
        if (! $instance_id) {
            return $q;
        }

        return $q->where('instance_id', '=', $instance_id);
    }

    public function scopeFilterByPlanId($q, $plan_id)
    {
        if (! $plan_id) {
            return $q;
        }

        return $q->where('plan_id', '=', $plan_id);
    }

    public function scopeFilterByCurrencyId($q, $currency_id)
    {
        if (! $currency_id) {
            return $q;
        }

        return $q->where('currency_id', '=', $currency_id);
    }

    public function scopeDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }
}
