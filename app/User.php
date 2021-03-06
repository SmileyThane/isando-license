<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','activation_token'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function profile()
    {
        return $this->hasOne('App\Profile');
    }

    public function userPreference()
    {
        return $this->hasOne('App\UserPreference');
    }

    public function getNameAttribute()
    {
        return $this->Profile->first_name.' '.$this->Profile->last_name;
    }

    public function getNameWithEmailAttribute()
    {
        return $this->Profile->first_name.' '.$this->Profile->last_name.' ('.$this->email.')';
    }

    function getEmailAttribute($value)
    {
        return isTestMode() ? hideEmail($value) : $value;
    }

    public function scopeFilterByEmail($q, $email = null)
    {
        if (! $email) {
            return $q;
        }

        return $q->where('email', 'like', '%'.$email.'%');
    }

    public function scopeFilterByFirstName($q, $first_name = null)
    {
        if (! $first_name) {
            return $q;
        }

        return $q->whereHas('profile', function ($q1) use ($first_name) {
            $q1->where('first_name', 'like', '%'.$first_name.'%');
        });
    }

    public function scopeFilterByLastName($q, $last_name = null)
    {
        if (! $last_name) {
            return $q;
        }

        return $q->whereHas('profile', function ($q1) use ($last_name) {
            $q1->where('last_name', 'like', '%'.$last_name.'%');
        });
    }

    public function scopeFilterByRoleId($q, $role_id = null)
    {
        if (! $role_id) {
            return $q;
        }

        return $q->whereHas('roles', function ($q) use ($role_id) {
            $q->where('role_id', '=', $role_id);
        });
    }

    public function scopeFilterByStatus($q, $status = null)
    {
        if (! $status) {
            return $q;
        }

        return $q->where('status', '=', $status);
    }

    public function scopeCreatedAtDateBetween($q, $dates)
    {
        if ((! $dates['start_date'] || ! $dates['end_date']) && $dates['start_date'] <= $dates['end_date']) {
            return $q;
        }

        return $q->where('created_at', '>=', getStartOfDate($dates['start_date']))->where('created_at', '<=', getEndOfDate($dates['end_date']));
    }
}
