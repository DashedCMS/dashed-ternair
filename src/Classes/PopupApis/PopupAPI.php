<?php

namespace Dashed\DashedTernair\Classes\PopupApis;

use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Dashed\DashedPopups\Models\PopupView;
use Dashed\DashedCore\Models\Customsetting;

class PopupAPI
{
    public static function dispatch(PopupView $view, array $api): void
    {
        $username = Customsetting::get('ternair_api_username');
        $password = Customsetting::get('ternair_api_password');

        if (! $username || ! $password) {
            return;
        }

        $data = [
            'IpAddress' => $view->ip_address,
            'EzineCode' => $api['EzineCode'] ?? null,
            'SendOptinMail' => ! empty($api['SendOptinMail']) ? 1 : 0,
            'SendConfirmationMail' => ! empty($api['SendConfirmationMail']) ? 1 : 0,
            'Email' => $view->email,
            'Properties' => [
                ['Key' => 'taalcode', 'Value' => $view->locale ?? app()->getLocale()],
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-API-Application' => Customsetting::get('ternair_x_api_application_header'),
        ])
            ->withBasicAuth($username, $password)
            ->post('https://campaign3-interact-api.ternairsoftware.com/subscription/newsletter', $data);

        if ($response->failed()) {
            throw new \RuntimeException('Ternair error for ezine '.($api['EzineCode'] ?? '?').': '.$response->body());
        }
    }

    public static function formFields(): array
    {
        return [
            TextInput::make('EzineCode')
                ->label('Ezine code')
                ->required(),
            Toggle::make('SendOptinMail')
                ->label('Stuur optin mail'),
            Toggle::make('SendConfirmationMail')
                ->label('Stuur bevestigingsmail'),
        ];
    }
}
