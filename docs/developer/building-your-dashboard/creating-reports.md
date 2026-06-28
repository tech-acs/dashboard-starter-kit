---
---

# Creating reports

Reports are compiled tabular datasets presented as CSV or Excel file formats. They can be generated on demand or automatically on a set schedule, and can also be emailed automatically to designated dashboard users.

Like indicators, reports can be organized into different pages. You can assign a report to appear on one or more pages via the edit form.

## Creating Reports

There are two ways to create reports: a CLI command and a web form.

The first way is by running the `php artisan chimera:make-report` command and following the prompts. This works best on Linux/macOS/WSL environments.

The second way is by going to the Manage dashboard menu, selecting Reports, then pressing the **CREATE NEW** button and filling out the form as directed.

The report name must be in **CamelCase** (e.g., `KenyaCensus/PartialCasesByEa`). It becomes both the PHP class name and the file name, and will create subdirectories if you use forward slashes.

The creation form includes:
- **Data source** — The data source this report will query (required).
- **Report name** — The CamelCase class name (required).
- **Title** — A reader-friendly title shown in the UI (required, multilingual).
- **Description** — A short description (optional at creation, required when editing).

## Implementing Reports

You need to implement the `getData()` method so that it returns a `Collection`. The keys of the collection items will become the column headers of the report spreadsheet, and the values will become the rows.

## Publishing and Scheduling

After implementing the report, navigate to the report management page and click **Edit**. The edit form includes:

- **Name** — Displayed but disabled (cannot be changed after creation).
- **Title** and **Description** — Multilingual fields.
- **Page** — Assign the report to one or more pages so it appears in the page's report list.
- **Rank** — Controls the display order when multiple reports are listed.
- **Published** — Toggle switch. When set to **Yes**, the report becomes visible on its assigned pages.
- **Enabled** — Toggle switch. When set to **Yes**, the report can be generated on a schedule.
- **Run at** — The hour when the report should first run (server time).
- **Run every** — How frequently the report regenerates: every 3, 6, 12, or 24 hours.

From the report management index page, you can also click **Run now** to generate the report immediately without waiting for the scheduled time.