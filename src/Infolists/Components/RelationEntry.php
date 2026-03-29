<?php

namespace Abather\RelationComponents\Infolists\Components;

use Abather\RelationComponents\Traits\HasRelationConfiguration;
use Abather\RelationComponents\Traits\HasRelationMethods;
use Abather\RelationComponents\Traits\ResolvesResourceRelations;
use Filament\Infolists\Components\TextEntry;

class RelationEntry extends TextEntry
{
    use HasRelationConfiguration;
    use HasRelationMethods;
    use ResolvesResourceRelations;
}
