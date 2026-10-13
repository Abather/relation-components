<?php

namespace Abather\RelationComponents\Traits;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use LogicException;

trait ResolvesResourceRelations
{
    /**
     * @return class-string<resource>
     */
    protected static function ensureIsResourceClass(?string $resource): string
    {
        $componentClass = static::class;

        if (blank($resource)) {
            throw new LogicException("Component of class [$componentClass] must have a unique name, passed to the [make()] method.");
        }

        if (! class_exists($resource)) {
            throw new LogicException("Component of class [$componentClass] requires a valid resource class, [$resource] does not exist.");
        }

        if (! is_subclass_of($resource, Resource::class)) {
            throw new LogicException("Component of class [$componentClass] requires a Filament resource class, [$resource] does not extend [".Resource::class.'].');
        }

        return $resource;
    }

    /**
     * @param  class-string<resource>  $resource
     */
    protected static function resolveRelationName(string $resource, ?string $relationship = null): string
    {
        return $relationship ?? str($resource::getModel())->classBasename()->camel()->value();
    }

    /**
     * Uses the resource's model label unless a custom relationship name was given,
     * e.g. `author` on a `UserResource` reads "Author", not "User".
     *
     * @param  class-string<resource>  $resource
     */
    protected static function resolveRelationLabel(string $resource, string $relation): string
    {
        if ($relation === static::resolveRelationName($resource)) {
            return $resource::getModelLabel();
        }

        return str($relation)->headline()->value();
    }

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
}
