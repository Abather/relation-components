<?php

namespace Abather\RelationComponents\Forms\Components;

use Abather\RelationComponents\Traits\HasRelationConfiguration;
use Abather\RelationComponents\Traits\ResolvesResourceRelations;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;

class BelongsToSelect extends Select
{
    use HasRelationConfiguration;
    use ResolvesResourceRelations;

    /**
     * The field is named after the foreign key (e.g. `category_id`), not the relation,
     * so the value is part of the create payload and `NOT NULL` keys are filled on insert.
     *
     * @param  class-string<resource>  $name
     * @param  string|null  $relationship  defaults to the camel-cased model name, e.g. `CategoryResource` → `category`
     * @param  string|null  $foreignKey  defaults to Laravel's convention, e.g. `category_id`
     */
    public static function make(?string $name = null, ?string $relationship = null, ?string $foreignKey = null): static
    {
        $resource = static::ensureIsResourceClass($name);
        $relation = static::resolveRelationName($resource, $relationship);
        $titleAttribute = static::resolveTitleAttribute($resource);
        $foreignKey ??= str($relation)->snake()->append('_', app($resource::getModel())->getKeyName())->value();

        /** @var static $static */
        $static = app(static::class, ['name' => $foreignKey]);

        $static->resourceClass($resource)
            ->relationName($relation)
            ->titleAttribute($titleAttribute)
            ->label(static::resolveRelationLabel($resource, $relation))
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
