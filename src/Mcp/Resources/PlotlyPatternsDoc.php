<?php

namespace Uneca\Chimera\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Description('Plotly chart patterns for indicators — trace configurations, meta.columnNames, hovertemplate formatting, and layout adjustments for all supported chart types (bar, scatter, pie, histogram, line, area, box).')]
#[Uri('docs://plotly-patterns')]
class PlotlyPatternsDoc extends Resource
{
    public function handle(Request $request): Response
    {
        return Response::text(self::CONTENT);
    }

    private const CONTENT = <<<'DOC'
PLOTLY CHART PATTERNS FOR INDICATORS
=====================================

HOW meta.columnNames WORKS
  The edit-chart tool maps SQL column aliases to trace properties via
  meta.columnNames. The keys are trace property paths, the values are SQL aliases
  from your getData() SELECT.

  Values can be a string ("x": "area_name") or a single-element array
  ("y": ["total"]). The edit-chart tool validates all array elements against
  your SQL aliases, then the trace reads the first element.

  For multi-trace charts (e.g. grouped bars for males and females), create
  **separate trace objects** in the data array, each with its own
  meta.columnNames pointing to a single SQL alias.

  For pie charts, use "labels" and "values" instead of "x" and "y".

──────────────────────────────────

CHART TYPES

1. BAR (type: "bar")
  Vertical bars comparing values across categories.

  Trace structure:
    {
      "type": "bar",
      "meta": { "columnNames": { "x": ["area_name"], "y": ["total"] } },
      "name": "Population",
      "marker": { "color": "#2563eb" },
      "hovertemplate": "%{x}<br>%{y:,.0f}<extra></extra>"
    }

  Key properties:
    orientation: "v" (default) | "h" (horizontal)
    marker.color: single color or array
    text: labels on bars
    textposition: "inside" | "outside" | "auto"

  Multi-trace (grouped bars):
    Two traces sharing the same x, different y aliases.
    Layout barmode: "group" (default) | "stack" | "relative"

  With reference line:
    Trace 1: bar with y from data
    Trace 2: scatter/line with y from reference_value column

──────────────────────────────────

2. SCATTER (type: "scatter")
  Points with optional connecting lines.

  Trace structure:
    {
      "type": "scatter",
      "mode": "markers",
      "meta": { "columnNames": { "x": ["area_name"], "y": ["rate"] } },
      "name": "Literacy Rate",
      "marker": { "color": "#2563eb", "size": 8 },
      "hovertemplate": "%{x}<br>%{y:.1f}%<extra></extra>"
    }

  mode values:
    "markers" — points only
    "lines" — lines only
    "lines+markers" — both

  Key properties:
    marker.size: point size
    marker.color: point color
    line.shape: "linear" | "spline" | "hv" | "vh" | "hvh" | "vhv"

──────────────────────────────────

3. PIE (type: "pie")
  Proportional slices (uses labels + values instead of x + y).

  Trace structure:
    {
      "type": "pie",
      "meta": { "columnNames": { "labels": ["area_name"], "values": ["total"] } },
      "name": "Distribution",
      "hole": 0.4,
      "textinfo": "label+percent",
      "hovertemplate": "%{label}<br>%{value:,.0f} (%{percent})<extra></extra>"
    }

  Key properties:
    hole: 0–1 (0 = pie, 0.4–0.6 = donut)
    textinfo: "label" | "percent" | "value" | "label+percent" | "none"
    pull: explode slices (array of 0–1)
    sort: true (sort by value descending, default)
    direction: "clockwise" | "counterclockwise"

──────────────────────────────────

4. HISTOGRAM (type: "histogram")
  Distribution of a continuous variable.

  Trace structure:
    {
      "type": "histogram",
      "meta": { "columnNames": { "x": ["interview_time"] } },
      "name": "Interview Duration",
      "marker": { "color": "#2563eb" },
      "hovertemplate": "%{x}<br>Count: %{y}<extra></extra>"
    }

  Key properties:
    nbinsx: number of bins (auto if omitted)
    histnorm: "" (count) | "percent" | "probability" | "density"
    cumulative.enabled: true (cumulative histogram)

──────────────────────────────────

5. LINE (type: "scatter" with mode: "lines")
  Trend/sequence data.

  Trace structure:
    {
      "type": "scatter",
      "mode": "lines",
      "meta": { "columnNames": { "x": ["area_name"], "y": ["rate"] } },
      "name": "Literacy Rate",
      "line": { "color": "#2563eb", "width": 2, "shape": "spline" },
      "hovertemplate": "%{x}<br>%{y:.1f}<extra></extra>"
    }

  Notes:
    Plotly has no native "line" type — always use scatter with mode: "lines".
    For multiple series, add more traces with different y aliases.

──────────────────────────────────

6. AREA (type: "scatter" with fill)
  Area under a line (fill to baseline or to another trace).

  Trace structure:
    {
      "type": "scatter",
      "mode": "lines",
      "fill": "tozeroy",
      "meta": { "columnNames": { "x": ["area_name"], "y": ["total"] } },
      "name": "Population",
      "line": { "color": "#2563eb", "width": 2 },
      "hovertemplate": "%{x}<br>%{y:,.0f}<extra></extra>"
    }

  fill values:
    "tozeroy" — fill to y=0 baseline
    "tonexty" — fill to the previous trace's y values (stacked areas)
    "tozerox" — fill to x=0

