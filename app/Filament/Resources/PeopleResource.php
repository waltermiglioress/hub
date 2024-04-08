<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeopleResource\Pages;
use App\Filament\Resources\PeopleResource\RelationManagers;
use App\Models\City;
use App\Models\People;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class PeopleResource extends Resource
{
    protected static ?string $model = People::class;

    protected static ?string $modelLabel = 'Rubrica';

    protected static ?string $pluralModelLabel = 'Rubrica';
    protected static ?string $navigationGroup = 'Elenchi';

    protected static ?string $navigationLabel = 'Rubrica';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dati generali')
                    ->schema([
                        TextInput::make('firstname')
                            ->label('Nome')
                            ->required(),
                        TextInput::make('lastname')
                            ->label('Cognome')
                            ->required(),

                        TextInput::make('CF')
                            ->label('Codice Fiscale')
                            ->unique(),


                        Fieldset::make('Indirizzi')
                            ->schema([
                                Select::make('country_id')
                                    ->label('Paese')
                                    ->relationship('country','name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set){
                                        $set('state_id',null);
                                        $set('city_id',null);
                                    }),
                                Select::make('state_id')
                                    ->label('Regione/Provincia')
                                    ->options(fn(Get $get): Collection =>State::query()
                                        ->where('country_id', $get('country_id'))
                                        ->pluck('name','id'))
//                    ->relationship('state','name')
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn(Set $set) => $set('city_id',null))
                                    ->preload(),

                                Select::make('city_id')
                                    ->label('Città')
                                    ->options(fn(Get $get): Collection =>City::query()
                                        ->where('state_id', $get('state_id'))
                                        ->pluck('name','id'))
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                TextInput::make('cap')
                                    ->label('CAP')
                                    ->numeric(),
                                TextInput::make('address')
                                    ->label('Indirizzo')
                                    ->columnSpanFull(),

                            ]),
                    ])->columnSpan(2)->columns(3),

                Section::make('Altri dati')->schema([
                    FileUpload::make('avatar')
                        ->label('Logo')
                        ->disk('public')
                        ->directory('storage')
                        ->image()
                        ->avatar()
                        ->openable()
                        ->imageEditor()
                        ->circleCropper(),
                    TextInput::make('tel')
                        ->label('Telefono')
                        ->tel(),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->unique()
                        ->required(),
                    Select::make('clifor_id')
                        ->label('Presso')
                        ->relationship('clifor','name')
                ])->columnSpan(1),

            ])->columns(3);
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePeople::route('/create'),
            'edit' => Pages\EditPeople::route('/{record}/edit'),
        ];
    }
}
