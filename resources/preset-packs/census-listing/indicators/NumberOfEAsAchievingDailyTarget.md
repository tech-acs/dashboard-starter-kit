---
type: indicator
title: Number of EAs Achieving Daily Target
description: Count of enumeration areas that met their daily listing target
---

Shows how many enumeration areas (EAs) achieved their daily listing target.

Calculated by comparing the number of households or structures listed in each EA per day
against the EA's assigned daily target, then counting the EAs that met or exceeded
the target. Render as a bar chart showing the count of achieving EAs per day or per area.

The `getData()` method should join area data via `lastlyAreaLeftJoinData()` so the
area name is available for chart labels.
