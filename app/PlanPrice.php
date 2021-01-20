<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanPrice extends Model
{
    protected $fillable = ['currency_id','plan_id','frequency'];
    protected $primaryKey = 'id';
    protected $table = 'plan_prices';

    public function plan()
    {
        return $this->belongsTo('App\Plan');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }
}
