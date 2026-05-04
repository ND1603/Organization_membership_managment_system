<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomAttributeValue extends Model
{
    protected $fillable = [
        'user_id',
        'custom_attribute_definition_id',
        'value',
    ];

    // Which field definition this value belongs to
    public function definition()
    {
        return $this->belongsTo(
            CustomAttributeDefinition::class,
            'custom_attribute_definition_id'
        );
    }

    // Which user this value belongs to
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}