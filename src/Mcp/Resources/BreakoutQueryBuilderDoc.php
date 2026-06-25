<?php

namespace Uneca\Chimera\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Description('Full BreakoutQueryBuilder API reference — method chain, join strategies, and usage patterns for all artefact types.')]
#[Uri('docs://breakout-query-builder')]
class BreakoutQueryBuilderDoc extends Resource
{
    public function handle(Request $request): Response
    {
        return Response::text(self::CONTENT);
    }

    private const CONTENT = <<<'DOC'
BreakoutQueryBuilder API Reference
===================================

PURPOSE
  Builds and executes queries against the MySQL breakout database.
  Always use this class in getData() — never raw DB:: queries.

CONSTRUCTOR
  new BreakoutQueryBuilder(
    string $dataSource,           // Connection name from get-data-sources
    string $filterPath = '',      // LTree area path ("" = national)
    bool $excludePartials = true  // Filters partial_save_mode IS NOT NULL
  )

METHOD CHAIN (call in this order)
  select(array $items)       -> SELECT col1, col2, ...
  from(array $items)         -> FROM table1, table2 (ALWAYS an array)
  where(array $items)        -> WHERE condition1 AND condition2
                               (array of RAW SQL STRINGS, joined with AND)
  groupBy(array $items)      -> GROUP BY col1, col2
  having(array $items)       -> HAVING cond1 AND cond2
  orderBy(array $items)      -> ORDER BY col1, col2

  lastlyAreaLeftJoinData(
    string $joinColumnOnDataSide = 'area_code',
    ?string $referenceValueToInclude = null
  )                           All areas shown, data null-padded for missing areas
  lastlyAreaRightJoinData(
    string $joinColumnOnDataSide = 'area_code',
    ?string $referenceValueToInclude = null
  )                           Only areas with data shown, no padding
  lastlyAreaCrossJoinData(...) Cartesian product (rarely needed)

  get()                       -> Collection (multi-row, for indicators/reports)
  getSingleRow()              -> Collection (0-1 rows, for scorecards/gauges)

CRITICAL RULES
  1. where() takes an array of RAW SQL STRINGS joined with AND:
     ->where(["sex = 'Male'", "age > 18"])
     NOT key-value pairs — do NOT use ->where('sex', 'Male')
     Each condition is a raw SQL fragment passed verbatim into the WHERE clause.
  2. from() always takes an ARRAY:
     ->from(['housing_rec'])
     NOT a string — do NOT use ->from('housing_rec')
     Pass multiple tables: ->from(['housing_rec', 'pop_rec'])
  3. groupBy(['area_code', ...]) is REQUIRED when using aggregate functions
     with lastlyAreaLeftJoinData() or lastlyAreaRightJoinData().
  4. Column aliases (AS alias) must match Plotly meta.columnNames exactly.
  5. Table names come from the CSPro dictionary RECORDS, lowercased.
     Read the dictionary via read-dictionary before writing queries.
  6. Column names come from dictionary ITEMS.
  7. getSingleRow() strips groupBy/having/orderBy — use only for scalar
     aggregates (scorecards and gauges that return a single value).

REFERENCE VALUES (OPTIONAL)
  Include only if the user asks for comparisons or reference lines.
  Pass the indicator name to the lastlyArea*() method:
    ->lastlyAreaLeftJoinData('area_code', 'population')
    ->lastlyAreaRightJoinData('area_code', 'number_of_hh')
  This adds a reference_value column to every result row.
  In getData():
    Scorecards/Gauges — compute diff = value - reference_value
    Indicators — add a second Plotly trace using the reference_value column
  Call get-reference-values to discover available indicator names.

WHERE CLAUSE EXAMPLES
  Single condition:         ->where(["sex = 'Male'"])
  Multiple conditions:      ->where(["sex = 'Male'", "age > 18"])
  With aggregate filter:    ->where(["sex = 'Male'", "total > 0"])
  Using IN:                ->where(["area_code IN ('001', '002', '003')"])
  Date comparison:         ->where(["interview_date >= '2024-01-01'"])

PATTERNS BY ARTEFACT TYPE

  Indicator (multi-row, grouped by area):
    (new BreakoutQueryBuilder($this->indicator->data_source, $filterPath))
        ->select(['SUM(col) AS value'])
        ->from(['table_name'])
        ->groupBy(['area_code'])
        ->lastlyAreaLeftJoinData()
        ->get();

  Scorecard (single scalar value):
    (new BreakoutQueryBuilder($this->scorecard->data_source, $filterPath))
        ->select(['COUNT(*) AS value', 'NULL AS diff'])
        ->from(['pop_rec'])
        ->getSingleRow();

  Gauge (single scalar value):
    (new BreakoutQueryBuilder($this->gauge->data_source, $filterPath))
        ->select(['COUNT(*) AS value'])
        ->from(['pop_rec'])
        ->getSingleRow();

  Map Indicator (multi-row, no area join needed):
    (new BreakoutQueryBuilder($this->mapIndicator->data_source, $filterPath))
        ->select(['COUNT(*) AS value'])
        ->from(['pop_rec'])
        ->groupBy(['area_code'])
        ->get();
    Requires $bins array and SELECTED_COLOR_CHART constant in the class.

  Report (multi-row, may use raw SQL):
    (new BreakoutQueryBuilder($this->report->data_source, $filterPath))
        ->select(['col1', 'col2'])
        ->from(['table_name'])
        ->get();
    Or pass raw SQL directly:
    (new BreakoutQueryBuilder($this->report->data_source))
        ->get('SELECT * FROM table_name LIMIT 10');

WITH POST-PROCESSING
    return (new BreakoutQueryBuilder(...))
        ->select(...)
        ->from(...)
        ->get()
        ->map(fn ($row) => (object) [
            'value' => Number::format(safeDivide($row->x, $row->y), 1),
            'diff' => null,
        ]);

WITH REFERENCE VALUE
    (new BreakoutQueryBuilder($this->indicator->data_source, $filterPath))
        ->select(['COUNT(*) AS value'])
        ->from(['pop_rec'])
        ->groupBy(['area_code'])
        ->lastlyAreaLeftJoinData('area_code', 'population')
        ->get();
    Result rows include: {area_name, area_code, value, reference_value}

DEBUG HELPERS
    ->dump()       Var-dumps the assembled SQL string (calls Laravel dump())
    ->debugLog()   Logs the SQL to the Laravel log via logger()
    Chain before get(): $qb->dump()->get();
DOC;
}
