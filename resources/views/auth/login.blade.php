<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-purple-700">HỌC VIỆN ABC</h1>
        <p class="text-gray-600">Hệ thống Quản lý Đào tạo</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="bg-white p-8 rounded-lg shadow-md">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Mật khẩu')" />

                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Ghi nhớ đăng nhập') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" href="{{ route('password.request') }}">
                        {{ __('Quên mật khẩu?') }}
                    </a>
                @endif

                <x-primary-button class="ms-3 bg-purple-600 hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900">
                    {{ __('Đăng nhập') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    @if (config('app.env') === 'local')
        <div class="mt-8 p-4 bg-gray-100 rounded-lg border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Thông tin đăng nhập DEMO:</h3>
            <ul class="text-xs text-gray-600 space-y-1">
                <li><span class="font-bold">Admin:</span> admin@academy.com / password123</li>
                <li><span class="font-bold">Giảng viên:</span> gv@academy.com / password123</li>
                <li><span class="font-bold">Học viên:</span> hv@academy.com / password123</li>
            </ul>
        </div>
    @endif
</x-guest-layout>
