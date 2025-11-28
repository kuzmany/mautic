# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Mautic is an open-source marketing automation platform built on Symfony 7.x. It follows a bundle-based architecture with 33+ core bundles providing distinct functionality (EmailBundle, FormBundle, CampaignBundle, etc). The project uses Doctrine ORM, Twig templating, and follows Symfony best practices.

## Quick Commands

### Development Setup
```bash
# Using DDEV (recommended)
ddev start                     # Automatically installs and configures Mautic
ddev composer install          # Install PHP dependencies
ddev exec bin/console          # Access Symfony console

# Manual setup
composer install               # Install PHP dependencies
npm ci                         # Install Node dependencies
npm run build                  # Build CKEditor 5
grunt compile-less             # Compile LESS to CSS
bin/console mautic:install    # Install Mautic
bin/console cache:clear        # Clear cache
```

### Build & Assets
```bash
npm run build                  # Build CKEditor 5 WYSIWYG editor
grunt compile-less             # Compile all LESS files to CSS
grunt watch                    # Watch for LESS changes
bin/console mautic:assets:generate  # Generate Mautic assets
bin/console assets:install    # Install Symfony assets
```

### Testing
```bash
# Run full test suite
composer test                  # PHPUnit tests with 2G memory limit

# Specific test types
bin/phpunit --testsuite "Project Test Suite"  # Unit tests only
bin/phpunit --testsuite "Plugin tests"        # Plugin tests only
composer e2e-test              # Codeception E2E tests (requires DDEV)

# Run single test
bin/phpunit path/to/test/file.php
bin/phpunit --filter testMethodName
```

### Code Quality
```bash
# Fix code style (run before commits)
bin/php-cs-fixer fix --config=.php-cs-fixer.php

# Check code style
bin/ecs check app config plugins tests

# Static analysis
composer phpstan -- --no-progress

# Code modernization (dry-run)
bin/rector --dry-run --ansi
```

### Database Operations
```bash
bin/console doctrine:migrations:migrate        # Run pending migrations
bin/console doctrine:migrations:generate       # Create new migration
bin/console doctrine:schema:update --dump-sql  # Preview schema changes
bin/console doctrine:fixtures:load             # Load test data
```

### Development Tools
```bash
bin/console                    # List all available commands
bin/console cache:clear        # Clear cache
bin/console debug:router       # Show all routes
bin/console debug:container    # Show services
bin/console mautic:segments:update  # Update segments
bin/console mautic:campaigns:update # Process campaigns
```

## Architecture Patterns

### Bundle Structure
Each bundle in `app/bundles/` follows this pattern:
- **Controller/**: HTTP request handlers
- **Entity/**: Doctrine ORM entities
- **Repository/**: Database queries
- **Model/**: Business logic layer
- **Form/**: Symfony form types
- **Event/**: Event classes
- **EventListener/**: Event subscribers
- **Views/**: PHP templates (migrating to Twig in `templates/`)

### Service Layer Pattern
```php
Controller -> Model -> Repository -> Entity
```
- Controllers handle requests, Models contain business logic
- Repositories manage database queries, Entities represent data

### Event-Driven Architecture
- Events defined in `[Bundle]Events.php` classes
- Listeners in `EventListener/` folders
- Use EventDispatcher for decoupled communication

### Form Handling
- Forms use both PHP (`Views/`) and Twig (`templates/`) templates
- Theme-specific overrides in `themes/[theme]/html/`
- Form configuration in bundle's `Config/config.php`

## Key Directories

- **app/bundles/**: Core functionality (33+ bundles)
- **plugins/**: Third-party integrations (GrapesJS, CRM, Social, etc.)
- **themes/**: Email and landing page templates (40+ themes)
- **media/**: Static assets (CSS, JS, images)
- **var/**: Runtime files (logs, cache, temp)
- **config/**: Environment configuration
- **templates/**: Twig templates (new standard)

## Testing Approach

### Test Database
- Uses dedicated `mautictest` database
- Configuration in `.env.test.local`
- Fixtures in `tests/_data/`

### Test Execution
- Always run with memory limit: `-d memory_limit=2G`
- Use `--testdox` for readable output
- Coverage reports to `var/cache/phpunit/coverage.xml`

## Development Guidelines

### Before Making Changes
1. Read existing code first - use `Read` tool on files before editing
2. Check for existing tests covering the feature
3. Understand the bundle's architecture

### Making Changes
1. Follow Symfony/PSR-12 coding standards
2. Add migrations for database changes
3. Update both PHP and Twig templates if touching forms
4. Write/update tests for new functionality

### After Changes
1. Run code formatters (`bin/php-cs-fixer fix`)
2. Run tests (`composer test`)
3. Clear cache (`bin/console cache:clear`)
4. Rebuild assets if needed (`npm run build`, `grunt compile-less`)

## Common Patterns

### Adding a New Feature
1. Identify the appropriate bundle
2. Create Entity if needed
3. Create Repository for database queries
4. Implement business logic in Model
5. Add Controller actions
6. Create Form types
7. Add templates (Twig preferred)
8. Write tests

### Debugging
- Logs: `var/logs/mautic_*.log`
- Profiler: `/_profiler` (dev mode)
- Var dumper: `bin/var-dump-server`
- Cache issues: `bin/console cache:clear`

## Environment Configuration

### Key Files
- `.env.local`: Local environment variables (git-ignored)
- `app/config/local.php`: Local Mautic configuration (git-ignored)
- `app/config/parameters.php`: Default parameters

### DDEV Development
- Config: `.ddev/config.yaml`
- Auto-setup: `.ddev/mautic-setup.sh`
- Default credentials: admin / Maut1cR0cks!
- Services: MariaDB, Redis, Selenium, MailHog

## Important Notes

- **Monorepo**: The `app/` folder is mirrored from `mautic/core-lib` repository
- **PHP Version**: Requires PHP 8.2+ (supports 8.2, 8.3, 8.4)
- **Symfony Version**: Uses Symfony 7.x
- **Template Migration**: Moving from PHP to Twig templates
- **Form Templates**: Support both PHP and Twig (configurable per form)
- **Asset Building**: CKEditor requires Webpack build, CSS uses Grunt
- **Plugin Development**: Follow bundle structure in `plugins/` directory