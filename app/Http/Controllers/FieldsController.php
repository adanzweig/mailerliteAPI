<?php

namespace App\Http\Controllers;

use App\Models\Fields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FieldsController extends Controller
{
    public function getAllFields()
    {
        $fields = Auth::user()->fields()->get();
        $result = [];
        foreach ($fields as $field) {
            $result[] = $field->show();
        }
        return response()->json(['success'=>true,'data'=>$result]);
    }
    public function getAllFieldTypes()
    {
        return response()->json(['success'=>true,'data'=>Fields::$types]);
    }
    public function createFields(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type'=> 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'data'=>'Error validating fields'], 400);
        }
        if (empty($request->get('title')) || empty($request->get('type'))) {
            return response()->json(['success'=>false,'data'=>'Missing parameters'], 400);
        }
        $fields = Auth::user()->fields()->where('title', $request->get('title'))->first();
        if (empty($fields)) {
            $fields = new Fields();
            $fields->title = $request->get('title');
            $fields->user_id = Auth::id();
            $type = Fields::StringToId($request->get('type'));
            if (empty($type) && $type !== 0) {
                return response()->json(['success'=>false,'data'=>'Incorrect type'], 404);
            }
            $fields->type = $type;
            $fields->save();
            return response()->json(['success'=>true,'data'=>$fields->show()]);
        } else {
            return response()->json(['success'=>false,'data'=>'Repeated field'], 409);
        }
    }
    public function updateField(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type'=> 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'data'=>'Error validating fields'], 400);
        }
        if (empty($request->get('title')) || empty($request->get('type'))) {
            return response()->json(['success'=>false,'data'=>'Missing parameters'], 400);
        }
        $fields = Auth::user()->fields()->where('title', $request->get('title'))->first();
        if (empty($fields)) {
            return response()->json(['success'=>false,'data'=>'Field does not exist'], 404);
        }
        $type = Fields::StringToId($request->get('type'));
        if (empty($type) && $type !== 0) {
            return response()->json(['success'=>false,'data'=>'Incorrect type'], 404);
        }
        $fields->type = $type;
        $fields->save();
        return response()->json(['success'=>true,'data'=>$fields->show()]);
    }
    public function deleteField(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'data'=>'Error validating fields'], 400);
        }
        if (empty($request->get('title'))) {
            return response()->json(['success'=>false,'data'=>'Missing parameters'], 400);
        }
        $field = Auth::user()->fields()->where('title', $request->get('title'))->first();
        if (empty($field)) {
            return response()->json(['success'=>false,'data'=>'Field does not exist'], 404);
        }
        $field->subscribers()->detach();
        $field->delete();
        return response()->json(['success'=>true,'data'=>[]]);
    }

}
