<?php

namespace App\Filament\Resources;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
                TextInput::make('code_ind')
                    ->label('Codice industriale')
                    ->required(),
                TextInput::make('desc')
                    ->label('Descrizione')
                    ->required(),
                TextInput::make('contract')
                    ->label('N. contratto')
                    ->required(),
                Radio::make('status')
                    ->options([
                        '0' => 'Non attiva',
                        '1' => 'Attiva',
                    ])
                    ->inline()
                    ->required(),
                Select::make('currency')
                    ->label('Valuta')
                    ->searchable()
                    ->options(function (): array {
                        $list = Currency::getCurrencies();
//                        dd($list);
                        $currencies = collect($list)->mapWithKeys(function ($item, $key) {
                            return [$key => $item['name']];
                        })->toArray();

                        return !empty($currencies) ? $currencies : ['no currencies'];
                    })
                    ->noSearchResultsMessage(__('Valuta non trovata')),
                TextInput::make('value')
                    ->label('Valore contrattuale')
                    ->numeric(),

                Select::make('group')
                    ->label('Gruppo')
                    ->options([
                        'Sicilsaldo' => 'SICILSALDO',
                        'Nuova Ghizzoni' => 'NUOVA GHIZZONI'
                    ])
                    ->required(),
                Select::make('clifor_id')
                    ->label('Cliente')
                    ->relationship('clifor', 'name')
                    ->createOptionForm(fn(Form $form) => CliForResource::form($form))
                    ->createOptionAction(
                        fn(Action $action) => $action->modalWidth('5xl'),
                    )
                    ->required(),
                Select::make('tender_id')
                    ->label('Gara')
                    ->nullable()
                    ->relationship('tender', 'num'),
                Select::make('responsible_id')
                    ->label('Responsabile')
                    ->relationship('manager', 'firstname')
                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->firstname} {$record->lastname}")
                    ->searchable(['firstname', 'lastname']),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Codice'),
                TextColumn::make('code_ind')
                    ->label('Codice Industriale'),
                TextColumn::make('group')
                    ->label('Gruppo'),
                TextColumn::make('desc')
                    ->label('Descrizione')
                    ->wrap(),
                TextColumn::make('contract')
                    ->label('N. Contratto'),
                TextColumn::make('value')
                    ->label('Valore')
                    ->money(function (Project $record): Currency {
                        // Verifica se il record esiste e ha un attributo 'currency' non vuoto
                        if (!empty($record) && !empty($record->currency)) {
                            return new Currency($record->currency);
                        }
                        // Caso di default: restituisce un oggetto Currency per 'EUR'
                        return new Currency('EUR');
                    }),
                IconColumn::make('status')
                    ->label('Stato')
                    ->boolean()
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
