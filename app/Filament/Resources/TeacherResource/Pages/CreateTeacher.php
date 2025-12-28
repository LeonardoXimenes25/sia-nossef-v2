<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use Filament\Actions;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TeacherResource;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;
    protected static ?string $title = 'Kria Mestre';
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Buat User otomatis
        $user = User::create([
            'login_id' => $data['nrp'],
            'name'     => $data['name'],
            'email'    => $data['email'] ?? $data['nrp'].'@school.local',
            'password' => bcrypt('password'),
        ]);

        // Assign role siswa
        $user->assignRole('mestre');

        // Simpan user_id ke student
        $data['user_id'] = $user->id;

        return $data;
    }
}
