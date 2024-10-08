<?php

namespace App\Filament\Clusters\Commessa\Resources;

use App\Filament\Clusters\Commessa;
use App\Filament\Clusters\Commessa\Resources\SubContractResource\Pages;
use App\Filament\Clusters\Commessa\Resources\SubContractResource\RelationManagers;
use App\Models\SubContract;
use App\Tables\Columns\ProgressColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubContractResource extends Resource
{
    protected static ?string $model = SubContract::class;

    protected static ?string $modelLabel = 'Subappalto';

    protected static ?string $pluralModelLabel = 'Subappalti';

    protected static ?string $navigationGroup = 'Contratti';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $cluster = Commessa::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('client.avatar'),
                Tables\Columns\TextColumn::make('project.contract')
                    ->label('Contratto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Subappaltatore')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.contractor')
                    ->label('Appaltatore')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.desc')
                    ->label('Descrizione breve')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('referent')
                    ->label('Referente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data creazione')
                    ->date('d/m/Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.desc')
                    ->label('Descrizione breve')
                    ->searchable()
                    ->sortable(),
                ProgressColumn::make('percentage')
                    ->label('Percentuale')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (SubContract $record): bool => auth()->user()->can('update_sub::contract', $record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (SubContract $record): bool => auth()->user()->can('delete_sub::contract', $record)),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubContracts::route('/'),
        ];
    }
}
