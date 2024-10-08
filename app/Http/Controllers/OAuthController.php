<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OAuthController extends Controller
{
    public function redirect()
    {
        
        $queries = http_build_query([
            'client_id' => '8',
            'redirect_uri' => 'http://127.0.0.1:8001/oauth/callback',
            'response_type' => 'code',
            // 'scope' => 'view-posts',
            // 'scope' => 'view-posts view-user'
        ]);
    
        // Faire attention avec localhost et 127.0.0.1 ne pas mettre les deux application sur localhost
        return redirect('http://localhost:8000/oauth/authorize?'. $queries);
    }


    public function callback(Request $request)
    {
       
        $response = Http::post('http://127.0.0.1:8000/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => '8',
            'client_secret' => 'u05vZ977kjaBa39MZeNpFG9BT0abVNaZdkNT40R9',
            'redirect_uri' => 'http://127.0.0.1:8001/oauth/callback',
            'code' => $request->code
        ]);
        
        $response = $response->json();

        // dd(auth()->user());
        // dd($request);
        // dd($request->user());
        if ($request->user()) {
            $request->user()->token()->delete();
        }
        

        // dd($response);
        auth()->user()->token()->create([
            'access_token' => $response['access_token'],
            'expires_in' => $response['expires_in'],
            'refresh_token' => $response['refresh_token']
        ]);

        return redirect('/dashboard');
    }

    public function refresh(Request $request)
    {
        $response = Http::post('http://127.0.0.1:8000/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' =>  auth()->user()->token->refresh_token,
            'client_id' => '8',
            'client_secret' => 'u05vZ977kjaBa39MZeNpFG9BT0abVNaZdkNT40R9',
            'redirect_uri' => 'http://127.0.0.1:8001/oauth/callback',
            'scope' => 'view-posts',
    
        ]);
        
        $response = $response->json();
// dd($response);
        auth()->user()->token()->update([
            'access_token' => $response['access_token'],
            'expires_in' => $response['expires_in'],
            'refresh_token' => $response['refresh_token']
        ]);

        return redirect('/dashboard');
    }
}
