<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Sends mail via Brevo's HTTPS Transactional Email API instead of SMTP.
 *
 * Render's free web services block outbound traffic on SMTP ports
 * (25/465/587) entirely, so any SMTP-based mailer times out there
 * regardless of provider or credentials. This transport talks to
 * api.brevo.com over plain HTTPS (port 443), which isn't blocked.
 */
class BrevoApiTransport extends AbstractTransport
{
    public function __construct(private readonly string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (! $email instanceof Email) {
            throw new RuntimeException('BrevoApiTransport only supports Symfony MIME Email messages.');
        }

        $from = $email->getFrom();

        if (empty($from)) {
            throw new RuntimeException('BrevoApiTransport requires a "from" address.');
        }

        $payload = array_filter([
            'sender' => $this->addressToArray($from[0]),
            'to' => array_map(fn (Address $a) => $this->addressToArray($a), $email->getTo()),
            'subject' => (string) $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
            'textContent' => $email->getTextBody(),
        ]);

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new RuntimeException(
                'Brevo API email send failed ('.$response->status().'): '.$response->body()
            );
        }
    }

    private function addressToArray(Address $address): array
    {
        return array_filter([
            'email' => $address->getAddress(),
            'name' => $address->getName() ?: null,
        ]);
    }

    public function __toString(): string
    {
        return 'brevo+api://api.brevo.com';
    }
}
