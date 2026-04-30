<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'name',
        'email',
        'role',
        'status',
        'join_date',
        'last_active',
        'photo',
        'organ_name',
        'password',
    ];

    // A member has many custom attribute values
    public function customAttributes()
    {
        return $this->hasMany(CustomAttributeValue::class);
    }

    // Helper method — get a single custom attribute value by field name
    // Usage: $member->getCustomAttribute('department')
    public function getCustomAttribute(string $name): ?string
    {
        return $this->customAttributes
            ->first(fn($v) => $v->definition->name === $name)
            ?->value;
    }
}