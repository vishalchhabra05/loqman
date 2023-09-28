<?php
namespace App\Http\Middleware;
use Closure;
class VerifyIfUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(auth()->user()){
            if(auth()->user()->status == 1){
                return $next($request);
            }else{
                return response()->json([
                    'status_code' => 300,
                    'response' => 'error',
                    'message' => 'Your account is temporarily inactive. Please contact Blinker Support.'
                ]); 
            }
        }
    }
}