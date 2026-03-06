# PrestaShop Module: FAQ

FAQ (Frequently Asked Questions) management module for PrestaShop > 8.0.0

## Installation

### 1. Clone the repository

Clone the repository into the `modules` folder of your PrestaShop store:

```bash
cd /path/to/your/store/modules
git clone git@github.com:FloFlo-L/prestashop-faq-module.git faq
```

### 2. Install dependencies

Go into the module folder:

```bash
cd faq
```

Install PHP dependencies and regenerate the autoloader:

```bash
composer install
composer dump-autoload
```

### 3. Install the module

**Via command line** (from the root of your PrestaShop store):

```bash
php bin/console prestashop:module install faq
```

**Or via the admin interface**: go to **Modules > Module Manager**, search for "FAQ" and click **Install**.

## Features

### Front office

A dedicated FAQ page displays categories as horizontal tabs with icons, and questions as an accordion within each category. The page is fully responsive with horizontal scrolling on mobile.

### Back office

The module adds a **FAQ** menu in the back office with two sub-entries:

- **Categories** — grid listing all FAQ categories (name, icon, position, active status) with create/edit forms and drag-and-drop reordering.
- **Questions & Answers** — grid listing all questions (category, question, position, active status) with create/edit forms and drag-and-drop reordering.

#### Configuration

A **Configuration** page lets you set:

- **Page title** — displayed above the FAQ page (multilingual).
- **Page subtitle** — displayed as the main heading of the FAQ page (multilingual).

#### Generate sample data

A **Generate** button is available in the back office to populate the FAQ with default sample data (categories and questions provided out of the box), useful for testing or getting started quickly.
