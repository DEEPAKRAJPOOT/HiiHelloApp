<?php

namespace App\Models;

use App\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
    	'section_id', 'title',
        'route', 'params',
        'icon', 'image', 'icon_type',
        'allowed_permissions', 'sequence', 'is_display', 'is_active'
    ];

    public function section()
    {
        return $this->belongsTo('App\Models\Section');
    }

    public function quickLinks()
    {
        return $this->hasOne(QuickLink::class, 'role_id', 'id')->where(['admin_id' => Auth::id()]);
    }

}
