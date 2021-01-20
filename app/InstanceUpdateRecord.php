<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstanceUpdateRecord extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'instance_update_records';
    
    public function instance()
    {
        return $this->belongsTo('App\Instance');
    }
}
