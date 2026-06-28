---
---

# Troubleshooting

## Common Issues

### Blank or empty charts after publishing

1. **Check the indicator is published** — Navigate to Manage > Indicators and verify the status toggle is set to **Yes**.
2. **Check page assignment** — The indicator must be assigned to a published page.
3. **Run the Test action** — Click **Test** on the indicator management page to verify `getData()` returns data.
4. **Check developer logs** — Run `tail -f storage/logs/laravel.log` while testing the indicator.
5. **Enable X-Ray debugging** — Add `->xRay()` to your `BreakoutQueryBuilder` chain to see the generated SQL.

### "The current area level is inapplicable to this indicator"

The indicator has been configured with [Unsupported Area Levels](/developer/building-your-dashboard/hierarchial-compatibility) that exclude the currently selected geographic level. Either change the filter level or edit the indicator's unsupported levels.

### Developer mode not working

- Ensure you are logged in as a **Super Admin** (not just a Manager role).
- Try the 7-click method again — click in the blank area **above** the first horizontal divider on the profile page.
- Alternatively, set `APP_ENV=local` in your `.env` file to auto-enable developer mode.

### Cache shows stale data

1. Run `php artisan chimera:cache-clear` to clear all cached data.
2. Run the relevant cache command (e.g., `php artisan chimera:cache-indicators`) to rebuild.
3. Check that `CACHE_TTL_SECONDS` in your `.env` is set appropriately.

### "Class not found" after creating an artefact

Run `composer dump-autoload` to regenerate the autoloader. If the artefact was created via the web form, ensure the file exists in the expected directory under `app/Livewire`, `app/MapIndicators`, or `app/Reports`.

### File upload size limit exceeded

When importing shapefiles or CSV files, if you get an error stating the file must not be larger than 12MB, override the default limit in `config/livewire.php`:

```php
'rules' => [
    'file' => ['file', 'max:102400'], // 100MB
],
```

### Map indicators not showing on the map

- Verify the shapefiles were imported with the correct CRS (EPSG:4326 - WGS 84).
- Check that the `area_code` returned by your `getData()` matches the codes in your shapefiles.
- Ensure the map indicator is published and assigned to a map page.
- Verify `$bins` is defined on your class — it is mandatory.

## Log Files

- **Laravel log:** `storage/logs/laravel.log`
- **Queue log:** Check your queue driver configuration (sync by default in development).
- **Query analytics:** Slow queries (>10s by default) are logged in the database and viewable via Manage > Query Analytics.

## Getting Help

If you cannot resolve an issue using this guide, please visit our [discussion board](https://github.com/orgs/tech-acs/discussions) for community support.

