<?php

namespace Abather\RelationComponents\Traits;

use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

/**
 * Visibility and URL resolution shared by the column and entry components.
 */
trait InteractsWithRelatedRecord
{
    /**
     * Resources whose relation manager pages should hide this component.
     *
     * @return array<class-string<resource>>
     */
    abstract public function getParentResources(): array;

    /**
     * The resource used for the authorization check, given the owner record (if any).
     *
     * @return class-string<resource> | null
     */
    abstract protected function resolveVisibilityResource(?Model $record): ?string;

    /**
     * Whether visibility may depend on the current record. Table columns are
     * hidden for the whole table at once, so they override this to return false.
     */
    protected function evaluatesVisibilityPerRecord(): bool
    {
        return true;
    }

    /**
     * Combines the parent-page and authorization checks into one condition, since
     * Filament keeps only the last closure passed to `hidden()` / `hiddenOn()`.
     */
    public function shouldBeHidden(mixed $livewire, ?Model $record = null): bool
    {
        if ($this->isHiddenOnParentPages() && $this->isOnParentRelationPage($livewire)) {
            return true;
        }

        $record = $this->evaluatesVisibilityPerRecord() ? $record : null;
        $related = $record ? data_get($record, $this->getRelationName()) : null;

        return $this->resolveHideWhenNotAuthorizedToView(
            $this->resolveVisibilityResource($record),
            $related instanceof Model ? $related : null,
        );
    }

    public function isOnParentRelationPage(mixed $livewire): bool
    {
        if (! is_object($livewire)) {
            return false;
        }

        foreach (static::getRelationManagerClasses($this->getParentResources()) as $relationManager) {
            if ($livewire instanceof $relationManager) {
                return true;
            }
        }

        return false;
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
     * Falls back to the `edit` page when the `view` page does not exist, and checks
     * `canEdit()` for the edit page and `canView()` for any other page.
     *
     * @param  class-string<resource>|null  $resource
     */
    public function resolveRecordUrl(?string $resource, mixed $record): ?string
    {
        if (! $this->getWithUrl() || blank($resource) || ! $record instanceof Model) {
            return null;
        }

        $page = $this->getPage();

        if ($page === 'view' && ! $resource::hasPage('view')) {
            $page = 'edit';
        }

        if (! $resource::hasPage($page)) {
            return null;
        }

        $isAuthorized = $page === 'edit'
            ? $resource::canEdit($record)
            : $resource::canView($record);

        if (! $isAuthorized) {
            return null;
        }

        return $resource::getUrl($page, ['record' => $record]);
    }

    /**
     * Relation manager classes registered on the given resources, with relation
     * groups and `RelationManager::make([...])` configurations unwrapped.
     *
     * @param  array<class-string<resource>>  $resources
     * @return array<class-string>
     */
    protected static function getRelationManagerClasses(array $resources): array
    {
        $classes = [];

        foreach ($resources as $resource) {
            foreach ($resource::getRelations() as $relation) {
                $managers = $relation instanceof RelationGroup ? $relation->getManagers() : [$relation];

                if (! is_array($managers)) {
                    continue;
                }

                foreach ($managers as $manager) {
                    $classes[] = $manager instanceof RelationManagerConfiguration ? $manager->relationManager : $manager;
                }
            }
        }

        return array_values(array_unique(array_filter($classes, 'is_string')));
    }
}
