# Global New Topic

**Version:** 1.0.0\
**Compatibility:** SMF 2.1.0--2.1.99\
**License:** BSD-3-Clause

Global New Topic adds a collapsible **Start a new topic** composer to
normal SMF 2.1 forum pages. Users can choose the destination board
directly from a dropdown without first opening that board.

Only boards in which the current user has SMF's native `post_new`
permission are available.

## Features

-   Global new-topic composer on normal forum pages
-   Collapsible and expandable interface
-   Destination-board dropdown grouped by category
-   No board selected by default
-   Uses SMF's native rich-text/BBC editor and posting controls
-   Native Preview, Save Draft and Post workflow
-   Native attachment handling
-   Native **Other options**, including permission-dependent options
    such as Lock, Sticky, Move, Announce and Approve
-   Saved drafts displayed like in SMF's native new-topic form
-   Per-member-group access
-   Supports Guests, Regular Members, primary groups, post-based groups
    and additional groups
-   Normal SMF board and posting permissions are always respected
-   Hidden on board index pages (`index.php?board=x.x`)
-   Does not interfere with SMF's member-name autocomplete endpoint
-   English, German formal and German informal language files
-   Uses native SMF CSS classes; no separate custom stylesheet

## Installation

1.  Download the package ZIP.
2.  Open **Admin Center → Package Manager** in SMF.
3.  Upload and install the package.
4.  Open **Admin Center → Configuration → Modification Settings → Global
    New Topic**.
5.  Enable the feature and select the member groups that may use it.

## Settings

The settings are located under:

**Admin Center → Configuration → Modification Settings → Global New
Topic**

Available settings include:

-   Enable or disable Global New Topic
-   Select the member groups allowed to use the global composer
-   Choose whether the composer is collapsed by default

SMF's normal board permissions are applied in addition to the selected
member groups.

## Uninstallation

Uninstall **Global New Topic** through SMF's Package Manager.

The uninstall process removes the package hooks, installed files and
settings.

## Compatibility

-   SMF 2.1.0--2.1.99
-   Designed for PHP versions supported by SMF 2.1

## License

Global New Topic is released under the **BSD-3-Clause** license. See
`license.txt` for the full license text.
