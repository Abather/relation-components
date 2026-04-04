
# Relation Components

![Relation Components](https://i.ibb.co/dSj32xy/relation-components-banner.jpg)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/abather/relation-components.svg?style=flat-square)](https://packagist.org/packages/abather/relation-components)
[![Total Downloads](https://img.shields.io/packagist/dt/abather/relation-components.svg?style=flat-square)](https://packagist.org/packages/abather/relation-components)

Filament table columns and infolist entries for `belongsTo` and `morphTo` relationships.

## Requirements

- [Filament](https://filamentphp.com) v4+

## Installation

```bash
composer require abather/relation-components
```

---

## Usage

### `BelongsToColumn` / `BelongsToEntry`

Pass the resource class directly to `make()`. All configuration is derived automatically.

```php
use Abather\RelationComponents\Tables\Columns\BelongsToColumn;
use Abather\RelationComponents\Infolists\Components\BelongsToEntry;

BelongsToColumn::make(UserResource::class)

BelongsToEntry::make(UserResource::class)
```

Available options (all chainable):

```php
BelongsToColumn::make(UserResource::class)
    ->page('edit')          // default: 'view'
    ->withIcon(false)       // default: true
    ->label('Owner')
    ->color('success')
```

---

### `MorphToColumn` / `MorphToEntry`

Pass the relation name to `make()` and provide the type map via `->types()`.

```php
use Abather\RelationComponents\Tables\Columns\MorphToColumn;
use Abather\RelationComponents\Infolists\Components\MorphToEntry;

MorphToColumn::make('subject')
    ->types([
        Farm::class => FarmResource::class,
        Plot::class => PlotResource::class,
    ])

MorphToEntry::make('subject')
    ->types([
        Farm::class => FarmResource::class,
        Plot::class => PlotResource::class,
    ])
```

> **N+1 warning:** eager-load the relation to avoid per-row queries.
> ```php
> // In your resource:
> public static function getEloquentQuery(): Builder
> {
>     return parent::getEloquentQuery()->with('subject');
> }
> ```

---

### `BelongsToSelect`

A form select for `belongsTo` relationships. Pass the resource class to `make()` — the relation, label, and title attribute are all derived automatically.

```php
use Abather\RelationComponents\Forms\Components\BelongsToSelect;

BelongsToSelect::make(UserResource::class)
```

Available options (all chainable):

```php
BelongsToSelect::make(UserResource::class)
    ->withIcon(false)       // default: true — shows resource icon as suffix
    ->preload(false)        // default: true
    ->label('Assigned To')
```

---

## Global Configuration

Since these classes extend Filament's `TextColumn` and `TextEntry`, they can be configured globally in your `AppServiceProvider` the same way:

```php
use Abather\RelationComponents\Forms\Components\BelongsToSelect;
use Abather\RelationComponents\Tables\Columns\BelongsToColumn;
use Abather\RelationComponents\Tables\Columns\MorphToColumn;
use Abather\RelationComponents\Infolists\Components\BelongsToEntry;
use Abather\RelationComponents\Infolists\Components\MorphToEntry;

public function boot(): void
{
    BelongsToSelect::configureUsing(fn (BelongsToSelect $select) => $select
        ->preload(false)
        ->withIcon(false)
    );

    BelongsToColumn::configureUsing(fn (BelongsToColumn $column) => $column
        ->color('primary')
        ->openUrlInNewTab(false)
    );

    MorphToEntry::configureUsing(fn (MorphToEntry $entry) => $entry
        ->color('warning')
    );
}
```

---

## Customization

Any method from `TextColumn` / `TextEntry` can be chained to override the defaults:

```php
BelongsToColumn::make(UserResource::class)
    ->color('success')
    ->icon('heroicon-o-user')
    ->openUrlInNewTab(false)

MorphToEntry::make('subject')
    ->types([Farm::class => FarmResource::class])
    ->color('warning')
    ->icon(null)
```

---

## Authorization Visibility

By default, columns and entries are always rendered regardless of whether the current user is authorized to view the related record's page. You can change this by calling `->hideWhenNotAuthorizedToView()`, which will completely hide the field if the user does not have permission to view the related resource.

```php
BelongsToColumn::make(UserResource::class)
    ->hideWhenNotAuthorizedToView()

BelongsToEntry::make(UserResource::class)
    ->hideWhenNotAuthorizedToView()

MorphToEntry::make('subject')
    ->types([Farm::class => FarmResource::class])
    ->hideWhenNotAuthorizedToView()
```

When this is enabled, the visibility is determined as follows:

- If no related record is loaded yet (e.g. on a list page), `canViewAny()` on the resource is checked.
- If a related record is available, `canView($record)` on the resource is checked.

The field is hidden entirely when the check fails — the user will not see the field at all, not just an empty value.

> **URL authorization:** Even without `->hideWhenNotAuthorizedToView()`, the link is always authorization-aware. The URL is only generated when all of the following are true: a related record exists, the target page exists on the resource, and `canView($record)` passes. If the user is not authorized to view the record, the field renders as plain text with no link.

---

## Visibility on Parent Pages

By default, columns and entries are automatically hidden when shown inside the related resource's own relation manager pages. For example, a `UserResource` column will be hidden on any of User's relation manager pages.

To override this behavior, chain `->hiddenOn([])`:

```php
BelongsToColumn::make(UserResource::class)
    ->hiddenOn([])
```
