# Installation

## Requirements

The extension requires:

- TYPO3 CMS `^11.5 || ^12.4 || ^13.4 || ^14.0`
- `typo3/cms-core`
- `typo3/cms-frontend`
- PHP `>=8.0`
- a configured TYPO3 site
- a DocCheck OAuth client
- a TYPO3 frontend user that should be logged in after successful DocCheck authentication
- visible success and failure pages in the relevant site languages

## Composer Installation

Install the extension with Composer:

```bash
composer require doc2k/doccheck-access
```

After Composer installation, activate the extension from the shell if it is not activated automatically:

```bash
vendor/bin/typo3 extension:install doccheck_access
```

This console step is especially relevant for newer TYPO3 installations where Composer installation and extension activation are separate steps.

## TER Installation

When installed from TER, install and activate the extension through the TYPO3 Extension Manager.

## Database Compare

After installation, run the TYPO3 database compare to add the required `tt_content` fields.

The extension adds:

| Field | Purpose |
|---|---|
| `tx_doccheckaccess_button_label` | Optional custom login button label |
| `tx_doccheckaccess_success_pid` | Optional success page per login content element |
| `tx_doccheckaccess_buttonsize` | Button size setting |
| `tx_doccheckaccess_buttonalign` | Button alignment setting |

## TypoScript and Page TSconfig

The extension ships with all required TypoScript and Page TSconfig files.

### TYPO3 11

After installing the extension, include the provided static TypoScript and Page TSconfig in your root page:

* **Template → Includes** → *DocCheck Access*
* **Root Page → Resources → Page TSconfig** → *DocCheck Access*

### TYPO3 12+

Depending on your project setup, the configuration may already be available automatically. If the content elements do not appear, include the provided static TypoScript and Page TSconfig as described above.

The TypoScript registers the rendering for:

* `tt_content.doccheckaccess_login`
* `tt_content.doccheckaccess_error_message`

It also includes the frontend stylesheet:

```typoscript
page.includeCSS.doccheck_access = EXT:doccheck_access/Resources/Public/Css/dca.css
```

The Page TSconfig registers the custom content elements in the New Content Element Wizard.


## Site Sets

The extension does not require Site Sets.

This is intentional to keep the extension usable in TYPO3 11.5 and TYPO3 12 installations and to avoid requiring TYPO3 13+ only APIs.

For TYPO3 13.4 LTS and TYPO3 14.3 LTS projects, no Site Set is required either. A future version may provide optional Site Sets, but the current integration does not depend on them.

## First Setup Checklist

1. Install and activate the extension.
2. Run database compare.
3. Create or choose the TYPO3 frontend user used for DocCheck logins.
4. Configure the extension in TYPO3 Extension Configuration.
5. Add a DocCheck Login content element.
6. Add a DocCheck Error Message content element to the failure page.
