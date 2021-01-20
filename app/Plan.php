<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['is_default','is_featured'];
    protected $primaryKey = 'id';
    protected $table = 'plans';

    public function planPrice()
    {
        return $this->hasMany('App\PlanPrice');
    }

    public function instances()
    {
        return $this->hasMany('App\Instance');
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

    public function scopeFilterByIsActive($q, $is_active)
    {
        return $q->where('is_active', '=', $is_active);
    }

    public function scopeFilterByIsDefault($q, $is_default)
    {
        return $q->where('is_default', '=', $is_default);
    }

    public function scopeFilterByIsFeatured($q, $is_featured)
    {
        return $q->where('is_featured', '=', $is_featured);
    }

    public function scopeFilterByName($q, $name)
    {
        if (! $name) {
            return $q;
        }

        return $q->where('name', 'like', '%'.$name.'%');
    }

    public function scopeFilterByExactSlug($q, $slug)
    {
        if (! $slug) {
            return $q;
        }

        return $q->where('slug', '=', $slug);
    }
}
