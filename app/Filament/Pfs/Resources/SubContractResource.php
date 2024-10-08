<?php

namespace App\Filament\Pfs\Resources;

use App\Filament\Pfs\Resources\SubContractResource\Pages;
use App\Filament\Pfs\Resources\SubContractResource\RelationManagers;
use App\Models\SubContract;
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

//    protected static ?string $navigationGroup = 'Contratti';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';

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
            'index' => Pages\ListSubContracts::route('/'),
            'create' => Pages\CreateSubContract::route('/create'),
            'edit' => Pages\EditSubContract::route('/{record}/edit'),
        ];
    }
}
