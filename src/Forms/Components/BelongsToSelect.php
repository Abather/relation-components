<?php

namespace Abather\RelationComponents\Forms\Components;

use Abather\RelationComponents\Traits\HasRelationConfiguration;
use Abather\RelationComponents\Traits\ResolvesResourceRelations;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use LogicException;

class BelongsToSelect extends Select
{
    use HasRelationConfiguration;
    use ResolvesResourceRelations;

    /**
     * @param  class-string<resource>  $name
     */
    public static function make(?string $name = null): static
    {
        $componentClass = static::class;

        if (blank($name)) {
            throw new LogicException("Component of class [$componentClass] must have a unique name, passed to the [make()] method.");
        }

        if (! class_exists($name)) {
            throw new LogicException("Component of class [$componentClass] requires a valid resource class, [$name] does not exist.");
        }

        if (! is_subclass_of($name, Resource::class)) {
            throw new LogicException("Component of class [$componentClass] requires a Filament resource class, [$name] does not extend [".Resource::class.'].');
        }

        /** @var class-string<resource> $resource */
        $resource = $name;
        $titleAttribute = static::resolveTitleAttribute($resource);
        $relation = str($resource::getModel())->classBasename()->camel()->value();

        /** @var static $static */
        $static = app($componentClass, ['name' => $relation]);

        $static->resourceClass($resource)
            ->relationName($relation)
            ->titleAttribute($titleAttribute)
            ->label($resource::getModelLabel())
            ->relationship($relation, $titleAttribute)
            ->suffixIcon(fn () => $static->getWithIcon() ? $resource::getNavigationIcon() : null);

        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->searchable();
        $this->preload();
    }
}
