<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use SoftDeletes;
    protected $table = 'subscribers';
    protected $hidden = array('created_at', 'updated_at','deleted_at');

    public function fields()
    {
        return $this->belongsToMany(Fields::class, 'subscriber_fields', 'subscriber_id', 'field_id')
            ->withPivot('value');
    }
    public function show()
    {
        $formattedResult = [];
        $formattedResult['id'] = $this->id;
        $formattedResult['email'] = $this->email;
        $formattedResult['name'] = $this->name;
        $formattedResult['state'] = States::find($this->state_id)->name;
        $formattedResult['fields'] = [];
        foreach ($this->fields as $field) {
            $formattedResult['fields'][$field->title] = $field->pivot->value;
        }

        return $formattedResult;
    }
}
