<?php

namespace Abather\RelationComponents\Traits;

trait HasRelationConfiguration
{
    protected string|\Closure|null $resourceClass = null;

    protected string|\Closure|null $relationName = null;

    protected string|\Closure|null $titleAttribute = null;

    protected string|\Closure|null $page = 'view';

    protected bool $withIcon = true;

    protected bool $withUrl = true;

    // ─── Fluent setters ───────────────────────────────────────────────────────

    public function resourceClass(string|\Closure $resource): static
    {
        $this->resourceClass = $resource;

        return $this;
    }

    public function relationName(string|\Closure $relation): static
    {
        $this->relationName = $relation;

        return $this;
    }

    public function titleAttribute(string|\Closure $attribute): static
    {
        $this->titleAttribute = $attribute;

        return $this;
    }

    public function page(string|\Closure $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function withIcon(bool $withIcon = true): static
    {
        $this->withIcon = $withIcon;

        return $this;
    }

    public function withUrl(bool $withUrl = true): static
    {
        $this->withUrl = $withUrl;

        return $this;
    }

    // ─── Getters ──────────────────────────────────────────────────────────────

    public function getResourceClass(): ?string
    {
        return $this->evaluate($this->resourceClass);
    }

    public function getRelationName(): ?string
    {
        return $this->evaluate($this->relationName);
    }

    public function getTitleAttribute(): ?string
    {
        return $this->evaluate($this->titleAttribute);
    }

    public function getPage(): string
    {
        return $this->evaluate($this->page) ?? 'view';
    }
}
