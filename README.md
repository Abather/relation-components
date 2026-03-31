
# Relation Components

![Relation Components](https://banners.beyondco.de/Relation%20Components.png?theme=light&packageManager=composer+require&packageName=abather%2Frelation-components&pattern=architect&style=style_1&description=Relation+%22BelongsTo%2C+MorphTo%22+TextColumn+and+TextEntry&md=1&showWatermark=1&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

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

## Global Configuration

Since these classes extend Filament's `TextColumn` and `TextEntry`, they can be configured globally in your `AppServiceProvider` the same way:

```php
use Abather\RelationComponents\Tables\Columns\BelongsToColumn;
use Abather\RelationComponents\Tables\Columns\MorphToColumn;
use Abather\RelationComponents\Infolists\Components\BelongsToEntry;
use Abather\RelationComponents\Infolists\Components\MorphToEntry;

public function boot(): void
{
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

## Visibility on Parent Pages

By default, columns and entries are automatically hidden when shown inside the related resource's own relation manager pages. For example, a `UserResource` column will be hidden on any of User's relation manager pages.

To override this behavior, chain `->hiddenOn([])`:

```php
BelongsToColumn::make(UserResource::class)
    ->hiddenOn([])
```
