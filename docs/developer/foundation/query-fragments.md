---
outline: deep
---

# Query Fragments

The `QueryFragments` class serves as the **Geographic Routing Engine** for your data source. While the [BreakoutQueryBuilder](/developer/foundation/breakout-query-builder) provides the structural skeleton of the SQL query, the `QueryFragments` class provides the spatial intelligence required to drill down into specific administrative levels.

## Core Functionality

This class translates a hierarchical `filterPath` (from the Area Filter) into specific SQL `SELECT` and `WHERE` clauses. It ensures that as a user navigates deeper into the geographic tree, the query automatically shifts its focus to the next logical administrative level.

## Key Features

### Dynamic Drill-Down Logic

The class uses a top-down conditional structure. It detects the lowest level currently selected in the filter and automatically sets the **next level down** as the `area_code`. For example, if you are looking at a **County**, the class sets the `area_code` to be **Subcounty**. This enables drill-down reporting without writing separate queries for every level.

### Data Normalization via `LPAD`

CSPro data often stores geographic codes as integers, which can lose leading zeros (e.g., `1` instead of `01`). The `LPAD(column, length, '0')` function ensures codes are treated as standardized strings, preventing join failures when comparing database integers against standardized geographic codes from your `Areas` table.

### Context-Aware Filtering

The class maintains the parental context in the `WHERE` clause. For example, if filtering by a specific **Subcounty**, the generated query does not just filter by `subcounty_code` — it includes the IDs for the **County** as well. This ensures data integrity, especially in cases where code IDs might repeat across different parent areas.

## How It Plugs into the Query Builder

This class acts as a plugin for the `BreakoutQueryBuilder`. When the builder is initialized:

1. It calls `getSqlFragments($filterPath)`.
2. The `QueryFragments` class returns an array containing the `area_code` definition and the matching geographic conditions.
3. The Builder merges these into its `$columns` and `$conditions` arrays, which are later turned into the `SELECT` and `WHERE` clauses of the query.

## Logic Walkthrough

| If the Filter Path is... | The `selectColumns` becomes... | The `whereConditions` filters by... |
| :--- | :--- | :--- |
| **Empty** (National Level) | `county` (The top-level units) | Nothing (returns all counties) |
| **County "01"** | `subcounty` | `county = '01'` |
| **Sublocation "10"** | `ea` (Enumeration Area) | `county`, `subcounty`, `division`, `location`, `sublocation` |

## Advantages for the Developer

- **Single Source of Truth:** The mapping between database columns (like `division`) and their administrative meaning is defined in one place.
- **Abstraction of Complexity:** You simply pass a `$filterPath` string; you do not need to manually calculate which levels are parents or children.
- **Scalability:** If the administrative structure changes (e.g., adding a "Ward" level), you only need to update this fragment class rather than every individual indicator in the dashboard.

## Implementation

When you create a data source, a `QueryFragments` class is automatically generated for you in `app/Services/QueryFragments/`. The generated class ships with the `$levels` array commented out as a template — you must uncomment it and customize the column names and padding lengths to match your database schema.

You can also regenerate the class at any time with `php artisan chimera:make-queryfragment`.

The `$levels` array maps each administrative level name to its corresponding SQL expression. The array keys **must match the area hierarchy level names** you created in the management interface; otherwise the filter detection in the loop will not match. The `LPAD` function ensures codes are zero-padded to the correct length.

```php
protected array $levels = [
    'County'      => "LPAD(county, 2, '0')",
    'Subcounty'   => "LPAD(subcounty, 2, '0')",
    'Division'    => "LPAD(division, 2, '0')",
    'Location'    => "LPAD(location, 2, '0')",
    'Sublocation' => "LPAD(sublocation, 2, '0')",
    'EA'          => "LPAD(ea, 3, '0')",
];

public function getSqlFragments(string $filterPath): array
{
    $filter = AreaTree::pathAsFilter($filterPath);
    $hierarchy = array_keys($this->levels);

    $selectColumns = [current($this->levels) . " AS area_code"];
    $fromTables = [];
    $whereConditions = [];

    for ($i = 0; $i < count($hierarchy); $i++) {
        $currentLevel = $hierarchy[$i];
        $nextLevel = $hierarchy[$i + 1] ?? null;

        if (blank($filter[$currentLevel] ?? null)) {
            break;
        }

        if ($nextLevel) {
            $selectColumns = ["{$this->levels[$nextLevel]} AS area_code"];
        }
        $whereConditions[] = "{$this->levels[$currentLevel]} = '{$filter[$currentLevel]}'";
    }

    return [$selectColumns, $whereConditions, $fromTables];
}
```

The loop iterates through the hierarchy levels, accumulating `WHERE` conditions for each level that has a filter value, and selecting the next level down as the `area_code`.
