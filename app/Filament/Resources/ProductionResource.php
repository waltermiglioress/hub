<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ProductionExporter;
use App\Filament\Resources\ProductionResource\Pages;

use App\Filament\Resources\ProductionResource\Widgets\ProductionOverview;
use App\Models\Production;
use App\Models\Project;
use App\Models\User;
use App\Tables\Columns\ProgressColumn;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\Alignment;
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
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
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
                        TextInput::make('desc')->label('Descrizione attività')->columnSpan(2),
                        TextInput::make('type')
                            ->datalist([
                                'SAL',
                                'ODL',
                                'MAP',
                            ])
                            ->label('Identificativo')
                            ->required(),
//                        TextInput::make('doc_id')->label('ID Documento')->required(),
                        TextInput::make('percentage')
                            ->label('Percentuale')
                            ->suffix('%')
                            ->numeric()
                            ->maxValue(100)
                            ->afterStateUpdated(function (Set $set,$get, $state){
                                $calc=(int)$get('value')*($state/100);
                                $set('imponibile',$calc);
                            })
                            ->debounce(600)
                            ->required(),

                        TextInput::make('value')
                            //->mask(RawJs::make('$money($input)'))
                            ->stripCharacters('.')
                            ->live()
                            ->numeric()
                            ->prefix('€')
                            ->label('Valore produzione')
                            ->afterStateUpdated(function (Set $set,$get, $state){
                                $calc=(int)$get('percentage')*($state/100);
                                $set('imponibile',$calc);
                            })
                            ->debounce(600)
                            ->required(),


                        TextInput::make('imponibile')
                        ->prefix('€')
                        ->readOnly()

                        ->live(),
                        MarkdownEditor::make('note')->label('Note')->columnSpanFull(),




                ])->columns(4)->columnSpan(2),
                Group::make()
                ->schema([
                    Section::make('Dettagli')
                        ->schema([
                            Select::make('status')->label('Stato')
                                ->options([
                                    'fatturato'=>'FATTURATO',
                                    'contabilizzato e non ft'=>'CONTABILIZZATO E NON FATTURATO',
                                    'stimato'=>'STIMATO',
                                    'in corso'=>'IN CORSO',
                                ])
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
                            Forms\Components\FileUpload::make('allegati')
                        ]),

                ])->columnSpan(1)

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.code')->label('Commessa')->searchable()->sortable(),
                TextColumn::make('client.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('desc')->label('Descrizione')->words(10)->wrap(),
                ColumnGroup::make('Date',[
                    TextColumn::make('date_start')->date('d/m/Y')->label('Data inizio'),
                    TextColumn::make('date_end')->date('d/m/Y')->label('Data fine'),
                ])->alignment(Alignment::Center)
                    ->wrapHeader(),
                TextColumn::make('type')->label('Identificativo')->searchable(),
                ColumnGroup::make('Valori',[
                    TextColumn::make('value')->label('Valore')
                        ->money('eur',true)
                        ->sortable()
                        ->summarize(
                            Sum::make()->label('Totale')->money('EUR')
                        ),
                    ProgressColumn::make('percentage')
                        ->label('Percentuale'),

                    TextColumn::make('imponibile')
                        ->label('Imponibile')
                        ->money('eur',true)->sortable()
                        ->summarize(
                            Sum::make()->label('Totale')->money('EUR'))
                ])->alignment(Alignment::Center)
                    ->wrapHeader(),


                TextColumn::make('status')->label('Stato')
//                    ->options([
//                        'fatturato'=>'FATTURATO',
//                        'contabilizzato e non ft'=>'CONTABILIZZATO E NON FATTURATO',
//                        'stimato'=>'STIMATO',
//                        'in corso'=>'IN CORSO',
//                    ]),
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fatturato' => 'success',
                        'contabilizzato e non ft' => 'warning',
                        'stimato' => 'estimated',
                        'in corso'=>'primary',
                    }),

                TextColumn::make('updated_at')->dateTime('d/m/y H:i','Europe/Rome')->label('Ultimo aggiornamento'),


//                TextColumn::make('ft')->label('Fattura')
//                    ->searchable()->sortable(),
//                TextColumn::make('date_ft')->label('Data fattura'),
            ])

//            ->groups([
//                'status',
//                'type',
//            ])
//            ->defaultGroup('status')

            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->multiple()
                    ->options([
                        'fatturato'=>'FATTURATO',
                        'contabilizzato e non ft'=>'CONTABILIZZATO E NON FATTURATO',
                        'stimato'=>'STIMATO',
                        'in corso'=>'IN CORSO',
                    ]),
                SelectFilter::make('project_id')
                    ->label('Commessa')
                    ->options(Project::whereHas('users',function ($query){
                        $query->where('user_id',Auth::id());
                    })->pluck('code','id'))
                    ->searchable()
                    ->preload(),


                SelectFilter::make('client.name')->label('Cliente')
                    ->preload()
                ->searchable(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Data inizio'),
                        DatePicker::make('created_until')->label('Data fine'),
                    ])->columns(2)->columnSpan(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_start', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_end', '<=', $date),
                            );
                    })
            ],layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exporter(ProductionExporter::class)
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
