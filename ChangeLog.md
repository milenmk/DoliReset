# CHANGELOG DOLIRESET FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## v1.1.1

#### Published: 2026-04-27

- [FIX] PHPStan errors
- Change minimum version of Dolibarr required by module to 19
- Change minimum version of PHP required by module to 8.1

## v1.1

#### Published: 2026-04-11

- Refactored `cleanDocumentsFolder()` and `deleteDatabase()` methods in reset class
- missing token when deleting the files/folders OR the database, triggering the CSRF protection

## 1.0

Initial version
