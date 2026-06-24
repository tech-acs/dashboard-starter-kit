# Chimera Dashboard Starter Kit — Agent Guide

## Package identity
- **Packagist**: `uneca/dashboard-starter-kit`, namespace `Uneca\Chimera` in `src/`
- **Consumed as a Laravel package** (Spatie PackageTools), not a standalone app

## Commands
- `composer test` — runs Pest v4
- `composer analyse` — PHPStan analysis
- `composer test-coverage` — Pest with coverage
- `vendor/bin/pint` — Laravel Pint (PSR12 + short arrays, ordered imports, trailing commas). The `.php_cs.dist.php` config exists but Pint is the actual dev formatter
- Key Artisan commands: `chimera:install`, `chimera:make-artefact`, `chimera:make-scorecard`, `chimera:make-indicator`, `chimera:make-gauge`, `chimera:make-map-indicator`, `chimera:make-report`

## Three-path artefact creation architecture
Every artefact (Scorecard, Gauge, MapIndicator, Report, Indicator) supports 3 creation paths sharing validation and Action logic:

| Path | Entry point |
|------|-------------|
| Web | `src/Http/Controllers/Manage/*MakerController.php` + FormRequest |
| CLI | `src/Commands/Make{Type}.php` |
| MCP | `src/Mcp/Tools/Create{Type}.php` (via Laravel MCP) |

- **Validation rules**: `src/Validation/{Type}ValidationRules.php` — shared across all 3 paths
- **Actions**: `deploy/actions/Maker/Create{Type}Action.php` under `App\Actions\Maker` — deployed to consumer app via `chimera:install`
- **DTOs**: `src/DTOs/{Type}Attributes.php` — readonly objects with `toArray()` that maps `camelCase` → `snake_case`
- **Result**: `src/Results/ArtefactCreationResult.php` — readonly DTO with `success()`/`failed()` named constructors

## Artefact creation flow (inside Action — always in a DB transaction)
```
DB insert → chimera:make-artefact (file from stub) → return Result
```
If file creation fails, the transaction rolls back. `chimera:make-artefact` is `GeneratorCommand`-based, invoked via `Artisan::call()`.

## Generated artefact file location
- Artefact files land in the consuming Laravel's `app/Livewire/` (not inside `vendor/`)
- `app_path()` is used in Actions for path construction
- Namespace convention: `\Livewire\Scorecard`, `\Livewire\Indicator`, etc.

## MCP server
- Server class: `src/Mcp/Servers/DashboardStarterKit.php`
- Registered in `ChimeraServiceProvider::packageBooted()` via `Mcp::local('dashboard-artefact-generator', DashboardStarterKit::class)`
- Tools have `#[Description]` attributes; parameters defined via `JsonSchema` in `schema()` methods

## Dual database architecture
- **PostgreSQL** (primary app DB) — areas, ltree hierarchy, users, settings, metadata
- **MySQL** (breakout DB) — questionnaire response data, accessed via `BreakoutQueryBuilder` in `src/Services/`

## Key base classes for artefacts
- `src/Livewire/Chart.php` — abstract base for Indicators (Plotly). Uses traits: `AreaResolver`, `Cachable`, `FilterBasedAxisTitle`, `PlotlyDefaults`
- `src/Livewire/ScorecardComponent.php` — abstract base for Scorecards, Gauges. Uses `AreaResolver`, `Cachable`
- `HasLevelDiscrimination` trait — artefacts can declare inapplicability at certain hierarchy levels

## Scorecard name constraint
Regex: `/^[A-Z][A-Za-z\/]*[A-Za-z]$/` — starts with uppercase, can include `/` for directory nesting (e.g. `Households/BirthRate`).

## Test setup quirks
- Uses Orchestra Testbench (not full Laravel), Pest v4
- Base test class: `Uneca\CensusDashboardStarterKit\Tests\TestCase` — note the namespace mismatch with `composer.json` which maps `Uneca\Chimera\Tests\` to `tests/`. The `Pest.php` file uses the longer namespace; trust the `Pest.php` file
- Tests share `uses(TestCase::class)->in(__DIR__)` in Pest.php
- No CI workflows exist

## Environment
- `DB_CONNECTION=pgsql` (PostgreSQL for main app)
- SESSION_DRIVER=database, CACHE_STORE=database, QUEUE_CONNECTION=sync (dev default)
- Extensions: `ext-intl`, `ext-redis`, `ext-zip`
- `SECURE=false` env var controls whether dashboard is HTTPS-only

## Style conventions
- PSR12 with short array syntax, ordered & no unused imports, trailing commas in multiline
- PHP 8.2 features: `readonly` classes, named arguments, constructor property promotion
- 4-space indent (`.editorconfig`)
- Action classes use constructor injection for dependencies
- Commands receive dependencies via `handle()` method injection

<!-- CODEGRAPH_START -->
## CodeGraph

In repositories indexed by CodeGraph (a `.codegraph/` directory exists at the repo root), reach for it BEFORE grep/find or reading files when you need to understand or locate code:

- **MCP tools** (when available): `codegraph_explore` answers most code questions in one call — the relevant symbols' verbatim source plus the call paths between them. `codegraph_node` returns one symbol's source + callers, or reads a whole file with line numbers. If the tools are listed but deferred, load them by name via tool search.
- **Shell** (always works): `codegraph explore "<symbol names or question>"` and `codegraph node <symbol-or-file>` print the same output.

If there is no `.codegraph/` directory, skip CodeGraph entirely — indexing is the user's decision.
<!-- CODEGRAPH_END -->
