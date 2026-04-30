<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomAttributeValue extends Model
{
    protected $fillable = [
        'member_id',
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

    // Which member this value belongs to
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}