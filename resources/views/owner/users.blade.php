@extends('layouts.app')

@section('title', 'User Maintenance')

@section('content')

    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <h1 class="text-3xl md:text-5xl font-bold mb-8" style="color: var(--color-black);">
        User Maintenance
    </h1>

    {{-- ── Success Alert ───────────────────────────────────────── --}}
    @if (session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #DCFCE7; color: #15803D;">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Error Alert ─────────────────────────────────────────── --}}
    @if ($errors->any())
        <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #FEE2E2; color: #B91C1C;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- ── User Card Grid ───────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- ── User Cards from Database ─────────────────────────── --}}
        @forelse ($users as $user)
            <article class="relative rounded-3xl p-6 md:p-8"
                     style="background-color: var(--color-white); box-shadow: var(--shadow-card); min-height: 250px;">

                {{-- Action Icons (top-right) --}}
                <div class="absolute top-6 right-6 flex items-center gap-4">
                    <button type="button"
                            title="Edit User"
                            class="hover:opacity-70 transition-opacity">
                        <img src="{{ asset('assets/ui/icon-edit-user.svg') }}"
                             alt="Edit"
                             class="w-7 h-7">
                    </button>

                    <button type="button"
                            title="Delete User"
                            class="hover:opacity-70 transition-opacity">
                        <img src="{{ asset('assets/ui/icon-delete-user.svg') }}"
                             alt="Delete"
                             class="w-7 h-7">
                    </button>
                </div>

                {{-- Name + Status Badge --}}
                <div class="flex items-center gap-4 mb-6 pr-24">
                    <span class="text-3xl md:text-4xl font-semibold" style="color: var(--color-black);">
                        {{ $user->name }}
                    </span>

                    @if ($user->active)
                        <span class="text-base md:text-lg font-medium px-5 py-1 rounded-full"
                              style="background-color: var(--color-status-active-bg);
                                     color: var(--color-status-active-text);">
                            Active
                        </span>
                    @else
                        <span class="text-base md:text-lg font-medium px-5 py-1 rounded-full"
                              style="background-color: var(--color-status-inactive-bg);
                                     color: var(--color-status-inactive-text);">
                            Non Active
                        </span>
                    @endif
                </div>

                {{-- Divider --}}
                <div class="w-full h-px mb-6" style="background-color: #E5E5E5;"></div>

                {{-- Phone + Email --}}
                <div class="space-y-4 text-lg md:text-xl" style="color: #6B7280;">

                    <div class="flex items-center gap-5">
                        <span class="w-6 flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="2"
                                 stroke="currentColor"
                                 class="w-6 h-6">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.125 1.125 0 0 0-1.173.417l-.97 1.293a1.125 1.125 0 0 1-1.21.38 12.035 12.035 0 0 1-7.143-7.143 1.125 1.125 0 0 1 .38-1.21l1.293-.97a1.125 1.125 0 0 0 .417-1.173L6.963 3.102A1.125 1.125 0 0 0 5.872 2.25H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </span>

                        <span>
                            {{ $user->phone ?? '0812-3456-7890' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-5">
                        <span class="w-6 flex justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="2"
                                 stroke="currentColor"
                                 class="w-6 h-6">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615A2.25 2.25 0 0 1 2.25 6.993V6.75" />
                            </svg>
                        </span>

                        <span>
                            {{ $user->email ?? '-' }}
                        </span>
                    </div>

                </div>
            </article>
        @empty
            {{-- Kalau database masih kosong --}}
            <article class="relative rounded-3xl p-6 md:p-8"
                     style="background-color: var(--color-white); box-shadow: var(--shadow-card); min-height: 250px;">
                <p class="text-sm md:text-base font-medium" style="color: #757575;">
                    Belum ada user. Klik Add User untuk menambahkan data baru.
                </p>
            </article>
        @endforelse

        {{-- ── Add User Card ────────────────────────────────────── --}}
        <button type="button"
                onclick="openAddUserModal()"
                class="flex flex-col items-center justify-center gap-3 rounded-3xl p-5 border-2 border-dashed transition-colors hover:opacity-80"
                style="background-color: var(--color-light);
                       border-color: var(--color-border);
                       min-height: 250px;">

            {{-- Yellow circle with red plus --}}
            <span class="flex items-center justify-center w-14 h-14 rounded-full"
                  style="background-color: var(--color-accent);">
                <img src="{{ asset('assets/ui/icon-plus.svg') }}"
                     alt="Add"
                     class="w-7 h-7">
            </span>

            <span class="text-lg font-semibold" style="color: var(--color-black);">
                Add User
            </span>
        </button>

    </div><!-- /.grid -->


    {{-- ── Add User Modal ───────────────────────────────────────── --}}
    <div id="addUserModal"
         class="fixed inset-0 z-50 hidden items-center justify-center"
         style="background-color: rgba(0, 0, 0, 0.55);">

        <div class="w-[90%] max-w-md rounded-3xl bg-white p-6 md:p-7 relative">

            {{-- Close Button --}}
            <button type="button"
                    onclick="closeAddUserModal()"
                    class="absolute top-6 right-6 text-3xl leading-none hover:opacity-60"
                    style="color: var(--color-black);">
                &times;
            </button>

            <h2 class="text-2xl font-bold mb-6" style="color: var(--color-black);">
                Add New User
            </h2>

            <form action="{{ route('owner.users.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Name
                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Name"
                           required
                           class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                           style="border-color: #DADADA;">
                </div>

                {{-- Username --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Username
                    </label>

                    <input type="text"
                           name="username"
                           value="{{ old('username') }}"
                           placeholder="Username"
                           required
                           class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                           style="border-color: #DADADA;">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Status
                    </label>

                    <div class="flex flex-wrap gap-3">
                        <label class="flex items-center gap-3 px-4 py-3 rounded-lg border cursor-pointer"
                               style="border-color: #76D49B; background-color: #F0FFF6;">
                            <input type="radio"
                                   name="status"
                                   value="active"
                                   {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
                            <span>Active</span>
                        </label>

                        <label class="flex items-center gap-3 px-4 py-3 rounded-lg border cursor-pointer"
                               style="border-color: #DADADA;">
                            <input type="radio"
                                   name="status"
                                   value="inactive"
                                   {{ old('status') === 'inactive' ? 'checked' : '' }}>
                            <span>Non Active</span>
                        </label>
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Email
                    </label>

                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="example@gmail.com"
                           required
                           class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                           style="border-color: #DADADA;">
                </div>

                {{-- Password --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Password
                    </label>

                    <input type="password"
                           name="password"
                           placeholder="********"
                           required
                           class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                           style="border-color: #DADADA;">
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Confirm Password
                    </label>

                    <input type="password"
                           name="password_confirmation"
                           placeholder="********"
                           required
                           class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                           style="border-color: #DADADA;">
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button"
                            onclick="closeAddUserModal()"
                            class="px-8 py-3 rounded-full border font-semibold hover:opacity-80"
                            style="border-color: var(--color-black); color: var(--color-black);">
                        Cancel
                    </button>

                    <button type="submit"
                            class="px-8 py-3 rounded-full font-semibold text-white hover:opacity-90"
                            style="background-color: #B9151B;">
                        Add User
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ── Modal Script ─────────────────────────────────────────── --}}
    <script>
        function openAddUserModal() {
            const modal = document.getElementById('addUserModal');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAddUserModal() {
            const modal = document.getElementById('addUserModal');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>

@endsection
