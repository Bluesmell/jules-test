# OpenCart Manager

## Overview
OpenCart Manager is a comprehensive web application designed to enhance the management capabilities of an OpenCart store. It provides a suite of tools for sales tracking, stock management, customer relationship management, SEO optimization, and optional AI-powered content generation through LLM integration. Built with PHP, MySQLi, HTML5, CSS3 (Bootstrap 5), and JavaScript, it features a modular MVC-like structure for maintainability and future expansion.

## Key Features
*   **Sales Tracking Dashboard:** Real-time metrics, revenue charts, top products, customer patterns, order status, CSV/PDF export.
*   **Stock Management:** Inventory levels, low stock alerts, stock movement history, bulk updates, inventory valuation, reorder points.
*   **Customer Management:** Customer lists with search/filter, purchase history, CLV, segmentation, contact management, order frequency.
*   **SEO Link Management:** List products missing SEO URLs, bulk edit aliases, preview URLs, duplicate detection, multi-language URL management, batch URL generation.
*   **LLM Integration Module (Optional):** Configure API keys (OpenAI, Claude, etc.), manage prompt templates, auto-generate SEO-friendly content (URLs, descriptions), bulk processing, review/approve, rollback.
*   **Multi-Language Support:** Language switcher, language-specific data display, multi-language SEO.
*   **Modern UI:** Clean, responsive design with Bootstrap 5, intuitive navigation, data tables with sort/filter/pagination, modals, progress indicators, dark/light theme.
*   **Security:** Input sanitization, CSRF protection, session management, prepared statements (SQLi prevention), XSS protection.
*   **Configuration:** Database settings, OpenCart table prefix, multi-language settings, LLM API config, email notifications, backup paths.
*   **Installation Wizard:** Simple setup process.

## Technology Stack
*   **Backend:** PHP 7.4+
*   **Database:** MySQL (interacts with existing OpenCart DB via MySQLi/PDO)
*   **Frontend:** HTML5, CSS3 (Bootstrap 5), JavaScript (ES6+)
*   **Architecture:** Modular (MVC-like)

## Prerequisites
*   A working OpenCart installation (version 3.x or 4.x recommended).
*   PHP 7.4 or higher.
*   MySQL database server used by OpenCart.
*   PHP extensions: MySQLi (or PDO_MySQL).
*   Web server (Apache, Nginx, etc.).
*   Write permissions for the `config/` directory (during installation only, can be restricted afterwards).
*   Write permissions for `backups/` and `exports/` directories (if these features are used).

## Installation Instructions

1.  **Download/Clone:**
    *   Download the latest release ZIP file or clone the repository into a directory named `opencart-manager` (or your preferred name) inside your main OpenCart installation directory.
    *   Example: If OpenCart is in `/var/www/html/opencart/`, then this application would be in `/var/www/html/opencart/opencart-manager/`.

2.  **Set Permissions (Temporary for Installation):**
    *   Ensure the `opencart-manager/config/` directory is writable by the web server. This is needed for the installer to attempt to write the database configuration.
        ```bash
        chmod 777 opencart-manager/config 
        # Or more securely, chown to the web server user and chmod 755
        ```
    *   *After installation, it's recommended to make `config/config.php` and `config/database.php` read-only if the installer writes to them, or ensure the installer provides the content to be manually placed.*

3.  **Run the Installation Wizard:**
    *   Open your web browser and navigate to the installation script:
        `http://your-opencart-domain.com/opencart-manager/install.php`
    *   Follow the on-screen instructions:
        *   **Step 1: Requirements Check:** Verifies server compatibility.
        *   **Step 2: Database Configuration:** Enter your existing OpenCart database details. This application will read from OpenCart tables and may create its own tables (prefixed, e.g., `ocm_`) in the same database.
        *   **Step 3: Admin User Setup:** Create an administrator account specifically for the OpenCart Manager application.
        *   **Step 4: Finalization:** Installation complete.

4.  **Secure Your Installation:**
    *   **IMPORTANT:** Delete or rename the `opencart-manager/install.php` file immediately after successful installation.
    *   If you temporarily set wider permissions on the `config/` directory, revert them to be more restrictive (e.g., read-only for the configuration files).

5.  **Directory Structure (ensure these are created if not by installer, and writable if features used):**
    *   `opencart-manager/backups/` (for data backups)
    *   `opencart-manager/exports/` (for data exports)
    *   Ensure these directories are writable by the web server if you intend to use backup/export features.

## Configuration

*   **Main Configuration:** `opencart-manager/config/config.php`
    *   Contains base paths, default language, LLM module toggle, admin email, backup/export paths, session timeout, and debug mode.
    *   Most of these are set during installation or have sensible defaults.
*   **Database Configuration:** `opencart-manager/config/database.php`
    *   Stores OpenCart database connection details. Typically generated by the installer.
*   **LLM Configuration:** `opencart-manager/config/llm-config.php` (or managed via UI and stored in DB)
    *   API keys and provider settings for the LLM module. Configure via the UI in `Modules > LLM Integration > LLM Configuration`.

## Basic Usage

1.  **Login:**
    *   Access the manager via `http://your-opencart-domain.com/opencart-manager/`.
    *   Log in with the admin credentials created during installation.

2.  **Navigate Modules:**
    *   Use the main navigation bar to access different modules: Dashboard, Sales, Stock, Customers, SEO, LLM.

3.  **Theme Toggle:**
    *   Use the moon/sun icon in the top right to switch between light and dark themes.

4.  **Language Switcher:**
    *   Use the globe icon to switch the interface language (if multiple languages are configured).

*(More detailed usage instructions for each module will be in the USER_MANUAL.md)*

## Contributing
*(Details for developers if this were an open-source project - e.g., coding standards, pull request process)*
This is currently a project being developed by Jules, an AI agent.

## License
*(Specify a license if applicable, e.g., MIT, GPL)*
License to be determined.

---
*This README is a work in progress and will be updated as the application evolves.*
