<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StudentResource;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
    protected static ?string $title = 'Kria Estudante';
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Buat User otomatis
        $user = User::create([
            'login_id' => $data['nre'],
            'name'     => $data['name'],
            'email'    => $data['email'] ?? $data['nre'].'@school.local',
            'password' => bcrypt('password'),
        ]);

        // Assign role siswa
        $user->assignRole('estudante');

        // Simpan user_id ke student
        $data['user_id'] = $user->id;

        // Ambil major_id dari class_room_id
        $data['major_id'] = \App\Models\ClassRoom::find($data['class_room_id'])->major_id;

        return $data;
    }
}
