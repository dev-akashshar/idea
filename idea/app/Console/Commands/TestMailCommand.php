<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mail:test {email?}';

    /**
     * The console command description.
     */
    protected $description = 'Send a test email to verify SMTP configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $to = $this->argument('email') ?? config('mail.from.address');

        $this->info("Attempting to send test email to: {$to}");
        $this->line('Mailer: ' . config('mail.default'));
        $this->line('Host: ' . config('mail.mailers.smtp.host') . ':' . config('mail.mailers.smtp.port'));
        $this->line('Encryption: ' . (config('mail.mailers.smtp.encryption') ?? 'none'));
        $this->line('From: ' . config('mail.from.address') . ' (' . config('mail.from.name') . ')');

        try {
            Mail::raw('This is a test email from your Idea App to verify that cPanel SMTP is working correctly.', function ($message) use ($to) {
                $message->to($to)->subject('SMTP Verification Test - Idea App');
            });

            $this->info('SUCCESS: Test email was sent successfully!');
            return 0;
        } catch (\Throwable $e) {
            $this->error('FAILED: Could not send email via SMTP.');
            $this->error('Error Message: ' . $e->getMessage());
            return 1;
        }
    }
}
