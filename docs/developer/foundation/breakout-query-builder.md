---
outline: deep
---

# Breakout Query Builder

In the [Understanding CSPro Data](/developer/foundation/understanding-cspro-data) section, we cover how data is organized in the "breakout" database. Since the joining of the various tables follows a constant and predictable pattern, we have built that logic into the **BreakoutQueryBuilder**.

The `BreakoutQueryBuilder` class is a sophisticated **Data Access Layer** designed specifically to bridge the gap between the breakout database and a modern dashboard application. It acts as a fluent wrapper that automates the tedious parts of joining CSPro's hierarchical tables while integrating geographic metadata.

## Core Functionality

The class automates the construction of SQL queries that follow the CSPro Breakout schema logic:

1. **Automatic Boilerplate Joins:** It automatically joins the `level-1` table with the `cases` table.
2. **CSPro Metadata Filtering:** It handles the filtering of deleted cases and partial saves by default, ensuring your analytics only show finalized, valid data.
3. **Geographic Enrichment:** It performs post-query joining, merging the raw SQL result with geographic `Area` records. This ensures that areas with zero data still appear and that proper area names are displayed instead of cryptic codes.

## Key Features

### Fluent Interface (Method Chaining)

It provides a human-readable way to build queries. You do not have to manually write complex `INNER JOIN` strings every time you want to access a record, reducing syntax errors and making the code easier to maintain.

### Intelligence through QueryFragmentFactory

In the constructor, it uses a factory to determine which columns and tables are required based on a `filterPath`. This allows the builder to dynamically know your geographic hierarchy — if the user is looking at a specific County, the builder automatically adds the necessary `WHERE` clauses for that path.

### Handling the "Empty Area" Problem

In standard SQL, a `GROUP BY` will not show an area if there are zero records for it. This class solves this via:

- **`lastlyAreaLeftJoinData`:** After getting data from SQL, it compares the result to the `Area` tree. If a district is missing from the data, it injects a skeleton row with zeroes.
- **`lastlyAreaCrossJoinData`:** Useful for matrices (e.g., Age Group vs. Region). It ensures every combination exists in the final collection, even if the count is zero.

### Safety and Cleanliness

- **Soft-Delete Awareness:** Automatically adds `cases.deleted = 0`.
- **Partial Save Awareness:** Automatically adds `cases.partial_save_mode IS NULL`.
- **X-Ray Debugging:** Includes a logging mechanism (`xRay`) to trace the raw SQL and the transformation process, invaluable for debugging complex census indicators.

### Logic Separation

It separates **Database Querying** from **Data Enrichment**. The SQL engine does the heavy lifting (aggregating millions of rows), and PHP handles the final beautification (mapping area names and calculating percentages).

## Example Usage

```php
(new BreakoutQueryBuilder($dataSource, $filterPath))
    ->select(['SUM(total_household_members) AS population', 'COUNT(*) AS households'])
    ->from(['housing_rec']) // Automatically joins housing_rec -> level-1 -> cases
    ->groupBy(['area_code'])
    ->lastlyAreaLeftJoinData() // Ensures districts with 0 houses still show up
    ->get()
```

### Summary of Benefits

| Feature | Benefit |
| :--- | :--- |
| **Encapsulation** | You do not need to know the internal structure of CSPro `level-1-id` linking. |
| **Consistency** | Every indicator in your app will filter deleted/partial cases the same way. |
| **Completeness** | Reports will show all geographic areas, not just those with data. |
| **Developer Velocity** | Writing a new indicator takes minutes instead of hours of SQL debugging. |

This class is essentially a mini-ORM specifically tuned for the unique quirks of census and survey data management.
