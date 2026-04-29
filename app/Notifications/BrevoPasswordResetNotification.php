<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use RuntimeException;

class BrevoPasswordResetNotification
{
    public function __construct(
        private readonly string $token
    ) {
    }

    public function send(User $user): void
    {
        $apiKey = config('services.brevo.key');

        if (!$apiKey) {
            throw new RuntimeException('Brevo API key is not configured.');
        }

        $resetUrl = URL::route('password.reset', [
            'token' => $this->token,
            'email' => $user->getEmailForPasswordReset(),
        ]);

        $appName = config('app.name', 'Money Market');
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name', $appName);
        $userName = $user->name ?: 'User';

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'api-key' => $apiKey,
            'content-type' => 'application/json',
        ])->post(config('services.brevo.endpoint'), [
            'sender' => [
                'name' => $fromName,
                'email' => $fromAddress,
            ],
            'to' => [[
                'email' => $user->getEmailForPasswordReset(),
                'name' => $userName,
            ]],
            'subject' => 'Reset your '.$appName.' password',
            'htmlContent' => $this->htmlContent($userName, $resetUrl, $appName),
            'textContent' => $this->textContent($resetUrl, $appName),
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Brevo password reset email failed: '.$response->body());
        }
    }

    private function htmlContent(string $userName, string $resetUrl, string $appName): string
    {
        return '
            <div style="font-family: Arial, sans-serif; color: #111827; line-height: 1.5;">
                <h2 style="margin-bottom: 16px;">Reset your password</h2>
                <p>Hello '.e($userName).',</p>
                <p>We received a request to reset your '.e($appName).' password.</p>
                <p style="margin: 24px 0;">
                    <a href="'.e($resetUrl).'" style="background: #2563eb; color: #ffffff; padding: 12px 18px; text-decoration: none; border-radius: 6px; display: inline-block;">
                        Reset Password
                    </a>
                </p>
                <p>This link will expire in '.config('auth.passwords.users.expire', 60).' minutes.</p>
                <p>If you did not request a password reset, you can ignore this email.</p>
                <p>Regards,<br>'.e($appName).'</p>
            </div>
        ';
    }

    private function textContent(string $resetUrl, string $appName): string
    {
        return "Reset your {$appName} password using this link: {$resetUrl}\n\n"
            .'This link will expire in '.config('auth.passwords.users.expire', 60)." minutes.\n\n"
            .'If you did not request a password reset, you can ignore this email.';
    }
}
