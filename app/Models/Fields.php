<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fields extends Model
{
    use SoftDeletes;
    protected $table='fields';
    protected $hidden = array('created_at', 'updated_at','deleted_at');
    static $types = ['date','number','string','boolean'];
    public function show(){
        $returnArray = [];
        $returnArray['id'] = $this->id;
        $returnArray['title'] = $this->title;
        $returnArray['type'] = Self::getType($this->type);
        return $returnArray;
    }

    public static function getType($type){
        return self::$types[$type];
    }
    public static function StringToId($type){
        return array_search($type,self::$types);
    }
    public function subscribers(){
        return $this->belongsToMany(Subscriber::class,'subscriber_fields', 'field_id','subscriber_id')->withPivot('value');
    }
}
