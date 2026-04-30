<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomAttributeDefinition extends Model
{
    protected $fillable = [
        'name',
        'label',
        'type',
        'options',
        'is_required',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'options'     => 'array',   // stored as JSON, returned as PHP array
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
    ];

    // One definition has many member values
    public function values()
    {
        return $this->hasMany(CustomAttributeValue::class);
    }
}