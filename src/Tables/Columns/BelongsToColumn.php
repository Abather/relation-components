<?php

namespace Abather\RelationComponents\Tables\Columns;

use Abather\RelationComponents\Traits\ConfiguresBelongsTo;
use Filament\Tables\Columns\TextColumn;

class BelongsToColumn extends TextColumn
{
    use ConfiguresBelongsTo;

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
