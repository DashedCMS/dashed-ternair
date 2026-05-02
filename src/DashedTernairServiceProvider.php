<?php

namespace Dashed\DashedTernair;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Dashed\DashedTernair\Livewire\Confirm;
use Dashed\DashedTernair\Livewire\Unsubscribe;
use Dashed\DashedTernair\Classes\FormWebhooks\Webhook;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Dashed\DashedTernair\Classes\FormApis\NewsletterAPI;
use Dashed\DashedTernair\Classes\PopupApis\PopupAPI as PopupNewsletterAPI;
use Dashed\DashedTernair\Filament\Pages\Settings\DashedTernairSettingsPage;

class DashedTernairServiceProvider extends PackageServiceProvider
{
    public static string $name = 'dashed-ternair';

    public function bootingPackage()
    {
        Livewire::component('dashed-ternair.newsletter-confirm', Confirm::class);
        Livewire::component('dashed-ternair.newsletter-unsubscribe', Unsubscribe::class);

        cms()->registerSettingsDocs(
            page: \Dashed\DashedTernair\Filament\Pages\Settings\DashedTernairSettingsPage::class,
            title: 'Ternair instellingen',
            intro: 'Op deze pagina koppel je jouw webshop aan Ternair, een Nederlands marketing automation platform. Via deze koppeling kan je webshop webhooks van Ternair verwerken, bijvoorbeeld voor het bevestigen van een aanmelding of het verwerken van een afmelding. Werk je met meerdere sites? Dan kun je per site eigen Ternair credentials instellen.',
            sections: [
                [
                    'heading' => 'Wat kun je hier instellen?',
                    'body' => <<<MARKDOWN
Op deze pagina vul je in:

- De API credentials voor de verbinding met Ternair (header, gebruikersnaam en wachtwoord).
- Optionele pagina\'s waar bezoekers naartoe gaan na een bevestiging of afmelding.
MARKDOWN,
                ],
                [
                    'heading' => 'Hoe zet je dit op?',
                    'body' => <<<MARKDOWN
1. Neem contact op met je Ternair contactpersoon en vraag of er een API integratie voor jouw webshop kan worden aangemaakt.
2. Je ontvangt van Ternair een X-API-Application header waarde, een gebruikersnaam en een wachtwoord.
3. Vul deze drie waarden in op deze pagina.
4. Kies eventueel een pagina waar de gebruiker naartoe gaat nadat een aanmelding is bevestigd.
5. Kies eventueel een pagina waar de gebruiker naartoe gaat na een afmelding.
6. Sla de instellingen op.
MARKDOWN,
                ],
            ],
            fields: [
                'X-API-Application header' => 'De waarde van de X-API-Application header. Deze waarde krijg je van Ternair aangeleverd op het moment dat zij een API integratie voor jou aanmaken.',
                'API gebruikersnaam' => 'De API gebruikersnaam die je van Ternair hebt ontvangen.',
                'API wachtwoord' => 'Het API wachtwoord dat bij de gebruikersnaam hoort. Behandel dit wachtwoord als gevoelige informatie en deel het niet met derden.',
                'Redirect na bevestiging' => 'Optioneel. De pagina waar een bezoeker naartoe wordt gestuurd nadat een aanmelding via Ternair is bevestigd. Bijvoorbeeld een bedankt-pagina. Laat leeg als je geen redirect wilt.',
                'Redirect na afmelding' => 'Optioneel. De pagina waar een bezoeker naartoe wordt gestuurd nadat een afmelding is verwerkt. Laat leeg als je geen redirect wilt.',
            ],
            tips: [
                'Weet je niet zeker welke waarden je moet invullen? Vraag je Ternair contactpersoon om de juiste credentials.',
            ],
        );
    }

    public function configurePackage(Package $package): void
    {
        $this->publishes([
            __DIR__ . '/../resources/templates' => resource_path('views/' . config('dashed-core.site_theme', 'dashed')),
        ], 'dashed-templates');

        cms()->registerSettingsPage(DashedTernairSettingsPage::class, 'Dashed Ternair', 'bell', 'Beheer instellingen voor Ternair');

        forms()->builder(
            'webhookClasses',
            array_merge(forms()->builder('webhookClasses'), [
                'ternair-webhook-1' => [
                    'name' => 'Ternair webhook',
                    'class' => Webhook::class,
                ],
            ])
        );

        forms()->builder(
            'apiClasses',
            array_merge(forms()->builder('apiClasses'), [
                'ternair-newsletters-api' => [
                    'name' => 'Ternair newsletter API',
                    'class' => NewsletterAPI::class,
                ],
            ])
        );

        forms()->builder(
            'popupApiClasses',
            array_merge(forms()->builder('popupApiClasses'), [
                'ternair-popup-api' => [
                    'name' => 'Ternair nieuwsbrief',
                    'class' => PopupNewsletterAPI::class,
                ],
            ])
        );

        $package
            ->name('dashed-ternair');

        cms()->builder('plugins', [
            new DashedTernairPlugin(),
        ]);
    }
}
