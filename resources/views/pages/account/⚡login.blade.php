<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.guest')] class extends Component {
    public $username = '';
    public $password = '';

    public function authenticate()
    {
        $this->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);
        $user = User::where('username', $this->username)->first();
        if ($user && Hash::check($this->password, $user->password)) {
            session()->regenerate();
            Auth::login($user);
            return redirect()->route('beranda');
        }
        $this->addError('username', 'Username atau password yang Anda masukkan salah.');
    }
};
?>

<div>
    <button class="icon-button theme-toggle auth-theme-toggle" type="button" data-theme-toggle
        aria-label="Switch color theme" title="Switch color theme">
        <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
    </button>
    <main class="auth-page">
        <section class="auth-card text-center">
            <span class="brand-icon"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i></span>
            <div class="my-4">
                <p class="eyebrow mb-1 fs-6">SISTEM INFORMASI MANAJEMEN PEGAWAI</p>
            </div>
            <form wire:submit="authenticate" class="needs-validation text-start" novalidate>
                <x-partials.forms.input name="username" label="Username" wire:model="username"></x-partials.forms.input>

                <div class="my-4">
                    <div class="position-relative">
                        <x-partials.forms.input setcol="col-12" name="password" label="Password" type="password"
                            wire:model="password" id="passwordInput" />

                        <button type="button"
                            class="btn position-absolute end-0 border-0 bg-transparent text-muted px-3 me-1"
                            id="togglePassword" style="z-index: 10; top: 27px; left:23.5rem;">
                            <i class="bi bi-eye fs-4" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button class="btn btn-primary w-100" type="submit"><i class="bi bi-box-arrow-in-right"
                        aria-hidden="true"></i> Sign In</button>
            </form>

        </section>
    </main>
</div>
