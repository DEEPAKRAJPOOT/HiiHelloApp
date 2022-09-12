<?php

namespace App;

use App\Http\Controllers\Admin\HelperController;
use App\Models\QuickLink;
use App\Models\Role;
use App\Notifications\AdminResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name', 'email', 'password','contact_no','permissions'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPassword($token));
    }

    public function getAssetProfileAttribute()
    {
        return !empty($this->profile) ? asset('storage/'.$this->profile) : asset('admin/images/default_profile.jpg');
    }

    public function quickLinks()
    {
        return $this->hasMany(QuickLink::class, 'admin_id', 'id');
    }

}
