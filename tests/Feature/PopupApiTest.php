<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Dashed\DashedPopups\Models\PopupView;
use Dashed\DashedCore\Models\Customsetting;
use Dashed\DashedTernair\Classes\PopupApis\PopupAPI;

function makeView(array $attributes = []): PopupView
{
    return new PopupView(array_merge([
        'id' => 1,
        'email' => 'visitor@example.com',
        'ip_address' => '127.0.0.1',
        'url' => 'https://example.test/landing',
        'locale' => 'nl',
    ], $attributes));
}

it('posts to the ternair newsletter endpoint with the expected payload', function () {
    Customsetting::$values = [
        'ternair_api_username' => 'svc-user',
        'ternair_api_password' => 'svc-pass',
        'ternair_x_api_application_header' => 'app-header-value',
    ];

    Http::fake([
        'campaign3-interact-api.ternairsoftware.com/*' => Http::response(['ok' => true], 200),
    ]);

    PopupAPI::dispatch(makeView(), [
        'EzineCode' => 'EZ123',
        'SendOptinMail' => true,
        'SendConfirmationMail' => false,
    ]);

    Http::assertSent(function (Request $request) {
        $payload = $request->data();
        $properties = $payload['Properties'] ?? [];
        $taalcode = collect($properties)->firstWhere('Key', 'taalcode')['Value'] ?? null;

        return $request->method() === 'POST'
            && $request->url() === 'https://campaign3-interact-api.ternairsoftware.com/subscription/newsletter'
            && ($payload['Email'] ?? null) === 'visitor@example.com'
            && ($payload['EzineCode'] ?? null) === 'EZ123'
            && ($payload['SendOptinMail'] ?? null) === 1
            && ($payload['SendConfirmationMail'] ?? null) === 0
            && ($payload['IpAddress'] ?? null) === '127.0.0.1'
            && $taalcode === 'nl'
            && $request->header('X-API-Application') === ['app-header-value'];
    });
});

it('throws a RuntimeException when ternair returns a non-2xx response', function () {
    Customsetting::$values = [
        'ternair_api_username' => 'svc-user',
        'ternair_api_password' => 'svc-pass',
    ];

    Http::fake([
        'campaign3-interact-api.ternairsoftware.com/*' => Http::response(['error' => 'Bad ezine'], 400),
    ]);

    expect(fn () => PopupAPI::dispatch(makeView(), ['EzineCode' => 'EZ123']))
        ->toThrow(\RuntimeException::class);
});

it('is a no-op when ternair credentials are missing', function () {
    Customsetting::$values = [];

    Http::fake();

    PopupAPI::dispatch(makeView(), ['EzineCode' => 'EZ123']);

    Http::assertNothingSent();
});
