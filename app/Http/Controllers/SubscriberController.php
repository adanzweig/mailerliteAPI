<?php

namespace App\Http\Controllers;

use App\Models\Fields;
use App\Models\States;
use App\Models\Subscriber;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    public function getSubscribers($email = null)
    {
        if (empty($email)) {
            $subscribers = Auth::user()->subscribers;
            $result = [];
            foreach ($subscribers as $subscriber) {
                $result[] = $subscriber->show();
            }
            return response()->json(['success'=>true,'data'=>$result]);
        } else {
            $subscriber = Subscriber::where('email', $email)->where('user_id', Auth::id())->first();
            if (empty($subscriber)) {
                return response()->json(['success'=>false,'data'=>'Subscriber not found'], 404);
            }
            return response()->json(['success'=>true,'data'=>$subscriber->show()]);
        }
    }

    public function createSubscriber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|indisposable',
            'name' => 'string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'data'=>'Error validating fields'], 400);
        }
        if (!empty($request->get('email'))) {
            $subscriber = Subscriber::where('email', $request->get('email'))->where('user_id', Auth::id())->first();
            if (empty($subscriber)) {
                $subscriber = new Subscriber();
                if (!empty($request->get('name'))) {
                    $subscriber->name = $request->get('name');
                }
                $subscriber->email = $request->get('email');
                $subscriber->user_id = Auth::id();
                if (empty($request->get('state'))) {
                    $subscriber->state_id = 5;
                } else {
                    $subscriber->state_id = $request->get('state');
                }
                $subscriber->save();

                if (!empty($request->get('fields'))) {
                    foreach ($request->get('fields') as $fields) {
                        foreach ($fields as $title => $value) {
                            $field = Auth::user()->fields()->where('title', $title)->first();
                            if (empty($field)) {
                                $field = new Fields();
                                $field->title = $title;
                                $field->user_id = Auth::id();
                                $field->save();
                            }
                            $subscriber->fields()->attach($field, ['value' => $value]);
                        }
                    }
                }
            } else {
                return response()->json(['success'=>false,'data'=>'Repeated subscriber in account'], 409);
            }
            return response()->json(['success'=>true,'data'=>$subscriber->show()]);
        } else {
            return response()->json(['success'=>false,'data'=>'Email missing'], 404);
        }
    }
    public function editSubscriber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|indisposable',
            'name' => 'string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'data'=>'Error validating fields'], 400);
        }
        if (!empty($request->get('email'))) {
            $subscriber = Subscriber::where('email', $request->get('email'))->where('user_id', Auth::id())->first();
            if (empty($subscriber)) {
                return response()->json(['success'=>false,'data'=>'Subscriber not found'], 404);
            }
            if (!empty($request->get('name'))) {
                $subscriber->name = $request->get('name');
            }
            if (!empty($request->get('state'))) {
                if (is_numeric($request->get('state'))) {
                    $subscriber->state_id = $request->get('state');
                } else {
                    $state = States::where('name', $request->get('state'))->first();
                    if (empty($state)) {
                        return response()->json(['success'=>false,'data'=>'State not found'], 404);
                    }
                    $subscriber->state_id = $state->id;
                }


            }
            $subscriber->save();

            if (!empty($request->get('fields'))) {
                foreach ($request->get('fields') as $fields) {
                    foreach ($fields as $title => $value) {
                        $field = Auth::user()->fields()->where('title', $title)->first();
                        if (empty($field)) {
                            $field = new Fields();
                            $field->title = $title;
                            $field->user_id = Auth::id();
                            $field->save();
                        }
                        if ($subscriber->fields()->where('field_id', $field->id)) {
                            $subscriber->fields()->detach($field);
                        }
                        $subscriber->fields()->attach($field, ['value' => $value]);
                    }
                }
            }
            return response()->json(['success'=>true,'data'=>$subscriber->show()]);
        } else {
            return response()->json(['success'=>false,'data'=>'Email missing'], 404);
        }
    }
    public function deleteSubscriber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success'=>false,'data'=>'Error validating fields'], 400);
        }
        if (!empty($request->get('email'))) {
            $subscriber = Subscriber::where('email', $request->get('email'))->where('user_id', Auth::id())->first();
            if (empty($subscriber)) {
                return response()->json(['success'=>false,'data'=>'Subscriber not found'], 404);
            }
            $subscriber->fields()->detach();

            $subscriber->delete();
            return response()->json(['success'=>true,'data'=>[]]);
        } else {
            return response()->json(['success'=>false,'data'=>'Email missing'], 404);
        }
    }
}
