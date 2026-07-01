---
type: indicator
title: Estimated Days to Complete Listing
description: Projected number of days to complete listing based on recent performance
---

Shows the estimated days remaining to complete the listing operation based on recent
daily performance.

Calculated by dividing the remaining structures or households to list by the average
daily listing rate over the previous days. Grouped by `area_code`. Render as a bar
chart showing projected completion time per region, or a line chart tracking the
estimate over time.

The `getData()` method should join area data via `lastlyAreaLeftJoinData()` so the
area name is available for chart labels.
