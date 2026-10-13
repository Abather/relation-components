# Changelog

All notable changes to `abather/relation-components` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-10-13

First stable release.

### Added

- `BelongsToColumn`, `BelongsToEntry` and `BelongsToSelect` accept a custom relationship name as the second argument to `make()`, for relations not named after their model (e.g. `BelongsToColumn::make(UserResource::class, 'author')`). This also allows the same resource to be used more than once in a table.
- `BelongsToSelect` accepts a custom foreign key as the third argument to `make()`.
- `hiddenOnParentPages(bool)` to turn off hiding on the related resource's relation manager pages.
- The link falls back to the `edit` page when the resource has no `view` page.
- When a custom relationship name is given, the label defaults to the headline of the relation (e.g. `updatedBy` → "Updated By") instead of the model label.

### Changed

- **Breaking:** `BelongsToSelect` is now named after the foreign key (e.g. `category_id`) instead of the relation (`category`). Code that reads the form data by key, such as `mutateFormDataBeforeCreate()`, must use the new key.
- **Breaking:** links to the `edit` page now require `canEdit()` instead of `canView()`.
- **Breaking:** the built-in parent-page and authorization checks are combined into one `hidden()` condition. Calling `->hidden()` or `->hiddenOn()` on a component replaces both; use `->visible()` to add your own condition. Use `->hiddenOnParentPages(false)` instead of `->hiddenOn([])`.
- Shared logic moved into the `ConfiguresBelongsTo`, `HasMorphTypes` and `InteractsWithRelatedRecord` traits.

### Fixed

- Hiding on parent relation manager pages had no effect on `BelongsToColumn` and `BelongsToEntry`.
- `hideWhenNotAuthorizedToView()` had no effect on `MorphToEntry` once `types()` was called.
- `BelongsToEntry` checked `canView()` against the owner record instead of the related record.
- `withUrl(false)` had no effect.
- Relation managers registered inside a `RelationGroup` or as `RelationManager::make([...])` were not detected as parent pages.
- `BelongsToSelect` failed to create records when the foreign key column is `NOT NULL`, because the value was saved only after the insert.
- The `MorphToColumn` / `MorphToEntry` resource cache did not cache unresolved types.

[1.0.0]: https://github.com/abather/relation-components/releases/tag/v1.0.0
