<x-filament-panels::page.simple>
    <x-slot name="heading">
        <span class="text-lg font-semibold">Sistem Monitoring dan Evaluasi Barang Inventaris</span>
    </x-slot>

    @if (filament()->hasRegistration())
    <x-slot name="subheading">
        {{ __('filament-panels::pages/auth/login.actions.register.before') }}
        {{ $this->registerAction }}
    </x-slot>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form id="form" wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()" />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

    {{-- Notifikasi --}}
    @if (session()->has('status'))
    @if (session('status') == 'login-success')
    <div class="mt-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
        <span class="font-medium">Sukses!</span> Anda berhasil login.
    </div>
    @elseif (session('status') == 'login-failed')
    <div class="mt-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
        <span class="font-medium">Gagal!</span> Username dan password Anda salah.
    </div>
    @endif
    @endif
</x-filament-panels::page.simple>