<?php

namespace Abather\RelationComponents\Tables\Columns;

use Abather\RelationComponents\Traits\HasRelationConfiguration;
use Abather\RelationComponents\Traits\HasRelationMethods;
use Abather\RelationComponents\Traits\ResolvesResourceRelations;
use Filament\Tables\Columns\TextColumn;

class RelationColumn extends TextColumn
{
    use HasRelationConfiguration;
    use HasRelationMethods;
    use ResolvesResourceRelations;
}
