<?php

namespace Abather\RelationComponents\Tables\Columns;

use Abather\RelationComponents\Traits\HasRelationConfiguration;
use Abather\RelationComponents\Traits\ResolvesResourceRelations;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use LogicException;

class BelongsToColumn extends TextColumn
{
    use HasRelationConfiguration;
    use ResolvesResourceRelations;

    /**
     * @param class-string<Resource> $name
     */
    public static function make(?string $name = null): static
    {
        $columnClass = static::class;

        if (blank($name)) {
            throw new LogicException("Column of class [$columnClass] must have a unique name, passed to the [make()] method.");
        }

        if (! class_exists($name)) {
            throw new LogicException("Column of class [$columnClass] requires a valid resource class, [$name] does not exist.");
        }

        if (! is_subclass_of($name, Resource::class)) {
            throw new LogicException("Column of class [$columnClass] requires a Filament resource class, [$name] does not extend [" . Resource::class . "].");
        }

        /** @var class-string<Resource> $resource */
        $resource       = $name;
        $titleAttribute = static::resolveTitleAttribute($resource);
        $relation       = str($resource::getModel())->classBasename()->camel()->value();

        /** @var static $static */
        $static = app($columnClass, ['name' => "{$relation}.{$titleAttribute}"]);

        $static->resourceClass($resource)
            ->relationName($relation)
            ->titleAttribute($titleAttribute)
            ->label($resource::getModelLabel())
            ->icon(fn () => $static->getWithIcon() ? $resource::getNavigationIcon() : null)
            ->hiddenOn($resource::getRelations())
            ->url(fn ($record) => static::getRecordUrl(
                $resource,
                data_get($record, $relation),
                $static->getPage()
            ));

        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->color('info');
        $this->openUrlInNewTab();
    }
}
