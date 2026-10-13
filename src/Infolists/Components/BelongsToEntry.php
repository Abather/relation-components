<?php

namespace Abather\RelationComponents\Infolists\Components;

use Abather\RelationComponents\Traits\ConfiguresBelongsTo;
use Filament\Infolists\Components\TextEntry;

class BelongsToEntry extends TextEntry
{
    use ConfiguresBelongsTo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->color('info');
        $this->openUrlInNewTab();
    }
}
