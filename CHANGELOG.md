# Changelog

All notable changes to `dashed-ternair` will be documented in this file.

## v4.0.5 - 2026-05-05

### Added
- `Classes\FormApis\NewsletterAPI` implementeert nu `Dashed\DashedEcommerceCore\Contracts\SupportsEmailBackfill`. Nieuwe `syncEmail(string $email, ?string $firstName, ?string $lastName, array $api): array` voegt een (email, voornaam, achternaam) toe aan de geconfigureerde Ternair ezine (`EzineCode` uit het API-config-blok) via `subscription/newsletter`. Gebruikt dezelfde `ternair_api_username` / `ternair_api_password` / `ternair_x_api_application_header` Customsettings als de bestaande `dispatch`-paden, zodat de nieuwe `OrderSettingsPage` "Bestaande e-mails synchroniseren"-actie in dashed-ecommerce-core v4.9.0 kan backfillen. Vereist dashed-ecommerce-core v4.9.0+.

## v4.0.4 - 2026-05-02

### Added
- `Classes\PopupApis\PopupAPI`: nieuwe provider-class voor `dashed-popups` newsletter-sync. `dispatch(PopupView, array)` doet POST naar `subscription/newsletter` met `Email`, `EzineCode`, `IpAddress`, `SendOptinMail`, `SendConfirmationMail` en `Properties` (incl. `taalcode`). Failures gooien een `RuntimeException` met body-context.
- Registratie in `popupApiClasses` builder onder key `'ternair-popup-api'`. Vereist `dashed-forms` v4.0.22+.

## 1.0.0 - 202X-XX-XX

- initial release
