<?php

namespace Abather\RelationComponents\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;

trait ResolvesResourceRelations
{
    /**
     * @param  class-string<\Filament\Resources\Resource>  $resource
     */
    protected static function resolveTitleAttribute(string $resource): string
    {
        return $resource::getRecordTitleAttribute() ?? app($resource::getModel())->getKeyName();
    }

    /**
     * @return class-string<\Filament\Resources\Resource> | null
     */
    protected static function resolveMorphResource(Model $record, string $relation, array $types): ?string
    {
        $morphRelation = $record->{$relation}();

        if ($morphRelation instanceof MorphTo) {
            $morphType = data_get($record, $morphRelation->getMorphType());

            if ($morphType) {
                $modelClass = Relation::getMorphedModel($morphType) ?? $morphType;

                return $types[$modelClass] ?? null;
            }
        }

        $related = $record->{$relation};

        if (! $related) {
            return null;
        }

        return $types[get_class($related)] ?? null;
    }

    /**
     * @param  class-string<\Filament\Resources\Resource>  $resource
     */
    protected static function getRecordUrl(string $resource, ?Model $record, string $page): ?string
    {
        if (blank($record) || blank($resource) || ! $resource::hasPage($page)) {
            return null;
        }

        return $resource::getUrl($page, ['record' => $record]);
    }
}
