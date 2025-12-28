<?php

namespace App\Filament\Widgets;

use App\Models\Timetable;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTimetables extends BaseWidget
{
    /**
     * Judul widget
     */
    protected static ?string $heading = 'Horarius Foun Ne\'ebe Ultimu';

    /**
     * Lebar widget (full dashboard)
     */
    protected int | string | array $columnSpan = 'full';

    /**
     * Tabel dashboard
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timetable::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Disiplina'),

                Tables\Columns\TextColumn::make('classRoom.name')
                    ->label('Klasse'),

                Tables\Columns\TextColumn::make('academicYear.year')
                    ->label('Ano Akademiku'),

                Tables\Columns\TextColumn::make('day')
                    ->label('Dia'),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Hora Inisiu')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Hora Remata')
                    ->time('H:i'),
            ])
            ->paginated(false) // ❌ tidak ada scroll / pagination
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }
}
