<?php

namespace Abather\RelationComponents\Infolists\Components;

use Abather\RelationComponents\Traits\HasRelationConfiguration;
use Abather\RelationComponents\Traits\ResolvesResourceRelations;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use LogicException;

class BelongsToEntry extends TextEntry
{
    use HasRelationConfiguration;
    use ResolvesResourceRelations;

    /**
     * @param class-string<Resource> $namee
     */
    public static function make(?string $name = null): static
    {
        $entryClass = static::class;

        if (blank($name)) {
            throw new LogicException("Entry of class [$entryClass] must have a unique name, passed to the [make()] method.");
        }

        if (! class_exists($name)) {
            throw new LogicException("Entry of class [$entryClass] requires a valid resource class, [$name] does not exist.");
        }

        if (! is_subclass_of($name, Resource::class)) {
            throw new LogicException("Entry of class [$entryClass] requires a Filament resource class, [$name] does not extend [" . Resource::class . "].");
        }

        /** @var class-string<Resource> $resource */
        $resource       = $name;
        $titleAttribute = static::resolveTitleAttribute($resource);
        $relation       = str($resource::getModel())->classBasename()->camel()->value();

        /** @var static $static */
        $static = app($entryClass, ['name' => "{$relation}.{$titleAttribute}"]);

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
