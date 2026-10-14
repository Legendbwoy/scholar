<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'provider',
        'is_active',
        'sandbox_mode',
        'credentials',
        'from_name',
        'from_address',
        'rate_limit_per_minute',
        'last_tested_at',
        'last_test_status',
        'last_test_message',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sandbox_mode' => 'boolean',
        'credentials' => 'array',
        'rate_limit_per_minute' => 'integer',
        'last_tested_at' => 'datetime',
    ];

    public function getCredential(string $key, $default = null)
    {
        $creds = $this->credentials ?? [];
        return $creds[$key] ?? $default;
    }

    public function getMaskedCredential(string $key): string
    {
        $val = $this->getCredential($key, '');
        if (empty($val)) return '';
        $len = strlen($val);
        if ($len <= 4) return '••••';
        return str_repeat('•', max(4, $len - 4)) . substr($val, -4);
    }

    public static function forChannel(string $channel): self
    {
        return static::firstOrCreate(
            ['channel' => $channel],
            [
                'provider' => match ($channel) {
                    'email' => 'smtp',
                    'sms' => 'twilio',
                    'whatsapp' => 'meta_whatsapp',
                    default => 'custom',
                },
                'is_active' => true,
                'sandbox_mode' => false,
                'from_name' => 'Scholar SMS Academy',
                'from_address' => match ($channel) {
                    'email' => 'notifications@scholar.edu',
                    'sms' => '+15550199',
                    'whatsapp' => '+15550198',
                    default => '',
                },
                'credentials' => match ($channel) {
                    'email' => [
                        'smtp_host' => 'smtp.mailtrap.io',
                        'smtp_port' => '587',
                        'smtp_username' => 'scholar_smtp_demo',
                        'smtp_password' => 'secret_token_key',
                        'smtp_encryption' => 'tls',
                    ],
                    'sms' => [
                        'account_sid' => 'AC_demo_tw_sid_928172948172',
                        'auth_token' => 'tw_auth_token_secret_829183',
                        'sender_id' => 'SCHOLAR_SMS',
                    ],
                    'whatsapp' => [
                        'phone_number_id' => '109283746192837',
                        'business_account_id' => '293847192837465',
                        'access_token' => 'EAAG_meta_permanent_system_token_xyz987',
                        'template_namespace' => 'scholar_sms_alerts',
                    ],
                    default => [],
                },
            ]
        );
    }
}
