# Hyvä Themes - Page Builder Module

[![Hyvä Themes](https://hyva.io/media/wysiwyg/logo-compact.png)](https://hyva.io/)

## magento2-page-builder

![Supported Magento Versions][ico-compatibility]

## Overview
A Magento 2 module that adds [Page Builder](https://magento.com/products/magento-commerce/page-builder) compatibility* for [Hvyä Themes](https://hyva.io/) projects running on Magento Commerce.

> :warning: - Page Builder is now part of [Hyva Default Theme](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/222) and [Theme Module](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/68), as of **version 1.1.9**.
>
> You should avoid using this module with this version and newer versions, since this might cause compatibility issues.

> *Note: this is purely a compatibility project, not a full reimplementation utilising [Alpine.js](https://github.com/alpinejs/alpine)/[Tailwind CSS](http://tailwindcss.com/). See more detail on this [below](#design-decisions--performance).

## Features
* Reimplements the JavaScript widgets that provide dynamic functionality (e.g. carousels/sliders, tabs, maps etc.)
* Ports the required, functional CSS from LESS to Tailwind/PostCSS (e.g. column and banner layouts)
* Reimplements product blocks to utilise the standard Hyvä product list templates so they are inline with Hyvä category/search pages

No Page Builder master templates* have been overridden, to ensure solid maintainability/updatability with Magento's base Page Builder modules.

> *The format Page Builder uses to render content as raw HTML, which is then stored in the database.

## Compatibility
Magento Commerce Edition 2.4.2 and PHP 7.4 are recommended as a minimum*. You'll also need the latest version of [Hyvä Themes](https://docs.hyva.io/).

> *2.4.0/2.4.1 (or patch versions) and PHP 7.3 may work, but have not been tested, nor will they be supported.

## Installation / Configuration

1. Install via composer
```
composer require hyva-themes/magento2-page-builder
```

2. Enable module
```
bin/magento setup:upgrade
```

3. Copy the CSS to your projects Hyvä based theme
```
cp view/frontend/web/tailwind/components/page-builder.css {PATH_TO_YOUR_THEME}/web/tailwind/components/page-builder.css
```

4. Add CSS to your Tailwind CSS output
   Edit the `{PATH_TO_YOUR_THEME}/web/tailwind/tailwind-source.css` file and import the above CSS file by adding `@import url(components/page-builder.css);` before the line containing the `/* purgecss end ignore */` comment.

> Note: it is important you add this import before the comment as otherwise styles may not be included once purged as Page Builder content is dynamic and not included in the list of files when purging.

5. Add templates to purge list
   The below ensures all templates used in this module (such as the product lists) are included when CSS is purged.
```
purge: {
    content: [
        ...
        '../../../../../../../vendor/hyva-themes/magento2-page-builder/**/*.phtml'
    ]
}

```

6. Rebuild Tailwind CSS output

From the `{PATH_TO_YOUR_THEME}/web/tailwind` directory, run either of the below based on your target environment.

For development
```
npm run build-dev
```

For production
```
npm run build-prod
```

## Content Types Support
> 'Content Types' are the different types of content admin users can select to output content, such as text, images, videos, banners. sliders, product lists etc.

All content types have a base level of support, and many require either no changes or have had a minimal amount of CSS ported* to provide compatibility, including:
* Columns
* Headings
* Text (WYSIWYG)
* HTML Code
* Dividers
* Images
* Videos
* CMS & Dynamic Blocks

> *CSS is ported to a custom PostCSS file that utilises [Tailwind CSS](http://tailwindcss.com/)'s `@apply` directive so utility classes are supported. Standard CSS declarations are only used where Tailwind CSS does not have a matching utility class.

### Rows
Parallax image and video backgrounds support has been added by reimplementing the [Jarallax](https://github.com/nk-o/jarallax) integration.

### Tabs
The tabs functionality has been reimplemented by applying the relevant Apline.js bindings to the markup dynamically.

### Buttons
The functionality to equalise button widths (making all buttons the same width) has been reimplemented.

### Banners
As with rows, parallax image and video backgrounds support has been added by reimplementing the [Jarallax](https://github.com/nk-o/jarallax) integration. In addition, the display of buttons and/or an overlay on hover has been recreated.

### Sliders
The reliance on Slick (a third-party, jQuery carousel plugin) has been removed and in its place a lightweight library (<3kb), [Glider.js](https://nickpiscitelli.github.io/Glider.js/), has been implemented to recreate almost all functionality. Additional functionality, including autoplay and infinite looping has also been added.

#### Missing Functionality / Known Issues
* The optional fade animation (for transitioning between slides) is not included/supported by [Glider.js](https://nickpiscitelli.github.io/Glider.js/) and has not been replicated
* Video backgrounds are not currently fully supported/working (fallback images do, however)

### Maps
The Google Maps integration has been fully reimplemented.

### Products
Product blocks are implemented via `.phtml` templates*, which have been refactored inline with other [Hvyä Themes](https://hyva.io/) product list templates and make use the per item (product) child template.

Product carousel support has been re-added, also utilising [Glider.js](https://nickpiscitelli.github.io/Glider.js/), with reimplementation of autoplay and infinite looping.

> *Modifying these templates does not impact Page Builder's master templates (data storage of content).

#### Missing Functionality / Known Issues
* The continuous scrolling option is not included/supported by [Glider.js](https://nickpiscitelli.github.io/Glider.js/) and has not been replicated
* Admin preview of product blocks needs review/fixing as the frontend `.phtml` template overrides appear to be pulled through

### dotdigital Form
The dotdigital form (included as a core bundled extension) is not yet supported (i.e. has not been reimplemented)

For more detail on supported/ported content types see this [Hyvä Page Builder Compatibility](https://docs.google.com/spreadsheets/d/1OKYtjxL37iQV3bvBoeCNQ0F5KRqmlYbaFoovQauWEy4/edit) doc.

## Design Decisions / Performance
This module is NOT a full reimplementation of Page Builder that makes full use of the key frameworks that power [Hvyä Themes](https://hyva.io/): [Alpine.js](https://github.com/alpinejs/alpine) and [Tailwind CSS](http://tailwindcss.com/). Taking this approach, which would provide the ideal solution in both developer experience and performance, would be a much larger undertaking*.

The design decisions of this module were made to allow a simple reimplementation in a short time frame to make [Hvyä Themes](https://hyva.io/) a viable option for merchants on Magento Commerce that require/desire Page Builder as their CMS solution.

> *See Magento's [PWA Studio implementation of Page Builder](https://magento.github.io/pwa-studio/pagebuilder/) for a comparable solution.

Due to these decisions there is a lack of control over HTML markup (and full [Alpine.js](https://github.com/alpinejs/alpine)/[Tailwind CSS](http://tailwindcss.com/) support). This means some areas do introduce minor layout shifts/impact to performance, but no more so (in fact in most cases far less less than) a Luma/Blank based theme, i.e. a theme utilising RequireJS, jQuery and Knockout.

Overall, the benefit of [Hvyä Themes](https://hyva.io/) with this module installed is a much faster/performant solution than a theme based on Luma/Blank. From (albeit brief/limited) testing lighthouse performance scores are not impacted by more than a few points, if at all, in most cases compared to a vanilla Hyvä Themes installation.

The biggest offenders are unavoidable third party scripts, such as Google Maps and those from Youtube/Vimeo when including video content. Care has also been taken to ensure additional scripts (e.g. Google Maps/[Jarallax](https://github.com/nk-o/jarallax)/[Glider.js](https://nickpiscitelli.github.io/Glider.js/)) are only loaded when required, i.e. Page Builder elements exist on the page that require them.

It is therefore recommended to use maps and video elements sparingly and ideally avoid their use on your homepage/key landing pages.

## License
Hyvä Themes - https://hyva.io

Copyright © Hyvä Themes B.V 2020-present. All rights reserved.

This product is licensed per Magento install. Please see [License File](LICENSE.md) for more information.

## Changelog
Please see [The Changelog](CHANGELOG.md).

[ico-compatibility]: https://img.shields.io/badge/magento-%202.4-brightgreen.svg?logo=magento&longCache=true&style=flat-square
