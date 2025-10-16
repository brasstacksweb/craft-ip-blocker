# Release Notes for IP Blocker

## 1.0.0
- Initial release

## 1.0.1
- Updated Blocker service to use null-safe operator when accessing previous exception.

## 1.1.0 - 2025-05-22
- Added pagination to Failed Attempts and Blocks listing pages

## 1.2.0 - 2025-10-16
- Simplified exception handling to match against any Exception type instead of only HttpException
- Added validation rules to Condition model to ensure required fields
- Improved pattern matching logic with better error handling

## 1.3.0 - 2025-10-16
- Added sortable table columns for both attempts and blocks views
- Changed default sort order to show newest records first
