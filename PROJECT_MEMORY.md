# Project Memory: Ghasa Products (gp)

> **Last Updated:** 2026-11-06  
> **Purpose:** Brief, persistent memory of project structure, environment, and conventions so agents do not need to re-analyze the codebase on every task.

---

## 1. Project Overview & Environment
- **Project Name:** Ghasa Products (`gp`)
- **Git Repository:** `https://github.com/0xyzone/gp.git`
- **Default Branch:** `main`
- **Application Type:** Laravel 13 web application
- **PHP Version:** PHP 8.4.15 (ZTS Visual C++ 2022 x64, with Xdebug v3.4.7, OPcache)
- **Composer Version:** 2.10.1
- **Database:** SQLite (`database/database.sqlite`) / MySQL configurable via `.env`
- **Node & Build Tool:** Vite 8 (`@tailwindcss/vite` 4, `tailwindcss` 4, `laravel-vite-plugin` 3.1)

---

## 2. Key Directories & Architecture
- `app/Http/Controllers`: Standard Laravel HTTP controllers (base `Controller.php`).
- `app/Models`: Eloquent models (standard `User.php` with Sanctum/Notifiable traits).
- `app/Providers`: Core service providers (`AppServiceProvider.php`).
- `routes/`: `web.php` (web routes), `console.php` (artisan closure commands).
- `resources/views/`: Blade templates (`welcome.blade.php`).
- `resources/css/app.css`: Tailwind v4 entrypoint (`@import 'tailwindcss';` + `@theme` block).
- `resources/js/app.js`: Frontend script entrypoint.
- `database/`:
  - `migrations/`: Standard migrations (users, cache, jobs).
  - `factories/`: Model factories (`UserFactory.php`).
  - `seeders/`: Database seeders (`DatabaseSeeder.php`).
- `tests/`: Feature and Unit tests using PHPUnit.
- `.claude/`, `.factory/`, `.grok/`: Agent skill catalogs and boost configurations.

---

## 3. Installed Packages & Tooling
- **Framework & Core:** `laravel/framework` (^13.17), `laravel/tinker` (^3.0)
- **AI Agent & Ecosystem:** `laravel/boost` (^2.10), `laravel/mcp`, `laravel/roster`, `laravel/pail`, `laravel/pao`
- **Testing & Quality:** `phpunit/phpunit` (^12.5), `mockery/mockery` (^1.6), `laravel/pint` (^1.27), `fakerphp/faker` (^1.23), `nunomaduro/collision` (^8.6)
- **Frontend:** Tailwind CSS v4 (`@tailwindcss/vite` ^4.0.0, `tailwindcss` ^4.0.0)

---

## 4. Coding & Styling Standards
- **Laravel Best Practices:**
  - Follow standard Laravel conventions (named routes, route(), Eloquent relationships, API resources).
  - Use Artisan `php artisan make:* --no-interaction` commands to generate classes, migrations, and models.
  - Run `vendor/bin/pint --dirty --format agent` after editing PHP files.
- **Tailwind CSS v4 Practices:**
  - **CSS-First Configuration:** Use `@theme` in `resources/css/app.css` (NO `tailwind.config.js`).
  - **Import Syntax:** Use `@import 'tailwindcss';` (NO `@tailwind` directives).
  - **No Deprecated Utilities:**
    - Use modifier syntax for opacity: `bg-{color}/*`, `text-{color}/*`, `border-{color}/*`, `divide-{color}/*`, `ring-{color}/*`.
    - Use `shrink-*` instead of `flex-shrink-*`.
    - Use `grow-*` instead of `flex-grow-*`.
    - Use `text-ellipsis` instead of `overflow-ellipsis`.
    - Use `box-decoration-slice` / `box-decoration-clone`.
  - **Spacing:** Always use flex/grid `gap-*` utilities instead of child margins for sibling spacing.
  - **Dark Mode:** Support `dark:` variants consistently where components have light/dark styling.
  - **Avoid Assumptions:** Always inspect existing Blade templates or CSS before adding utility combinations.
- **Strict Verification & Zero Assumptions:**
  - Never make blind assumptions about installed library versions, schema column names, or configurations. Verify with files, commands, or documentation before altering functionality.

---

## 5. Mandatory Agent Automation Workflow (On Every Task Completion)
Every agent working on this repository **must** perform the following actions upon completing any task:

1. **Format Code:** Run `vendor/bin/pint --dirty --format agent` on modified PHP files.
2. **Git Commit & Push:**
   - Stage relevant modified and new files: `git add <files>`
   - Commit with clear message: `git commit -m "<task description>"`
   - Push to `main`: `git push origin main`
3. **Send ntfy Notification:**
   - Send detailed notification to `https://ntfy.sh/gp-tasks`.
   - On Windows PowerShell, use `curl.exe`:
     ```powershell
     curl.exe -s -X POST "https://ntfy.sh/gp-tasks" `
       -H "Title: <Task Title> - Completed" `
       -H "Tags: white_check_mark" `
       -d "<Detailed summary of tasks executed, changes made, and commit hash>"
     ```
