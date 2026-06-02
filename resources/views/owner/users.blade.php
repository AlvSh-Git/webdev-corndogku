@extends('layouts.app')

@section('title', 'User Maintenance')

@section('content')

    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <h1 class="text-3xl md:text-5xl font-bold mb-7" style="color: var(--color-black);">
        User Maintenance
    </h1>

    {{-- ── Success Alert ───────────────────────────────────────── --}}
    @if (session('success'))
        <div id="pageAlert"
             class="mb-5 px-4 py-3 rounded-xl text-sm font-medium transition-opacity duration-500"
             style="background-color: #DCFCE7; color: #15803D;">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Error Alert ─────────────────────────────────────────── --}}
    @if ($errors->any())
        <div id="pageAlert"
             class="mb-5 px-4 py-3 rounded-xl text-sm font-medium transition-opacity duration-500"
             style="background-color: #FEE2E2; color: #B91C1C;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- ── User Card Grid ───────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

        {{-- ── User Cards from Database ─────────────────────────── --}}
        @forelse ($users as $user)
            <article class="relative rounded-3xl p-5"
                     style="background-color: var(--color-white); box-shadow: var(--shadow-card); min-height: 170px;">

                {{-- Action Icons --}}
                <div class="absolute top-5 right-5 flex items-center gap-3">
                    <button type="button"
                            title="Edit User"
                            onclick="openEditUserModal(
                                {{ $user->id }},
                                @js($user->name),
                                @js($user->username),
                                @js($user->email),
                                @js($user->active ? 'active' : 'inactive')
                            )"
                            class="hover:opacity-70 transition-opacity">
                        <img src="{{ asset('assets/ui/icon-edit-user.svg') }}"
                             alt="Edit"
                             class="w-5 h-5">
                    </button>

                    <button type="button"
                            title="Delete User"
                            onclick="openDeleteUserModal({{ $user->id }}, @js($user->name))"
                            class="hover:opacity-70 transition-opacity">
                        <img src="{{ asset('assets/ui/icon-delete-user.svg') }}"
                             alt="Delete"
                             class="w-5 h-5">
                    </button>
                </div>

                {{-- Name + Status Badge --}}
                <div class="flex items-center gap-3 mb-4 pr-20 min-w-0">

                    <span class="text-xl md:text-2xl font-semibold leading-tight truncate whitespace-nowrap overflow-hidden"
                          style="color: var(--color-black); max-width: 240px;">
                        {{ $user->name }}
                    </span>

                    @if ($user->active)
                        <span class="text-xs font-medium px-3 py-1 rounded-full whitespace-nowrap shrink-0"
                              style="background-color: var(--color-status-active-bg);
                                     color: var(--color-status-active-text);">
                            Active
                        </span>
                    @else
                        <span class="text-xs font-medium px-3 py-1 rounded-full whitespace-nowrap shrink-0"
                              style="background-color: var(--color-status-inactive-bg);
                                     color: var(--color-status-inactive-text);">
                            Non Active
                        </span>
                    @endif

                </div>

                {{-- Divider --}}
                <div class="w-full h-px mb-4" style="background-color: #E5E5E5;"></div>

                {{-- Phone + Email --}}
                <div class="space-y-3 text-sm md:text-base" style="color: #6B7280;">

                    <div class="flex items-center gap-3">
                        <span class="w-5 flex justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="2"
                                 stroke="currentColor"
                                 class="w-4 h-4">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.125 1.125 0 0 0-1.173.417l-.97 1.293a1.125 1.125 0 0 1-1.21.38 12.035 12.035 0 0 1-7.143-7.143 1.125 1.125 0 0 1 .38-1.21l1.293-.97a1.125 1.125 0 0 0 .417-1.173L6.963 3.102A1.125 1.125 0 0 0 5.872 2.25H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                            </svg>
                        </span>

                        <span class="truncate">
                            {{ $user->phone ?? '0812-3456-7890' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="w-5 flex justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="2"
                                 stroke="currentColor"
                                 class="w-4 h-4">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615A2.25 2.25 0 0 1 2.25 6.993V6.75" />
                            </svg>
                        </span>

                        <span class="truncate">
                            {{ $user->email ?? '-' }}
                        </span>
                    </div>

                </div>
            </article>
        @empty
            <article class="relative rounded-3xl p-5 flex items-center justify-center"
                     style="background-color: var(--color-white); box-shadow: var(--shadow-card); min-height: 170px;">
                <p class="text-sm font-medium text-center" style="color: #757575;">
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
                       min-height: 170px;">

            <span class="flex items-center justify-center w-12 h-12 rounded-full"
                  style="background-color: var(--color-accent);">
                <img src="{{ asset('assets/ui/icon-plus.svg') }}"
                     alt="Add"
                     class="w-6 h-6">
            </span>

            <span class="text-base font-semibold" style="color: var(--color-black);">
                Add User
            </span>
        </button>

    </div>


    {{-- ── Add User Modal ───────────────────────────────────────── --}}
    <div id="addUserModal"
         class="fixed inset-0 z-50 hidden items-center justify-center"
         style="background-color: rgba(0, 0, 0, 0.55);">

        <div class="w-[90%] max-w-md rounded-3xl bg-white p-6 md:p-7 relative">

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

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Name
                    </label>

                    <input type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Name"
                            maxlength="15"
                            required
                            class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                            style="border-color: #DADADA;">
                </div>

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


    {{-- ── Edit User Modal ──────────────────────────────────────── --}}
    <div id="editUserModal"
         class="fixed inset-0 z-50 hidden items-center justify-center"
         style="background-color: rgba(0, 0, 0, 0.55);">

        <div class="w-[90%] max-w-md rounded-3xl bg-white p-6 md:p-7 relative">

            <button type="button"
                    onclick="closeEditUserModal()"
                    class="absolute top-6 right-6 text-3xl leading-none hover:opacity-60"
                    style="color: var(--color-black);">
                &times;
            </button>

            <h2 class="text-2xl font-bold mb-6" style="color: var(--color-black);">
                Update User
            </h2>

            <form id="editUserForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Name
                    </label>

                    <input type="text"
                            id="edit_name"
                            name="name"
                            placeholder="Name"
                            maxlength="15"
                            required
                            class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                            style="border-color: #DADADA;">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Username
                    </label>

                    <input type="text"
                           id="edit_username"
                           name="username"
                           placeholder="Username"
                           required
                           class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                           style="border-color: #DADADA;">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Status
                    </label>

                    <div class="flex flex-wrap gap-3">
                        <label class="flex items-center gap-3 px-4 py-3 rounded-lg border cursor-pointer"
                               style="border-color: #76D49B; background-color: #F0FFF6;">
                            <input type="radio"
                                   id="edit_status_active"
                                   name="status"
                                   value="active">
                            <span>Active</span>
                        </label>

                        <label class="flex items-center gap-3 px-4 py-3 rounded-lg border cursor-pointer"
                               style="border-color: #DADADA;">
                            <input type="radio"
                                   id="edit_status_inactive"
                                   name="status"
                                   value="inactive">
                            <span>Non Active</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Email
                    </label>

                    <input type="email"
                           id="edit_email"
                           name="email"
                           placeholder="example@gmail.com"
                           required
                           class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                           style="border-color: #DADADA;">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Password
                    </label>

                    <input type="password"
                           name="password"
                           placeholder="Kosongkan jika tidak ingin mengubah password"
                           class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                           style="border-color: #DADADA;">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold" style="color: var(--color-black);">
                        Confirm Password
                    </label>

                    <input type="password"
                           name="password_confirmation"
                           placeholder="Kosongkan jika tidak ingin mengubah password"
                           class="w-full rounded-lg border px-4 py-3 text-sm outline-none"
                           style="border-color: #DADADA;">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button"
                            onclick="closeEditUserModal()"
                            class="px-8 py-3 rounded-full border font-semibold hover:opacity-80"
                            style="border-color: var(--color-black); color: var(--color-black);">
                        Cancel
                    </button>

                    <button type="submit"
                            class="px-8 py-3 rounded-full font-semibold text-white hover:opacity-90"
                            style="background-color: #B9151B;">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ── Delete User Modal ────────────────────────────────────── --}}
    <div id="deleteUserModal"
         class="fixed inset-0 z-50 hidden items-center justify-center"
         style="background-color: rgba(0, 0, 0, 0.55);">

        <div class="w-[90%] max-w-md rounded-3xl bg-white px-8 py-8 relative"
             style="box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);">

            <button type="button"
                    onclick="closeDeleteUserModal()"
                    class="absolute top-5 right-6 text-3xl leading-none hover:opacity-60"
                    style="color: var(--color-black);">
                &times;
            </button>

            <div class="text-center pt-5">
                <h2 class="text-2xl font-bold leading-tight mb-4" style="color: var(--color-black);">
                    Are you sure you want to delete<br>
                    this user?
                </h2>

                <p class="text-lg mb-8" style="color: #222222;">
                    This action cannot be undone.
                </p>
            </div>

            <form id="deleteUserForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="flex justify-center gap-4">
                    <button type="button"
                            onclick="closeDeleteUserModal()"
                            class="w-40 py-3 rounded-full border font-semibold hover:opacity-80"
                            style="border-color: var(--color-black); color: var(--color-black);">
                        Cancel
                    </button>

                    <button type="submit"
                            class="w-40 py-3 rounded-full font-semibold text-white hover:opacity-90"
                            style="background-color: #B9151B;">
                        Delete
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

        function openEditUserModal(id, name, username, email, status) {
            const modal = document.getElementById('editUserModal');
            const form = document.getElementById('editUserForm');

            form.action = `/owner/users/${id}`;

            document.getElementById('edit_name').value = name ?? '';
            document.getElementById('edit_username').value = username ?? '';
            document.getElementById('edit_email').value = email ?? '';

            if (status === 'active') {
                document.getElementById('edit_status_active').checked = true;
            } else {
                document.getElementById('edit_status_inactive').checked = true;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditUserModal() {
            const modal = document.getElementById('editUserModal');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openDeleteUserModal(id, name) {
            const modal = document.getElementById('deleteUserModal');
            const form = document.getElementById('deleteUserForm');

            form.action = `/owner/users/${id}`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteUserModal() {
            const modal = document.getElementById('deleteUserModal');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const alertBox = document.getElementById('pageAlert');

            if (alertBox) {
                setTimeout(function () {
                    alertBox.style.opacity = '0';

                    setTimeout(function () {
                        alertBox.remove();
                    }, 500);
                }, 3000);
            }
        });
    </script>

@endsection
