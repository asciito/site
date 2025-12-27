<?php

namespace App\Filament\Pages;

use BackedEnum;
use Coyotito\LaravelSettings\Settings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Override;

/**
 * Base class for settings page
 *
 * @property Schema $form
 */
abstract class SettingsPage extends Page
{
    use CanUseDatabaseTransactions;
    use HasUnsavedDataChangesAlert;
    use InteractsWithFormActions;

    public array $data = [];

    /**
     * @var ?Settings The actual settings used in the page
     */
    protected ?Settings $settings = null;

    /**
     * @var class-string<Settings>
     */
    protected static string $settingsClass;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.settings';

    #[Override]
    public function getTitle(): string|Htmlable
    {
        $group = $this->settings()->group;

        return 'Settings '.Str::headline($group);
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill($this->settings()->all());

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    public function settings(): Settings
    {
        if ($this->settings instanceof Settings) {
            return $this->settings;
        }

        return $this->settings = app()->make(static::$settingsClass);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getSettingsFields())
            ->statePath('data')
            ->inlineLabel($this->hasInlineLabels());
    }

    abstract protected function getSettingsFields(): array;

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->settings()->update($data)->save();

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
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
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

    public function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('Save'))
            ->action('save')
            ->keyBindings(['mod+s']);
    }

    public function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }
}
