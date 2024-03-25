<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductionResource\Pages;

use App\Models\Production;
use App\Models\Project;
use App\Models\User;
use App\Tables\Columns\ProgressColumn;
use Filament\Support\RawJs;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

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
                Section::make('Parte generale')
                    ->description('Descrizione della parte generale')
                    ->aside()
                    ->schema([
                        Select::make('client_id')->label('Cliente')
                            ->relationship('client','name')
                            ->required(),
                        Select::make('project_id')
                            ->label('Commessa')
                            ->options(Project::whereHas('users',function ($query){
                                $query->where('user_id',Auth::id());
                            })->pluck('code','id'))
                            ->searchable()
                            ->preload()
//                            ->relationship(
//                                'projects',
//                                'code',
//                                fn (Builder $query)=>$query->whereBelongsTo('user','user','true'))
                            ->required(),
                        TextInput::make('desc')->label('Descrizione'),
                        TextInput::make('type')
                            ->datalist([
                                'SAL',
                                'ODL',
                                'MAP',
                            ])
                            ->label('Tipologia')
                            ->required(),
//                        TextInput::make('doc_id')->label('ID Documento')->required(),
                        TextInput::make('percentage')->label('Percentuale')->suffix('%')->numeric()->maxValue(100)->required(),
                    ])->columnSpan(1)->inlineLabel()->live(),
                Section::make('Dettaglio')
                    ->description('Parte legata alla descrizione ed eventuali istruzioni per bambini cani gatti etc etc')
                    ->aside()
                    ->schema([
                        TextInput::make('value')
                            //->mask(RawJs::make('$money($input)'))
                            ->stripCharacters('.')
                            ->live()
                            ->numeric()
                            ->prefix('€')
                            ->label('Valore produzione')
                            ->required(),
                        DatePicker::make('date_start')->native(false)
                            ->label('Data inizio')
                            ->displayFormat('d/m/Y')
                            ->suffixIcon('heroicon-o-calendar-days')
                            ->required(),
                        DatePicker::make('date_end')->native(false)
                            ->label('Data fine')
                            ->displayFormat('d/m/Y')
                            ->after('date_start')
                            ->suffixIcon('heroicon-o-calendar-days')
                            ->required(),
                        Select::make('status')->label('Stato')
                            ->options([
                                'fatturato'=>'FATTURATO',
                                'contabilizzato e non ft'=>'CONTABILIZZATO E NON FATTURATO',
                                'stimato'=>'STIMATO',
                            ])
                            ->required(),
                        TextInput::make('imponibile')
//                        ->mask(RawJs::make('$money($input)'))
                        //->stripCharacters(',')
                        ->prefix('€')
                        ->numeric()
                            ->placeholder(function (callable $get){
                            return (int)$get('value')*(int)$get('percentage')/100;
                            })
                        ->disabled()
                        ,
                    ])->columnSpan(1)->inlineLabel()
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.code')->label('Commessa')->searchable()->sortable(),
                TextColumn::make('client.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('desc')->label('Descrizione')->words(10)->wrap(),
                TextColumn::make('date_start')->date('d/m/Y'),
                TextColumn::make('date_end')->date('d/m/Y'),
                TextColumn::make('type')->label('Tipo'),
                ProgressColumn::make('percentage')
                    ->label('Percentuale'),
//                Tables\Columns\TextColumn::make('percentage')->label('Percentuale')->sortable(),
                TextColumn::make('value')->label('Valore')
                    ->money('eur',true)
                    ->sortable(),
                TextColumn::make('status')->label('Stato')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fatturato' => 'success',
                        'contabilizzato e non ft' => 'warning',
                        'stimato' => 'estimated',
                    }),

                TextColumn::make('imponibile')
                    ->label('Imponibile')

                    ->money('eur',true)->sortable()
                    ->getStateUsing(function (Model $record) {
                        if (isset($record->value) && isset($record->percentage)) {
                            if (!empty($record->value && !empty($record->percentage))) {
                                return $record->value * $record->percentage/100;
                            }
                        }
                    }),

                TextColumn::make('ft')->label('Fattura')
                    ->searchable()->sortable(),
//                TextColumn::make('date_ft')->label('Data fattura'),
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

    public static function getEloquentQuery(): Builder
    {

        return parent::getEloquentQuery()->whereIn('project_id',auth()->user()->projects()->pluck('id'));
    }

}
