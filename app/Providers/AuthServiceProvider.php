<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        // アクセストークンの制限時間を1時間に設定している
        Passport::tokensExpireIn(Carbon::now()->addHours(1));
 
        // リフレッシュトークンの制限時間を10日に設定している
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(10));

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $frontendUrl = env('FRONT_URL');
            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );            
            $url = $frontendUrl . 'email/verify/' . urlencode($verifyUrl);
            return (new MailMessage)
                ->subject('メールアドレスの確認')
                ->line('下のボタンをクリックして、メールアドレスを確認してください。')
                ->action('Verify Email Address', $url);
        });

        
    }
}
