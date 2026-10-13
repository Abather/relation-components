<?php

namespace Abather\RelationComponents\Traits;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

/**
 * Shared `make()` for the `belongsTo` column and entry.
 */
trait ConfiguresBelongsTo
{
    use HasRelationConfiguration;
    use InteractsWithRelatedRecord;
    use ResolvesResourceRelations;

    /**
     * @param  class-string<resource>  $name
     * @param  string|null  $relationship  defaults to the camel-cased model name, e.g. `UserResource` → `user`
     */
    public static function make(?string $name = null, ?string $relationship = null): static
    {
        $resource = static::ensureIsResourceClass($name);
        $relation = static::resolveRelationName($resource, $relationship);
        $titleAttribute = static::resolveTitleAttribute($resource);

        /** @var static $static */
        $static = app(static::class, ['name' => "{$relation}.{$titleAttribute}"]);

        $static->resourceClass($resource)
            ->relationName($relation)
            ->titleAttribute($titleAttribute)
            ->label(static::resolveRelationLabel($resource, $relation))
            ->icon(fn () => $static->getWithIcon() ? $resource::getNavigationIcon() : null)
            ->hidden(fn (mixed $livewire, ?Model $record) => $static->shouldBeHidden($livewire, $record))
            ->url(fn (?Model $record) => $static->resolveRecordUrl(
                $static->getResourceClass(),
                data_get($record, $static->getRelationName()),
            ));

        $static->configure();

        return $static;
    }

    public function getParentResources(): array
    {
        return array_filter([$this->getResourceClass()]);
    }

    protected function resolveVisibilityResource(?Model $record): ?string
    {
        return $this->getResourceClass();
    }
}
