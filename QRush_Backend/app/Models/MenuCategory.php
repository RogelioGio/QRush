<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{

    protected $fillable = [
        'name',
        'is_active',
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_category_id');
    }
}
