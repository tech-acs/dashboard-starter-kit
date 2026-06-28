---
outline: deep
---

# Area Insights

The **Area Insights** page is a dynamic, powerful tool designed to provide a comprehensive snapshot of field operations and thematic indicators for any geographic area. It translates complex datasets into actionable intelligence through a combination of grading gauges, scorecards, and interactive visualizations.

## Overview

Area Insights gives you a unified view of all indicators, scorecards, and gauges that are configured to display at the area level, filtered by the geographic area you select. This makes it an essential tool for:

- **Field supervisors** monitoring data collection progress across regions
- **Data analysts** comparing indicators across different administrative levels
- **Decision makers** who need a quick, at-a-glance view of key metrics for a specific area

## The Area Filter

At the top of the Area Insights page is the [Area Filter](/developer/building-your-dashboard/area-filter). This is your primary navigation tool for drilling down from a national overview to highly localized views.

The filter uses your configured **area hierarchy** (e.g., Country > Subcounty > Division > Location > Sublocation > EA) and supports two interaction modes that stay synchronized:

1. **Drill-Down (Cascading)** — Select a parent area, and the next dropdown is automatically populated with its child areas.
2. **Search & Jump** — Type any area name into the search box to find it instantly across all levels.

The two modes are bi-directionally synced. If you use search to jump to a specific EA, switching back to drill-down mode will show the full hierarchy path already selected.

## What You Will See

Once you select an area, the Area Insights page displays:

- **Case Stats** — A summary of data collection status for the selected area (total cases, complete, partial, duplicate).
- **Grading Gauges** — Visual progress indicators comparing actual values against reference or target values, with color-coded thresholds.
- **Scorecards** — High-level numeric summaries displayed as prominent cards.
- **Indicators (Charts)** — All published chart indicators configured for area-level display, rendered as interactive Plotly charts.

## Configuring Artefacts for Area Insights

Which artefacts appear on Area Insights depends on their **Scope** setting:

### Indicators

Indicators have a **Scope** field with three options:
- **Pages only** — Appears only on assigned pages.
- **Area insights only** — Appears only on the Area Insights page.
- **Everywhere** — Appears on both assigned pages and Area Insights.

### Scorecards

Scorecards also have a **Scope** field:
- **Dashboard only** — Appears only on the home page.
- **Area insights only** — Appears only on the Area Insights page.
- **Everywhere** — Appears on both.

### Gauges

Gauges do **not** have a Scope setting. By design, all published gauges always render on the Area Insights page only. They do not appear on regular indicator pages or the home page.

:::tip
If your dashboard has multiple active data sources, clicking **Area Insights** in the navigation will first show an index page with a card for each data source. Select a card to enter the detailed Area Insights view for that source.
:::

The Area Insights page uses its own filter, separate from the main dashboard [Area Filter](/developer/building-your-dashboard/area-filter). Changing your selection on Area Insights does not affect the filter used on regular indicator pages, and vice versa.

## Hierarchical Compatibility

Some indicators may not make sense at every level of the hierarchy. For example, a national-level market share chart would be meaningless at the EA level. Use the **Unsupported Area Levels** setting on each indicator to hide it at levels where it would be irrelevant or cluttered. See [Hierarchical Compatibility](/developer/building-your-dashboard/hierarchial-compatibility) for details.
