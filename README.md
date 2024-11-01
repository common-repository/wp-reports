# Reports for WordPress

WordPress reporting framework. Powerful reporting framework for WordPress. Written by developers for developers.

The plugin contains couple of built-in reports out of the box. It is very easy to implement a new report for your own plugin/theme.

## Taglines

- create custom reports directly within WordPress administration with ease

## Access Control

By default only administrators have access to all the reports (even if you override this in report settings).

We have also created a special WordPress capability called `wp_reports_view`. If you use a plugin to manage user roles, you can easy assign this capability to a role thus allowing all users with this role to see the reports.

## Filters

### wpr-available-reports

This hooks allows you to modify a list of available reports. By default it contains
all built-in reports. Use should use this hook to add custom reports.

#### Parameters

- `$reports (array)` - List of available reports. The key is used as a unique ID of the reports thoughout the plugin. The value is an array described in more detail below (see **Report attributes**).

##### Report attributes
- `name (string)` - Name of the report. Used in navigation and report list.
- `group_id (string)` - ID of the group the report belongs to.
- `filters (array)` - List of filters.
  - `date_range` - Allows filtering by start and end date. Enabled by default. Defaults to last 30 days. Uses keys `date_from` and `date_to` when loading data.
  - custom filter (see **Filter attributes** below)
- `data_callback (callable)` - The callback to be run when report data is needed.
- `minimum_capability (string)` - Minimal capability a user must have to see the report. Defaults to `wp_reports_view`.
- `suitable_visualizations` (string|array) - List of suitable visualizations. Accepted values are 'tabular' and 'timeline'.
- `export_supported (bool)` - Optional, enabled by default. Enable export of tabular data.

##### Filter attributes
- `filter_id` (string)
- `label (string)`
- `values (array)` - Associative array of possible values.
- `default_value (int|string)` - Optional. Default value for the filter.

##### Data callback arguments
- `report_id` (string) - Report ID.
- `settings` (array) - An array of settings to narrow down the data required. Contains data from all defined report filters (the keys match the filter IDs) plus the following:
  - `date_from (int)`
  - `date_to (int)`
  - `page (int)`
  - `per_page (int)`
  - `visualization (string)`

### wpr-available-groups
- `id (string)` - Group ID. Used to assign report to a group.
- `name (string)` - Name of the group.

## Data format
- `labels`
- `highlights`
- `values`

## Modules

There are couple of reports bundled within the core of this plugin. Each modules is a separate file in "modules" folder and it must contain a class that implements <code>DS_WP_Reports_ModuleInterface</code>.

## Add-on ideas
- addon to allow admins to select specific users for each report via a neat UI
- Contact Form 7 add-on
- PostMan add-on

## Todo
- Filter for report filters - `wpr-filters-<report_id>`, useful to remove default filters such as date range.
- Allow autocomplete select for report filters. Only static list is supported at the moment.
- Timeline supports only grouping by day, it would be hancy to add grouping by other time periods (week, month, quarter, year).
- Add more visualization types.
- Allow the minimum capability to be a callback that returns true or false. This would allow third parties have more control over access to plugin. Someone might want to implement a UI for selecting users who are allowed to see a certain report.
- Multiple data series when only 1 data series defined and only 1 filter available
- Allow 3-rd parties to display selected reports on a separate admin page.
- Add shortcode support to be able to show the chart anywhere.