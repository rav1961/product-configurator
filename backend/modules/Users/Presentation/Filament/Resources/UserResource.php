<?php

declare(strict_types=1);

namespace Modules\Users\Presentation\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use Modules\Users\Domain\Enums\Role;
use Modules\Users\Domain\Models\User;
use Modules\Users\Presentation\Filament\Resources\UserResource\Pages\CreateUser;
use Modules\Users\Presentation\Filament\Resources\UserResource\Pages\EditUser;
use Modules\Users\Presentation\Filament\Resources\UserResource\Pages\ListUsers;
use Spatie\Permission\Models\Role as SpatieRole;

final class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string
    {
        return __('users.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('users.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('users.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('users.label.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('users.fields.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label(__('users.fields.email'))
                ->required()
                ->email()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            TextInput::make('password')
                ->label(__('users.fields.password'))
                ->password()
                ->revealable()
                ->maxLength(255)
                ->required(static fn (string $operation): bool => $operation === 'create')
                ->dehydrated(static fn (?string $state): bool => filled($state))
                ->rule(Password::default(), static fn (?string $state): bool => filled($state)),
            Select::make('roles')
                ->label(__('users.fields.roles'))
                ->multiple()
                ->preload()
                ->relationship(
                    name: 'roles',
                    titleAttribute: 'name',
                    modifyQueryUsing: static fn (Builder $query): Builder => $query->whereIn('name', self::assignableRoleNames()),
                )
                ->getOptionLabelFromRecordUsing(
                    static fn (SpatieRole $record): string => __('users.role.'.$record->name),
                )
                ->visible(static fn (?User $record): bool => self::assignableRoleNames() !== []
                    && ! ($record instanceof User && $record->is(auth()->user()))),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('users.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('users.fields.email'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('roles.name')
                    ->label(__('users.fields.roles'))
                    ->badge()
                    ->formatStateUsing(static fn (string $state): string => Role::tryFrom($state)?->label() ?? $state),
                TextColumn::make('email_verified_at')
                    ->label(__('users.fields.email_verified_at'))
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('users.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $actor = auth()->user();

        if (! $actor instanceof User) {
            return $query;
        }

        $blockedRoleNames = array_values(array_map(
            static fn (Role $role): string => $role->value,
            array_filter(
                Role::cases(),
                static fn (Role $role): bool => $role->rank() >= $actor->rank(),
            ),
        ));

        return $query->where(static function (Builder $builder) use ($blockedRoleNames, $actor): void {
            $builder
                ->whereDoesntHave('roles', static fn (Builder $roles): Builder => $roles->whereIn('name', $blockedRoleNames))
                ->orWhere($actor->getKeyName(), $actor->getKey());
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    /**
     * @return list<string>
     */
    private static function assignableRoleNames(): array
    {
        $actor = auth()->user();

        if (! $actor instanceof User) {
            return [];
        }

        return $actor->highestRole()?->assignableRoles() ?? [];
    }
}
