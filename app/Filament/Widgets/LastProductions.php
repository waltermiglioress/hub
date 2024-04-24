<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductionResource;
use App\Tables\Columns\ProgressColumn;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\View\View;

class LastProductions extends BaseWidget
{

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = '15s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductionResource::getEloquentQuery()
            )
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at','desc')
            ->columns([
                TextColumn::make('project.code')->label('Commessa')->sortable(),
                TextColumn::make('client.name')->label('Cliente')->sortable(),
                TextColumn::make('desc')->label('Descrizione')->words(10)->wrap(),
                ColumnGroup::make('Date lavorazioni', [
                    TextColumn::make('date_start')->date('d/m/Y')->label('Data inizio'),
                    TextColumn::make('date_end')->date('d/m/Y')->label('Data fine'),
                ])->alignment(Alignment::Center)
                    ->wrapHeader(),
                TextColumn::make('type')->label('Identificativo'),
                ColumnGroup::make('Valori', [
                    TextColumn::make('value')->label('Valore')
                        ->money('eur', true)
                        ->sortable(),
                    ProgressColumn::make('percentage')
                        ->label('Percentuale')
                        ->sortable(),

                    TextColumn::make('imponibile')
                        ->label('Imponibile')
                        ->money('eur', true)->sortable()
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
                    ->color(fn(string $state): string => match ($state) {
                        'fatturato' => 'success',
                        'contabilizzato e non ft' => 'warning',
                        'stimato' => 'estimated',
                        'in corso' => 'primary',
                    }),

                TextColumn::make('updated_at')->dateTime('d/m/y H:i', 'Europe/Rome')->label('Ultimo aggiornamento'),
            ]);
    }
}
