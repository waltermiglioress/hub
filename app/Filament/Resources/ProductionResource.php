<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductionResource\Pages;

use App\Models\Production;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductionResource extends Resource
{
    protected static ?string $model = Production::class;

    protected static ?string $modelLabel = 'Produzione';

    protected static ?string $pluralModelLabel = 'Produzioni';
//    protected static ?string $navigationGroup = 'Gestione rifiuti';

    protected static ?string $navigationLabel = 'Monitoraggio commessa';

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('client_id')->label('Cliente')
                            ->relationship('client','name')
                            ->required(),
                        Select::make('project_id')->label('Commessa')
                            ->relationship('projects','code',fn (Builder $query)=>$query->whereBelongsTo('user','user','true'))
                            ->required(),
                        TextInput::make('desc')->label('Descrizione'),
                        TextInput::make('type')->label('Tipologia')->required(),
                        TextInput::make('doc_id')->label('ID Documento')->required(),
                        TextInput::make('percentage')->label('Percentuale')->suffix('%')->required(),
                    ])->inlineLabel(),
                Section::make()
                    ->schema([
                        TextInput::make('value')
//                        ->mask(fn ($mask) => $mask->money( '€ ', ',',2))
                            ->numeric()
                            ->prefix('€')
                            ->label('Valore produzione')
                            ->required(),
                        DatePicker::make('date_start')
                            ->label('Data inizio')
                            ->displayFormat('d/m/Y')
                            ->required(),
                        DatePicker::make('date_end')
                            ->label('Data fine')
                            ->displayFormat('d/m/Y')
                            ->required(),
                        Select::make('status')->label('Stato')
                            ->options([
                                'FATTURATO',
                                'CONTABILIZZATO E NON FATTURATO',
                                'STIMATO',
                            ])
                            ->required(),

                        TextInput::make('imponibile')->placeholder(function (callable $get){
                            return $get('value')*$get('percentage')/100;
                        })->disabled()
//                            ->mask(fn (TextInput\Mask $mask) => $mask->money( '€ ', ',',2))

                            ->label('Imponibile'),
                    ])->inlineLabel()
                //
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
                ])

            ])
            ->emptyStateHeading('Nessuna produzione')
            ->emptyStateIcon('heroicon-o-currency-euro')
            ->emptyStateDescription('Aggiungi una nuova produzione da fatturare o non ancora bla bla bla')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Aggiungi produzione')
                    ->url('productions/create')
                    ->icon('heroicon-m-plus')
                    ->button(),
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
            'index' => Pages\ListProductions::route('/'),
            'create' => Pages\CreateProduction::route('/create'),
            'edit' => Pages\EditProduction::route('/{record}/edit'),
        ];
    }
}
