# Changelog

All notable changes to `dashed-ternair` will be documented in this file.

## v4.0.4 - 2026-05-02

### Added
- `Classes\PopupApis\PopupAPI`: nieuwe provider-class voor `dashed-popups` newsletter-sync. `dispatch(PopupView, array)` doet POST naar `subscription/newsletter` met `Email`, `EzineCode`, `IpAddress`, `SendOptinMail`, `SendConfirmationMail` en `Properties` (incl. `taalcode`). Failures gooien een `RuntimeException` met body-context.
- Registratie in `popupApiClasses` builder onder key `'ternair-popup-api'`. Vereist `dashed-forms` v4.0.22+.

## 1.0.0 - 202X-XX-XX

- initial release
