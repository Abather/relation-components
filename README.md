
# Relation Components

![Relation Components](https://banners.beyondco.de/Relation%20Components.png?theme=light&packageManager=composer+require&packageName=abather%2Frelation-components&pattern=architect&style=style_1&description=Relation+%22BelongsTo%2C+MorphTo%22+TextColumn+and+TextEntry&md=1&showWatermark=1&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/abather/relation-components.svg?style=flat-square)](https://packagist.org/packages/abather/relation-components)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/abather/relation-components/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/abather/relation-components/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/abather/relation-components/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/abather/relation-components/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/abather/relation-components.svg?style=flat-square)](https://packagist.org/packages/abather/relation-components)

Filament table columns and infolist entries for `belongsTo` and `morphTo` relationships.

## Installation

```bash
composer require abather/relation-components
```

## Usage

### Table Column

```php
use Abather\RelationComponents\Tables\Columns\RelationColumn;
```

### Infolist Entry

```php
use Abather\RelationComponents\Infolists\Components\RelationEntry;
```

---

### `belongsTo`

Displays the related record's title with a link to its resource page.

```php
RelationColumn::belongsTo(UserResource::class)
```

All parameters are optional except `$resource`:

```php
RelationColumn::belongsTo(
    resource:       UserResource::class,
    relation:       'user',         // auto-derived from model class name
    titleAttribute: 'name',         // auto-derived from resource configuration
    label:          'User',         // auto-derived from resource model label
    page:           'view',         // default: 'view'
    withIcon:       true,           // default: true
)
```

---

### `morphTo`

Displays a polymorphic related record with a link to its resource page.

```php
RelationColumn::morphTo(
    relation: 'subject',
    types: [
        Farm::class => FarmResource::class,
        Plot::class => PlotResource::class,
    ],
)
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

### Customization

Both methods return a standard `TextColumn` / `TextEntry` instance, so any method from those classes can be chained after to override the defaults:

```php
RelationColumn::belongsTo(UserResource::class)
    ->color('success')
    ->icon('heroicon-o-user')
    ->openUrlInNewTab(false)
    ->label('Owner')
```

```php
RelationColumn::morphTo('subject', [
    Farm::class => FarmResource::class,
    Plot::class => PlotResource::class,
])->color('warning')
  ->icon(null)
```

---

Both methods work identically on `RelationEntry` for infolists.
