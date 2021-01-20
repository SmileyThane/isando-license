<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstanceDatabaseRecord extends Model
{
    protected $fillable = [];
    protected $primaryKey = 'id';
    protected $table = 'instance_database_records';
    
    public function instance()
    {
        return $this->hasOne('App\Instance','db_id');
    }
}
