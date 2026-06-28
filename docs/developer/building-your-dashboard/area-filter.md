---
outline: deep
---

# Area Filter

The **Geographic Area Filter** is a hierarchical navigation tool that allows users to narrow down dashboard data from a broad national perspective to highly localized granularities. This system uses a parent-child relationship between levels, ensuring that data remains contextually accurate as the user zooms into specific regions.

To accommodate different user workflows, the system operates in two synchronized modes: **Hierarchical Drill-Down** and **Search & Jump**.

## Applying the Filter

The filter does **not apply automatically** as you select dropdowns or type in search. You must explicitly click **Apply** to commit your selection. The **Clear** button resets all selections and removes the active filter. Your selection persists across page navigation until you either Apply a new one or Clear it.

## Hierarchical Drill-Down (Cascading)

This mode is ideal for users exploring data through a structured administrative lens.

- **Top-Down Logic:** Each selection filters the subsequent level. Choosing a primary region (e.g., a County) automatically populates the next dropdown with only the relevant sub-areas (e.g., Subcounties within that County).
- **Data Integrity:** This cascading effect prevents mismatched geographic selections, ensuring users only view valid administrative paths.
- **Flexible Depth:** Users can choose to apply filters at any tier — viewing data for an entire region or narrowing the scope down to the most granular local level.
- **Explicit Apply:** The filter only takes effect after clicking **Apply**. Selecting dropdown values alone does not update the dashboard.
- **Area Restrictions:** If your account has an area restriction, your top-level selection appears as a **fixed label** you cannot change. You can still drill down into its sub-areas.

## Search & Jump

For users who know exactly which area they need, the **Search Mode** bypasses the hierarchy for immediate results.

- **Level-Agnostic Discovery:** Users can search for any area name across the entire database, regardless of which administrative level it belongs to.
- **Minimum Characters:** Type at least **2 characters** to trigger the search.
- **Result Format:** Results show the area name and its level — e.g., "Nairobi — County".
- **Explicit Apply:** Selecting a result does **not** apply immediately. You still need to click **Apply** to update the dashboard.
- **Instant Jump:** Once applied, the dashboard focuses on the selected area, skipping the manual step-by-step drill-down process.
- **Area Restrictions:** If your account has an area restriction, you can only search within areas below your restriction level.

## Bi-Directional Sync

A key feature of the Area Filter is the **bi-directional synchronization** between the two modes. Switching between Search and Drill-Down never resets the user's progress. If a user searches for a specific local area and selects it, switching back to the drill-down view will show the entire breadcrumb path pre-selected in the dropdowns.

Area restrictions are always enforced — you cannot switch modes to bypass your assigned geographic boundary.

## Area Restrictions

Some user accounts are restricted to specific geographic areas. When restricted:

- Your assigned area appears as a **fixed label** (not a dropdown).
- You can **only navigate downward** in the hierarchy — to sub-areas of your restriction.
- You **cannot** navigate upward to parent areas or sideways to peer areas.
- This restriction applies in **both Drill-Down and Search modes**.

## Granularity on Area Insights

By default, the Area Filter stops at the second-to-last administrative level on most dashboard pages. The **Area Insights** page is the exception — it allows filtering all the way to the most granular level.
