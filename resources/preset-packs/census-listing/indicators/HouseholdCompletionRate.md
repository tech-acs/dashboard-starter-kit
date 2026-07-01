---
type: indicator
title: Household Completion Rate
description: Percentage of households listed against the listing target
---

Shows the percentage of households listed against the target across administrative areas.

Calculated by counting listed households divided by the EA or area listing target,
grouped by `area_code`. Render as a bar chart or gauge showing listing progress by region.

The `getData()` method should join area data via `lastlyAreaLeftJoinData()` so the
area name is available for chart labels.
