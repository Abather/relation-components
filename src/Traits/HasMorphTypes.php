<?php

namespace Abather\RelationComponents\Traits;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use LogicException;

/**
 * Shared `make()` and type map for the `morphTo` column and entry.
 */
trait HasMorphTypes
{
    use HasRelationConfiguration;
    use InteractsWithRelatedRecord;
    use ResolvesResourceRelations;

    /** @var array<class-string<Model>, class-string<resource>> */
    protected array $types = [];

    /** @var array<string, class-string<resource>|null> */
    private array $resourceCache = [];

    public static function make(?string $name = null): static
    {
        $componentClass = static::class;

        if (blank($name)) {
            throw new LogicException("Component of class [$componentClass] must have a unique name, passed to the [make()] method.");
        }

        /** @var static $static */
        $static = app($componentClass, ['name' => "{$name}_type"]);

        $static->relationName($name)
            ->label(str($name)->headline()->value())
            ->icon(function (?Model $record) use ($static) {
                if (! $record || ! $static->getWithIcon()) {
                    return null;
                }

                $resource = $static->resolveFromCache($record);

                return $resource ? $resource::getNavigationIcon() : null;
            })
            ->formatStateUsing(function (Model $record) use ($static) {
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
            ->hidden(fn (mixed $livewire, ?Model $record) => $static->shouldBeHidden($livewire, $record))
            ->url(fn (?Model $record) => $record ? $static->resolveRecordUrl(
                $static->resolveFromCache($record),
                data_get($record, $static->getRelationName()),
            ) : null);

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
        $this->resourceCache = [];

        return $this;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getParentResources(): array
    {
        return array_values(array_unique($this->types));
    }

    protected function resolveVisibilityResource(?Model $record): ?string
    {
        return $record ? $this->resolveFromCache($record) : null;
    }

    /**
     * @return class-string<resource>|null
     */
    private function resolveFromCache(Model $record): ?string
    {
        $key = $record->getKey();

        if ($key === null) {
            return static::resolveMorphResource($record, $this->getRelationName(), $this->types);
        }

        $cacheKey = $record::class.':'.$key;

        if (! array_key_exists($cacheKey, $this->resourceCache)) {
            $this->resourceCache[$cacheKey] = static::resolveMorphResource($record, $this->getRelationName(), $this->types);
        }

        return $this->resourceCache[$cacheKey];
    }
}
