<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'currencies';

    public function getDetailAttribute()
    {
        return $this->name.' ('.$this->symbol.')';
    }

    public function instances()
    {
        return $this->hasMany('App\Instance');
    }

    public function planPrices()
    {
        return $this->hasMany('App\PlanPrice');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Subscription');
    }

    public function scopeFilterById($q, $id)
    {
        if (! $id) {
            return $q;
        }

        return $q->where('id', '=', $id);
    }

    public function scopeFilterByIsDefault($q, $is_default)
    {
        return $q->where('is_default', '=', $is_default);
    }

    public function scopeFilterByName($q, $name)
    {
        if (! $name) {
            return $q;
        }

        return $q->where('name', 'like', '%'.$name.'%');
    }
}
