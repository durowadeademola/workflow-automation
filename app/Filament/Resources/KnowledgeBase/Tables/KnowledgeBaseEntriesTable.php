<?php

namespace App\Filament\Resources\KnowledgeBase\Tables;

use App\Models\KnowledgeBaseEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KnowledgeBaseEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->visible(fn () => (bool) auth()->user()?->is_admin)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => KnowledgeBaseEntry::TYPES[$state] ?? $state),
                TextColumn::make('title')
                    ->label('Title / Question')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title)
                    ->searchable(),
                TextColumn::make('content')
                    ->label('Content / Answer')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->content)
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('views')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    // Articles are only ever used for RAG lookups, never
                    // opened directly by a visitor the way an FAQ is, so a
                    // count for them would just always read 0 — showing a
                    // dash instead makes clear it isn't a meaningful metric
                    // here rather than implying nobody's looked at it.
                    ->formatStateUsing(fn ($state, $record) => $record->type === 'faq' ? $state : '—'),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
