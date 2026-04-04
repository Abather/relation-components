<?php

namespace Abather\RelationComponents\Traits;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;

trait ResolvesResourceRelations
{
    /**
     * @param  class-string<resource>  $resource
     */
    protected static function resolveTitleAttribute(string $resource): string
    {
        return $resource::getRecordTitleAttribute() ?? app($resource::getModel())->getKeyName();
    }

    /**
     * @return class-string<resource> | null
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
     * @param  class-string<resource>|null  $resource
     */
    public function resolveHideWhenNotAuthorizedToView(?string $resource, ?Model $record = null): bool
    {
        if (! $this->getHideWhenNotAuthorizedToView()) {
            return false;
        }

        if (blank($resource)) {
            return false;
        }

        if (blank($record)) {
            return ! $resource::canViewAny();
        }

        return ! $resource::canView($record);
    }

    /**
     * @param  class-string<resource>  $resource
     */
    protected static function getRecordUrl(string $resource, ?Model $record, string $page): ?string
    {
        if (blank($record) || blank($resource) || ! $resource::hasPage($page)) {
            return null;
        }

        if (! $resource::canView($record)) {
            return null;
        }

        return $resource::getUrl($page, ['record' => $record]);
    }
}
