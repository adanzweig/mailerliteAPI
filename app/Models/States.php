<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    protected $table = 'states';
    protected $hidden = array('created_at', 'updated_at','deleted_at');

}
