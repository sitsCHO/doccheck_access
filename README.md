# DocCheck Access

TYPO3 extension for DocCheck-based frontend access.

## Purpose

This extension provides DocCheck login handling without Extbase plugins or a backend module. It uses TYPO3 content elements, PSR-15 middlewares, Fluid templates and small service classes.

## TYPO3 Versions

The extension targets TYPO3 11.5 LTS through TYPO3 14.x. It intentionally keeps the classic TYPO3 integration available and does not require Site Sets.

## Content Elements

### DocCheck Login

`doccheckaccess_login` renders a DocCheck login button. The button points to the internal login route:

```text
/doccheck-access/login/?ce={contentElementUid}
```

For translated content elements, the localized content element UID is used. This allows each language version of the login element to define its own success page.

Fields:

- Standard TYPO3 header fields
- Button Label
- Success Page
- Standard Appearance, Language, Access, Categories and Notes tabs

### DocCheck Error Message

`doccheckaccess_error_message` renders the latest DocCheck error message from the frontend session. It is rendered uncached so session-based messages do not get stuck in the page cache.

## Flow

1. The login button calls `/doccheck-access/login/?ce=...`.
2. `LoginMiddleware` validates required admin configuration.
3. The middleware reads the login content element and determines the success page.
4. The selected success page, current language and DocCheck language are stored in the TYPO3 frontend session.
5. The user is redirected to the DocCheck authorization URL.
6. DocCheck returns to the configured callback URL `/doccheck-access/callback/`.
7. `CallbackMiddleware` exchanges the returned code for a token.
8. A configured frontend user is logged in.
9. The user is redirected to the language-aware success page or failure page.

DocCheck does not receive a TYPO3 state parameter. The required TYPO3 context is kept in the frontend session.

## Configuration

Configure the extension in TYPO3 extension configuration:

- `clientId`
- `clientSecret`
- `callbackPath`
- `successPid`
- `failurePid`
- `frontendUserUid`
- `frontendUserGroupUid`
- `tokenEndpoint`

`successPid` can be configured globally or per login content element. The per-element success page takes precedence.

Required admin configuration errors throw `RuntimeException` with explicit error codes. They are not converted into frontend messages.

## Frontend Errors

Non-admin login failures are stored in the frontend session under:

```text
doccheck_access_error
```

The error message content element reads the code, maps it to a fixed English message and deletes the session value afterwards.

Supported codes:

- `missing_code`
- `token_exchange_failed`
- `frontend_login_failed`
- `missing_content_element`
- `invalid_content_element`

## Installation

Install the Composer package `doc2k/doccheck-access` and activate the extension key `doccheck_access`.

Run the TYPO3 database compare to add the extension fields to `tt_content`.

No changes outside the extension are required by this package.
