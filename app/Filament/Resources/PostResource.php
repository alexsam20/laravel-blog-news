<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Main content')->schema([
                    Forms\Components\TextInput::make('title')
                        ->live()
                        ->required()->minLength(1)->maxLength(150)
                        ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                            if ($operation === 'edit') {
                                return;
                            }
                            $set('slug', Str::slug($state));
                        }),
                    Forms\Components\TextInput::make('slug')->required()->minLength(1)->unique(ignoreRecord: true)->maxLength(150),
                    Forms\Components\RichEditor::make('body')->required()->fileAttachmentsDirectory('posts/images')->columnSpanFull(),
                ])->columns(2),
                Forms\Components\Section::make('Meta')->schema([
                    Forms\Components\FileUpload::make('image')->image()->directory('posts/thumbnails'),
                    Forms\Components\DateTimePicker::make('published_at')->nullable(),
                    Forms\Components\Toggle::make('featured')->required(),
                    //Forms\Components\Checkbox::make('featured'),
                    Forms\Components\Select::make('author')
                        ->relationship('author', 'name')
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('categories')
                        ->multiple()
                        ->relationship('categories', 'title')
                        ->searchable(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('slug')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('author.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('published_at')->date('Y-m-d')->sortable()->searchable(),
                Tables\Columns\IconColumn::make('featured')
                    ->sortable()
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