  For stacked areas:
    Two traces, both with fill: "tonexty", ordered bottom→top.

──────────────────────────────────

7. BOX (type: "box")
  Statistical distribution showing quartiles and outliers.

  Trace structure:
    {
      "type": "box",
      "meta": { "columnNames": { "y": ["value"] } },
      "name": "Distribution",
      "boxmean": "sd",
      "hovertemplate": "%{y}<br>%{x}<extra></extra>"
    }

  Key properties:
    boxmean: true (mean line) | "sd" (mean ± std dev line)
    notched: true (notched boxplot)
    boxpoints: "outliers" (default) | "all" | "suspectedoutliers" | false
    With x grouping: { "x": ["group_col"], "y": ["value_col"] }

──────────────────────────────────

REFERENCE LINE OVERLAY (bar + line)
  When getData() includes a reference_value column (via
  lastlyAreaLeftJoinData with referenceValueToInclude):

  Trace 1 (bars — main data):
    { "type": "bar", "meta": { "columnNames": { "x": ["area_name"], "y": ["value"] } } }

  Trace 2 (line — reference):
    { "type": "scatter", "mode": "lines+markers",
      "meta": { "columnNames": { "x": ["area_name"], "y": ["reference_value"] } },
      "line": { "color": "red", "width": 2, "dash": "dash" },
      "name": "Reference" }

──────────────────────────────────

MULTI-AXIS LAYOUT (two y-axes)
  When one trace should use the right axis:

  Trace 1 (bars — left axis):
    { ... no yaxis attribute ... }

  Trace 2 (line — right axis):
    { ..., "yaxis": "y2" }

  Layout:
    {
      "yaxis": { "title": { "text": "Left Axis Label" } },
      "yaxis2": { "title": { "text": "Right Axis Label" }, "overlaying": "y", "side": "right" }
    }

──────────────────────────────────

LAYOUT ADJUSTMENTS
  The Chart base class always injects `PlotlyDefaults::DEFAULT_LAYOUT` as the
  starting layout. The agent only needs to pass the properties it wants to
  **override** in the `layout` field of `edit-chart`. Values already set by
  default (omit unless overriding with a different value):

    showlegend: true
    legend: { orientation: "h", x: 0, y: 1.12 }
    xaxis: { type: "category", tickmode: "auto", automargin: true }
    yaxis2: { side: "right", overlaying: "y", showgrid: false }
    margin: { l: 60, r: 30, t: 15, b: 40 }
    modebar: { orientation: "v", color: "white", bgcolor: "darkgray" }
    dragmode: "pan"

  Override reference (pass any of these to change the default):

  Title:        { "title": { "text": "Chart Title" } }
  X axis:       { "xaxis": { "title": { "text": "Category" }, "tickangle": -45 } }
  Y axis:       { "yaxis": { "title": { "text": "Value" }, "tickprefix": "$" } }
  2nd Y axis:   { "yaxis2": { "title": { "text": "Rate" }, "overlaying": "y", "side": "right" } }
  Legend:       { "showlegend": true, "legend": { "orientation": "h", "x": 0, "y": 1.12 } }
  Margins:      { "margin": { "l": 60, "r": 30, "t": 15, "b": 40 } }
  Barmode:      { "barmode": "group" } | "stack" | "relative" | "overlay"

  Dynamic X-Axis Title (PHP property, not a Plotly layout field)
    Set `public bool $useDynamicAreaXAxisTitles = true;` in your indicator
    class to automatically update the x-axis label based on the current
    area filter level. At national level it reads "Counties", at county
    level it reads "Subcounty of {County Name}", and so on.

    Do NOT set this via edit-chart — it is a PHP class property. Edit the
    generated indicator file at `app/Livewire/Indicator/...` to add the
    property.

──────────────────────────────────

HOVERTEMPLATE FORMATTING
  %{y}             raw value
  %{y:.1f}         one decimal
  %{y:,.0f}        thousands separator, no decimals
  %{y:$.2f}        currency format
  %{x}             x value
  %{label}         pie label
  %{percent}       pie percentage
  %{fullData.name} trace name (shows in secondary box)
  <extra></extra>  hide secondary grey box

──────────────────────────────────

EXAMPLE: COMPLETE BAR CHART
  {
    "data": [
      {
        "type": "bar",
        "meta": { "columnNames": { "x": ["area_name"], "y": ["value"] } },
        "name": "Population",
        "marker": { "color": "#2563eb" },
        "hovertemplate": "%{x}<br>%{y:,.0f}<extra></extra>"
      }
    ],
    "layout": {
      "title": { "text": "Population by County" },
      "xaxis": { "title": { "text": "County" }, "tickangle": -45 },
      "yaxis": { "title": { "text": "Population" } },
      "margin": { "l": 60, "r": 30, "t": 15, "b": 40 },
      "showlegend": false
    }
  }

  Note: `margin` and the base `xaxis` properties (`type`, `tickmode`,
  `automargin`) are already part of the default layout — they are shown here
  for clarity but can be omitted in your edit-chart call if you don't need to
  override them. The indicator title is rendered by the card component above
  the chart, so `layout.title` is only shown here as an example — omit it
  unless the user explicitly asks for a chart subtitle.
DOC;
}
