<?php

namespace App\Filament\Tables\Columns;

use Closure;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A table's "who" column: an initials avatar, the name, and a smaller grey line under it (usually
 * the email, which is why the tables using this fold their separate email column into it). Plain
 * TextColumn underneath, so sorting/searching/exporting behave exactly as they did before — only
 * the rendered cell changes (resources/views/filament/tables/columns/person.blade.php).
 */
class PersonColumn
{
    /**
     * @param  Closure(Model): ?string  $subline
     */
    public static function make(string $name, Closure $subline): TextColumn
    {
        return TextColumn::make($name)
            ->html()
            ->formatStateUsing(fn (?string $state, Model $record): string => view('filament.tables.columns.person', [
                'name' => $state,
                'initials' => static::initials($state),
                'subline' => $subline($record),
            ])->render());
    }

    public static function initials(?string $name): string
    {
        $words = preg_split('/\s+/', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);

        return Str::upper(collect($words)->take(2)->map(fn (string $word): string => Str::substr($word, 0, 1))->implode('')) ?: '?';
    }
}
