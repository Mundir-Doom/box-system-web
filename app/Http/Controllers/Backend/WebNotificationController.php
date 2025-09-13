<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Services\PushNotificationService;
use Illuminate\Http\Request;
use App\Models\User;

class WebNotificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Short-circuit if token is unchanged
        if ($user->web_token === $request->token) {
            return response()->json(['message' => 'Token unchanged'], 200);
        }

        $user = User::find($user->id);
        $user->web_token = $request->token;
        $user->save();
        try {
            $request->request->add(['device_token'  => $request->token, 'topic' => $user->email]);
            app(PushNotificationService::class)->fcmSubscribe($request);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Subscription failed'], 500);
        }
        return response()->json(['message' => 'Token stored'], 200);
    }


}
