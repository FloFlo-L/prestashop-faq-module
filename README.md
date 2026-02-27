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
