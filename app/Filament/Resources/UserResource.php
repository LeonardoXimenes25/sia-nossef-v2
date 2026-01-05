<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\UserResource\Pages;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Spatie\Permission\Models\Role;
use Filament\Tables\Filters\SelectFilter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Utilizador';
    protected static ?string $navigationGroup = 'Managementu Utilizador';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Naran')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->label('Password'),

                Forms\Components\Select::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Nu'),
                Tables\Columns\TextColumn::make('login_id')->label('Login ID'),
                Tables\Columns\TextColumn::make('name')->label('Naran'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('roles.name')->label('Roles'),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(Role::pluck('name', 'name')->toArray())
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('roles', fn($q) => $q->where('name', $data['value']));
                        }
                    }),
            ])
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Action::make('generate_users')
                    ->label('Generate Users')
                    ->form([
                        Forms\Components\TextInput::make('start_nre')
                            ->label('Mulai NRE / Login ID')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('count')
                            ->label('Jumlah User')
                            ->numeric()
                            ->required(),

                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options(Role::pluck('name', 'name'))
                            ->required(),
                    ])
                    ->action(function ($data) {
                        $start = (int) $data['start_nre'];
                        $count = (int) $data['count'];
                        $role  = $data['role'];

                        for ($i = 0; $i < $count; $i++) {
                            $nre = $start + $i;

                            if (!User::where('login_id', $nre)->exists()) {
                                $user = User::create([
                                    'login_id' => $nre,
                                    'name' => "Student {$nre}",
                                    'email' => "student{$nre}@nossef.edu",
                                    'password' => bcrypt('password'),
                                ]);

                                $user->assignRole($role);
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title("Generate user berhasil")
                            ->body("Total {$count} user dengan role {$role}")
                            ->success()
                            ->send();
                    })
                    ->color('success')
                    ->icon('heroicon-o-user-plus'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
