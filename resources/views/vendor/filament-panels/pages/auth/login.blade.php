
<div class="bg-white w-full h-screen dark:bg-gray-900">
    <div class="flex justify-center h-screen">
        <div class="ins-login-img hidden bg-cover lg:block lg:w-80" style="background-image:url({{asset('image/bg_login.JPG')}})">
{{--            <div class="flex items-center w-full h-full px-20 bg-gray-900 bg-black/50">--}}
                <div class="ins-login-wrapper">
                    <h2 >Insider - Sicilsaldo Group</h2>

                    <p >Piattaforma per l'accesso alle risorse e agli strumenti necessari per collaborare, comunicare e lavorare in modo efficiente all'interno del nostro gruppo. Vi invitiamo ad inserire le vostre credenziali per accedere al vostro profilo personale e iniziare a esplorare le varie funzionalità messe a disposizione. Grazie per essere parte della nostra community aziendale.</p>
                </div>
{{--            </div>--}}
        </div>

        <div class="flex items-center w-full max-w-md px-6 mx-auto w-1/4">
            <div class="flex-1">
                <div class="login-logo">
                    <img class="mx-auto w-32" src="{{asset('image/logo.png')}}" alt=""/>
                </div>
                <div class="text-center">
                    <h2 class="text-4xl font-bold text-center text-gray-700 dark:text-white">Insider - Platform</h2>

                    <p class="mt-3 text-gray-500 dark:text-gray-300">Sign in to access your account</p>
                </div>

                <div class="mt-8">
{{--                    @if (filament()->hasRegistration())--}}
{{--                        <x-slot name="subheading">--}}
{{--                            {{ __('filament-panels::pages/auth/login.actions.register.before') }}--}}

{{--                            {{ $this->registerAction }}--}}
{{--                        </x-slot>--}}
{{--                    @endif--}}
                    <x-filament-panels::form wire:submit="authenticate">
                        {{ $this->form }}

                        <x-filament-panels::form.actions
                            :actions="$this->getCachedFormActions()"
                            :full-width="$this->hasFullWidthFormActions()"
                        />
                    </x-filament-panels::form>

                    {{--                    <form>--}}
                    {{--                        <div>--}}
                    {{--                            <label for="email" class="block mb-2 text-sm text-gray-600 dark:text-gray-200">Email Address</label>--}}
                    {{--                            <input type="email" name="email" id="email" placeholder="example@example.com" class="block w-full px-4 py-2 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200 rounded-md dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-400 dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40" />--}}
                    {{--                        </div>--}}

                    {{--                        <div class="mt-6">--}}
                    {{--                            <div class="flex justify-between mb-2">--}}
                    {{--                                <label for="password" class="text-sm text-gray-600 dark:text-gray-200">Password</label>--}}
                    {{--                                <a href="#" class="text-sm text-gray-400 focus:text-blue-500 hover:text-blue-500 hover:underline">Forgot password?</a>--}}
                    {{--                            </div>--}}

                    {{--                            <input type="password" name="password" id="password" placeholder="Your Password" class="block w-full px-4 py-2 mt-2 text-gray-700 placeholder-gray-400 bg-white border border-gray-200 rounded-md dark:placeholder-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 focus:border-blue-400 dark:focus:border-blue-400 focus:ring-blue-400 focus:outline-none focus:ring focus:ring-opacity-40" />--}}
                    {{--                        </div>--}}

                    {{--                        <div class="mt-6">--}}
                    {{--                            <button--}}
                    {{--                                class="w-full px-4 py-2 tracking-wide text-white transition-colors duration-200 transform bg-blue-500 rounded-md hover:bg-blue-400 focus:outline-none focus:bg-blue-400 focus:ring focus:ring-blue-300 focus:ring-opacity-50">--}}
                    {{--                                Sign in--}}
                    {{--                            </button>--}}
                    {{--                        </div>--}}

                    {{--                    </form>--}}

{{--                    <p class="mt-6 text-sm text-center text-gray-400">Don&#x27;t have an account yet? <a href="#" class="text-blue-500 focus:outline-none focus:underline hover:underline">Sign up</a>.</p>--}}
                </div>
            </div>
        </div>
    </div>
</div>
