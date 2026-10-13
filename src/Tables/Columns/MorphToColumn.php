<?php

namespace Abather\RelationComponents\Tables\Columns;

use Abather\RelationComponents\Traits\HasMorphTypes;
use Filament\Tables\Columns\TextColumn;

class MorphToColumn extends TextColumn
{
    use HasMorphTypes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->color('info');
        $this->openUrlInNewTab();
    }

    protected function evaluatesVisibilityPerRecord(): bool
    {
        return false;
    }
}
