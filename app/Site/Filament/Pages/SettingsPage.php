<?php

namespace App\Site\Filament\Pages;

use App\Site\Settings\SiteSettings;
use Coyotito\LaravelSettings\Settings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;

/**
 * @property Schema $form
 */
class SettingsPage extends Page
{
    use CanUseDatabaseTransactions;
    use HasUnsavedDataChangesAlert;
    use InteractsWithFormActions;

    protected static ?string $title = 'Settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $settings = SiteSettings::class;

    protected string $view = 'site.pages.settings';

    protected static bool $shouldRegisterNavigation = false;

    public array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill(static::getSettings()->all());

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $settings = static::getSettings();

            // TODO: Remove quick hack to fill settings once the package `coyotito/laravel-settings` supports mass assignment.
            $settings->update($data)->save();

            $this->callHook('afterSave');
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction()
                ? $this->rollBackDatabaseTransaction()
                : $this->commitDatabaseTransaction();

            return;
        }

        $this->commitDatabaseTransaction();

        $this->rememberData();

        $this->getSaveNotification()->send();

        $this->redirect(static::getNavigationUrl(), navigate: FilamentView::hasSpaMode());
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected static function getSettings(): Settings
    {
        return app()->make(static::$settings);
    }

    public function getSaveNotification(): Notification
    {
        $title = $this->getSaveNotificationTitle();
        $message = $this->getSaveNotificationMessage();

        return Notification::make()
            ->success()
            ->title($title)
            ->body($message);
    }

    public function getSaveNotificationTitle(): string
    {
        return __('Settings saved successfully.');
    }

    public function getSaveNotificationMessage(): string
    {
        return __('Your settings have been saved successfully.');
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    public function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('Save'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    public function getSubmitFormAction(): Action
    {
        return $this->getSaveFormAction();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Site Configuration'))
                    ->description('Configure the basic settings for your site.')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(32),
                        Textarea::make('description')
                            ->required()
                            ->maxLength(120),
                        // TODO: Add logo, media and favicon uploaders
                        Group::make([
                            TextInput::make('twitter_handler')
                                ->label('X/Twitter')
                                ->required()
                                ->prefixIcon('fab-x-twitter'),
                            TextInput::make('facebook_handler')
                                ->label('Facebook')
                                ->required()
                                ->prefixIcon('fab-facebook-f'),
                            TextInput::make('instagram_handler')
                                ->label('Instagram')
                                ->required()
                                ->prefixIcon('fab-instagram'),
                            TextInput::make('linkedin_handler')
                                ->label('LinkedIn')
                                ->required()
                                ->prefixIcon('fab-linkedin'),
                            TextInput::make('github_handler')
                                ->label('Github')
                                ->required()
                                ->prefixIcon('fab-github'),
                        ]),
                    ])
                    ->columnSpanFull()
                    ->aside(),
            ])
            ->inlineLabel($this->hasInlineLabels())
            ->statePath('data');
    }

    public function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema($this->getFormSchema())
                    ->statePath('data')
                    ->columns()
                    ->inlineLabel($this->hasInlineLabels())
            ),
        ];
    }
}
