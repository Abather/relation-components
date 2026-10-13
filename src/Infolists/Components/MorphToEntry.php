<?php

namespace Abather\RelationComponents\Infolists\Components;

use Abather\RelationComponents\Traits\HasMorphTypes;
use Filament\Infolists\Components\TextEntry;

class MorphToEntry extends TextEntry
{
    use HasMorphTypes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->color('info');
        $this->openUrlInNewTab();
    }
}
