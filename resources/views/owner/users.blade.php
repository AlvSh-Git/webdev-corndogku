@extends('layouts.app')

@section('title', 'User Maintenance')

@section('content')

    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <h1 class="text-3xl md:text-5xl font-bold mb-8" style="color: var(--color-black);">
        User Maintenance
    </h1>

    {{-- ── User Card Grid ───────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        @php
            $users = [
                [
                    'name'     => 'Casey',
                    'role'     => 'Employee',
                    'position' => 'Cashier',
                    'branch'   => 'Surabaya',
                    'active'   => true,
                ],
                [
                    'name'     => 'Gaby',
                    'role'     => 'Owner',
                    'position' => 'Owner',
                    'branch'   => 'Surabaya',
                    'active'   => false,
                ],
                [
                    'name'     => 'Alvon',
                    'role'     => 'Employee',
                    'position' => 'Marketing',
                    'branch'   => 'Blora',
                    'active'   => true,
                ],
            ];
        @endphp

        {{-- ── User Cards ──────────────────────────────────────── --}}
        @foreach ($users as $user)
            <article class="relative rounded-3xl p-4 md:p-5 pt-4"
                     style="background-color: var(--color-white); box-shadow: var(--shadow-card);">

                {{-- Action Icons (top-right) --}}
                <div class="absolute top-4 right-4 flex items-center gap-3">
                    <button type="button"
                            title="Edit User"
                            class="p-2 md:p-1 rounded hover:opacity-70 transition-opacity min-h-[44px] min-w-[44px] md:min-h-0 md:min-w-0 flex items-center justify-center">
                        <img src="{{ asset('assets/ui/icon-edit-user.svg') }}"
                             alt="Edit"
                             class="w-5 h-5 md:w-4 md:h-4">
                    </button>
                    <button type="button"
                            title="Delete User"
                            class="p-2 md:p-1 rounded hover:opacity-70 transition-opacity min-h-[44px] min-w-[44px] md:min-h-0 md:min-w-0 flex items-center justify-center">
                        <img src="{{ asset('assets/ui/icon-delete-user.svg') }}"
                             alt="Delete"
                             class="w-5 h-5 md:w-4 md:h-4">
                    </button>
                </div>

                {{-- Name + Status Badge --}}
                <div class="flex items-center gap-2 mb-4 pr-16">
                    <span class="text-xl md:text-2xl font-semibold" style="color: var(--color-black);">
                        {{ $user['name'] }}
                    </span>

                    @if ($user['active'])
                        <span class="text-xs font-medium px-3 py-0.5 rounded-full"
                              style="background-color: var(--color-status-active-bg);
                                     color: var(--color-status-active-text);">
                            Active
                        </span>
                    @else
                        <span class="text-xs font-medium px-3 py-0.5 rounded-full"
                              style="background-color: var(--color-status-inactive-bg);
                                     color: var(--color-status-inactive-text);">
                            Non Active
                        </span>
                    @endif
                </div>

                {{-- Details --}}
                <dl class="space-y-1 text-sm">
                    <div class="flex gap-1">
                        <dt style="color: #9c9c9c;" class="shrink-0">Role:</dt>
                        <dd style="color: #757575;" class="font-medium">{{ $user['role'] }}</dd>
                    </div>
                    <div class="flex gap-1">
                        <dt style="color: #9c9c9c;" class="shrink-0">Position:</dt>
                        <dd style="color: #757575;" class="font-medium">{{ $user['position'] }}</dd>
                    </div>
                    <div class="flex gap-1">
                        <dt style="color: #9c9c9c;" class="shrink-0">Branch:</dt>
                        <dd style="color: #757575;" class="font-medium">{{ $user['branch'] }}</dd>
                    </div>
                </dl>
            </article>
        @endforeach

        {{-- ── Add User Card ────────────────────────────────────── --}}
        <button type="button"
                class="flex flex-col items-center justify-center gap-3 rounded-3xl p-5 border-2 border-dashed transition-colors hover:opacity-80"
                style="background-color: var(--color-light);
                       border-color: var(--color-border);
                       min-height: 160px;">

            {{-- Yellow circle with red plus, matching Figma design --}}
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

@endsection
