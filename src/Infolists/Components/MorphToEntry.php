<?php

namespace Abather\RelationComponents\Infolists\Components;

use Abather\RelationComponents\Traits\HasRelationConfiguration;
use Abather\RelationComponents\Traits\ResolvesResourceRelations;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class MorphToEntry extends TextEntry
{
    use HasRelationConfiguration;
    use ResolvesResourceRelations;

    /** @var array<class-string<Model>, class-string<resource>> */
    protected array $types = [];

    private array $cache = [];

    public static function make(?string $name = null): static
    {
        $entryClass = static::class;

        if (blank($name)) {
            throw new LogicException("Entry of class [$entryClass] must have a unique name, passed to the [make()] method.");
        }

        /** @var static $static */
        $static = app($entryClass, ['name' => "{$name}_type"]);

        $static->relationName($name)
            ->label(str($name)->headline()->value())
            ->icon(function ($record) use ($static) {
                if (! $static->getWithIcon()) {
                    return null;
                }

                $resource = $static->resolveFromCache($record);

                /** @var class-string<resource> $resource */
                return $resource ? $resource::getNavigationIcon() : null;
            })
            ->formatStateUsing(function ($record) use ($static) {
                $related = data_get($record, $static->getRelationName());

                if (! $related) {
                    return '-';
                }

                $resource = $static->resolveFromCache($record);

                $titleAttribute = $resource
                    ? static::resolveTitleAttribute($resource)
                    : $related->getKeyName();

                return data_get($related, $titleAttribute) ?? '-';
            })
            ->hidden(fn ($record) => $static->resolveHideWhenNotAuthorizedToView($static->resolveFromCache($record), $record))
            ->url(function ($record) use ($static) {
                $resource = $static->resolveFromCache($record);
                $related = data_get($record, $static->getRelationName());

                if (! $resource || ! $related) {
                    return null;
                }

                return static::getRecordUrl($resource, $related, $static->getPage());
            });

        $static->configure();

        return $static;
    }

    /**
     * @param  array<class-string<Model>, class-string<resource>>  $types
     */
    public function types(array $types): static
    {
        foreach ($types as $resourceClass) {
            if (! class_exists($resourceClass)) {
                throw new LogicException("Resource class [$resourceClass] does not exist.");
            }

            if (! is_subclass_of($resourceClass, Resource::class)) {
                throw new LogicException("[$resourceClass] does not extend [".Resource::class.'].');
            }
        }

        $this->types = $types;

        $relationPages = [];

        foreach ($types as $resourceClass) {
            $relationPages = array_merge($relationPages, $resourceClass::getRelations());
        }

        return $this->hiddenOn($relationPages);
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    private function resolveFromCache(Model $record): ?string
    {
        $recordId = $record->getKey();

        if (! isset($this->cache[$recordId])) {
            $this->cache[$recordId] = static::resolveMorphResource($record, $this->getRelationName(), $this->types);
        }

        return $this->cache[$recordId];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->color('info');
        $this->openUrlInNewTab();
    }
}
