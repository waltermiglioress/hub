<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $modelLabel = 'Commessa';
    protected static ?string $pluralModelLabel = 'Commesse';

    protected static ?string $navigationLabel = 'Elenco commesse';

    protected static ?string $navigationGroup = 'Elenchi';

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')
                    ->label('Codice commessa')
                    ->required(),
                TextInput::make('desc')
                    ->label('Descrizione')
                    ->required(),
                TextInput::make('contract')
                    ->label('N. contratto')
                    ->required(),
                Radio::make('status')
                    ->options([
                        '0' => 'Attiva',
                        '1' => 'Non attiva',
                    ])
                    ->inline()
                    ->required(),
                Select::make('group')
                    ->label('Gruppo')
                    ->options(['Sicilsaldo','Nuova Ghizzoni']),
                Select::make('clifor_id')
                    ->label('Cliente')
                    ->relationship('clifor','name'),
                Select::make('tender_id')
                    ->label('Gara')
                    ->relationship('tender','num'),
                Select::make('responsible_id')
                    ->label('Responsabile')
                    ->relationship('manager','firstname'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
