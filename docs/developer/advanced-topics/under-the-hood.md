---
---

# Under the hood

Understanding how the kit works internally will help you make better architectural decisions and debug issues more effectively.

## Architecture Overview

The Dashboard Starter Kit is a Laravel package that layers dashboard-specific functionality on top of a standard Laravel + Jetstream + Livewire stack.

### Request Flow

1. A user visits a page, which triggers a Livewire component mount.
2. The component resolves the artefact (indicator, scorecard, gauge) from the database.
3. For indicators, the `Chart` base class calls the artefact's `getData()` method.
4. `getData()` instantiates a `BreakoutQueryBuilder`, which builds and executes a SQL query against the configured data source.
5. The query result is enriched with geographic area metadata (names, codes, paths).
6. The data is cached (if caching is enabled) and rendered as a Plotly chart via the frontend `PlotlyChart.js`.

### Dual-Database Architecture

The kit uses two database tiers:

- **PostgreSQL (Main App):** Stores users, roles, permissions, areas (with PostGIS and ltree), artefact metadata, settings, and cache.
- **Data Sources (MySQL/MariaDB/etc.):** Stores the actual survey data in CSPro breakout format. Multiple data sources can be connected.

The `BreakoutQueryBuilder` bridges these two worlds by querying the data source database and then enriching results with area data from the main app database.

### Caching Layer

Every published artefact is cached automatically. The `Cachable` trait on base classes manages cache keys, tags, and TTL. The cache can be pre-warmed via scheduled `chimera:cache-*` commands.

### Authorization

Permissions are artefact-scoped. Each indicator, scorecard, gauge, map indicator, and report generates a permission entry during creation. Gates check these permissions against the user's assigned roles.

### Livewire to Plotly Data Flow

```
getData() → Collection → Chart::getTraces() (maps columnNames to Plotly traces)
                         → Chart::getLayout() (merges stored layout with dynamic axis titles)
                         → sendUpdates() (dispatches Livewire event)
                         → PlotlyChart.js (receives event, calls Plotly.react())
```