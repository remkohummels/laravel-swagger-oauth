<?php
namespace App\Listeners;
use Illuminate\Auth\Events\Failed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User as User;
use MikeMcLin\WpPassword\Facades\WpPassword;
use Auth;
use Hash;
class LogFailedAuthenticationAttempt
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    /**
     * Handle the event.
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        $user = User::where('email',$event->credentials['email'])->first();
        if ( $user ) {
            if ( WpPassword::check($event->credentials['password'], $user->password ) ) {
                Auth::login($user);
                $user->password = Hash::make($event->credentials['password']);
                $user->save();
            }
        }
    }
}