<?php

namespace App\Filament\Resources\NewsCategoryResource\Pages;

use App\Filament\Resources\NewsCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNewsCategory extends EditRecord
{
    protected static string $resource = NewsCategoryResource::class;
    protected static ?string $title = 'Edita Kategoria Notisia';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
