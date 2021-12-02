<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
	protected $table = 'sections';

	protected $fillable = [
		'name', 'icon', 'image', 'icon_type',
		'sequence', 'is_active',
	];

	public function roles()
	{
		return $this->hasMany('App\Models\Role')->where('is_active', 'y')->orderBy('sequence', 'asc');
	}

	public function scopeActiveSections($query)
	{
		return $query->with('roles')->where('is_active', 'y')->orderBy('sequence', 'asc')->get();
	}
}
