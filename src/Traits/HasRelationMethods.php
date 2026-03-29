<?php

namespace Abather\RelationComponents\Traits;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

trait HasRelationMethods
{
    /**
     * @param  class-string<\Filament\Resources\Resource>  $resource
     */
    public static function belongsTo(
        string $resource,
        ?string $relation = null,
        ?string $titleAttribute = null,
        ?string $label = null,
        ?string $page = 'view',
        bool $withIcon = true
    ): static {
        $titleAttribute ??= static::resolveTitleAttribute($resource);
        $label ??= $resource::getModelLabel();
        $relation ??= str($resource::getModel())->classBasename()->camel()->value();
        $icon = $withIcon ? $resource::getNavigationIcon() : null;

        /** @var static $instance */
        $instance = static::make("{$relation}.{$titleAttribute}");

        return $instance
            ->resourceClass($resource)
            ->relationName($relation)
            ->titleAttribute($titleAttribute)
            ->page($page)
            ->withIcon($withIcon)
            ->label($label)
            ->color('info')
            ->icon($icon)
            ->url(fn ($record) => static::getRecordUrl($resource, data_get($record, $relation), $page))
            ->openUrlInNewTab();
    }

    /**
     * @param  array<class-string, class-string<\Filament\Resources\Resource>>  $types
     *                                                                                  Keyed by fully-qualified model class, e.g.:
     *                                                                                  [ Farm::class => FarmResource::class, Plot::class => PlotResource::class ]
     *
     * @note    N+1 risk: each row will trigger a separate query unless the relation is
     *          eager-loaded. Add ->with('relation') to your resource's getEloquentQuery()
     *          or use ->modifyQueryUsing(fn ($q) => $q->with('relation')).
     */
    public static function morphTo(
        string $relation,
        array $types,
        ?string $label = null,
        ?string $page = 'view',
        bool $withIcon = true
    ): static {
        $label ??= str($relation)->headline()->value();

        // Create a closure-scoped cache
        $cache = [];

        // Helper closure to get cached resource
        $getCachedResource = function (Model $record) use ($relation, $types, &$cache) {
            $recordId = $record->getKey();

            if (! isset($cache[$recordId])) {
                $cache[$recordId] = static::resolveMorphResource($record, $relation, $types);
            }

            return $cache[$recordId];
        };

        /** @var static $instance */
        $instance = static::make("{$relation}_type");

        return $instance
            ->relationName($relation)
            ->page($page)
            ->withIcon($withIcon)
            ->label($label)
            ->color('info')
            ->icon(function ($record) use ($getCachedResource, $withIcon) {
                if (! $withIcon) {
                    return null;
                }

                $resource = $getCachedResource($record);

                return $resource ? $resource::getNavigationIcon() : null;
            })
            ->formatStateUsing(function ($record) use ($relation, $getCachedResource) {
                $related = data_get($record, $relation);

                if (! $related) {
                    return '-';
                }

                $resource = $getCachedResource($record);

                $titleAttribute = $resource
                    ? static::resolveTitleAttribute($resource)
                    : $related->getKeyName();

                return data_get($related, $titleAttribute) ?? '-';
            })
            ->url(function ($record) use ($relation, $getCachedResource, $page) {
                $resource = $getCachedResource($record);
                $related = data_get($record, $relation);

                if (! $resource || ! $related) {
                    return null;
                }

                return static::getRecordUrl($resource, $related, $page);
            })
            ->openUrlInNewTab();
    }
}
