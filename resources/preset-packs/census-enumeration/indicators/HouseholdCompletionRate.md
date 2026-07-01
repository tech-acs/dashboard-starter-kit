---
type: indicator
title: Household Completion Rate
description: Percentage of households enumerated against the enumeration target
---

Shows the percentage of households completed against the target across administrative areas.

Calculated as `COUNT(enumct` or `SUM(enumstat` on the household record, divided by the
EA or area household target, grouped by `area_code`. Render as a bar chart or gauge
showing completion progress by region.

The `getData()` method should join area data via `lastlyAreaLeftJoinData()` so the
area name is available for chart labels.
