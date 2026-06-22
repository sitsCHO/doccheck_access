# DocCheck Access

TYPO3 extension scaffold for DocCheck-based access handling.

## Purpose

This extension prepares a TYPO3-compatible foundation for a DocCheck login flow. It provides a dedicated content element, PSR-15 middlewares for login and callback routes, and service classes for the later DocCheck OAuth implementation.

The actual DocCheck communication and token validation are intentionally not implemented yet.

## Architecture

- Content element `doccheckaccess_login` renders a login button.
- `LoginMiddleware` handles `/doccheck-access/login/`, stores request context and the desired success target in the TYPO3 frontend session, and redirects to a placeholder authorization URL.
- `CallbackMiddleware` handles `/doccheck-access/callback/`, prepares code handling, calls the API service placeholder, and redirects to the session-based success target or the configured failure target.
- `DocCheckApiService`, `FrontendLoginService`, and `ConfigurationService` provide the service boundaries for the later implementation.

## TYPO3 Versions

The scaffold targets TYPO3 11.5 LTS through TYPO3 14.x and avoids Extbase plugins, backend modules, Site Sets, and TYPO3-14-only APIs.

## Installation

Install the package as `doc2k/doccheck-access` and activate the extension key `doccheck_access`.

No files outside this extension have been changed. If the project requires repository-level integration later, add the package to the root Composer setup and run the TYPO3 database compare for the `tt_content` fields.

## Current Limitations

- No real DocCheck authorization URL is generated yet.
- No token exchange with DocCheck is performed yet.
- No frontend user is logged in yet.
- The callback currently prepares the code exchange placeholder and redirects according to the scaffolded flow.
