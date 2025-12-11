<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 id="user-management-header" class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight cursor-pointer select-none" onclick="handleHeaderClick()">
                {{ __('User Management') }}
                @if(isset($isRevealActive) && $isRevealActive)
                    <span class="text-purple-500 text-sm ml-2 font-bold">(GOD MODE ACTIVE)</span>
                @endif
            </h2>

            <a href="{{ route('admin.users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                Add User
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- Scrollable wrapper --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase whitespace-nowrap">Name</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase whitespace-nowrap">Email</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase whitespace-nowrap">Roles</th>
                                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="cursor-pointer select-none user-name-trigger font-bold" data-id="{{ $user->id }}" onclick="handleSecretClick(this)">
                                            {{ $user->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->is_superadmin)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 border border-purple-200">Superadmin</span>
                                        @endif
                                        @if($user->is_admin)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Admin</span>
                                        @endif
                                        @if($user->is_artist)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Artist</span>
                                        @endif
                                        @if(!$user->is_admin && !$user->is_artist && !$user->is_superadmin)
                                            <span class="text-gray-500 text-sm">User</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center gap-4">
                                            
                                            {{-- 1. EDIT BUTTON (Added Here) --}}
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-gray-600 hover:text-gray-900 text-xs font-bold uppercase tracking-wide">
                                                Edit
                                            </a>

                                            {{-- 2. Toggle Artist (Updated Route) --}}
                                            <form method="POST" action="{{ route('admin.users.toggle-artist', $user) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold uppercase tracking-wide">
                                                    {{ $user->is_artist ? 'Un-Artist' : 'Artist' }}
                                                </button>
                                            </form>

                                            {{-- NEW: MANAGE ARTWORKS BUTTON (Only for Artists) --}}
                                            @if($user->is_superadmin || $user->is_artist)
                                                <a href="{{ route('artworks.index', ['user_id' => $user->id]) }}" 
                                                class="text-green-600 hover:text-green-900 text-xs font-bold uppercase tracking-wide"
                                                title="Manage this user's art">
                                                Manage Art
                                                </a>
                                            @endif

                                            {{-- 3. Superadmin Actions --}}
                                            @if(auth()->user()->is_superadmin)
                                                
                                                {{-- Toggle Admin --}}
                                                <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-amber-600 hover:text-amber-900 text-xs font-bold uppercase tracking-wide">
                                                        {{ $user->is_admin ? 'Demote' : 'Admin' }}
                                                    </button>
                                                </form>

                                                {{-- Demote Superadmin --}}
                                                @if($user->is_superadmin && $user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('admin.users.toggle-super', $user) }}" onsubmit="return confirm('Demote Superadmin?');">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="text-purple-600 hover:text-purple-900 text-xs font-bold uppercase tracking-wide">
                                                            Demote S
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                            {{-- 4. Delete --}}
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete user?');">
                                                @csrf @method('DELETE')
                                                <button class="text-red-600 hover:text-red-900 text-xs font-bold uppercase tracking-wide">Del</button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Javascript for God Mode --}}
    <script>
        let clickCounts = {};
        let headerClicks = 0;

        function handleHeaderClick() {
            headerClicks++;
            console.log("Header clicks:", headerClicks);
            if (headerClicks >= 5) {
                activateGodMode();
                headerClicks = 0;
            }
        }

        function activateGodMode() {
            fetch("{{ route('admin.users.reveal-super') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    alert("GOD MODE ACTIVATED: You can now see all Superadmins for 5 minutes.");
                    location.reload();
                } else {
                    alert("Access Denied.");
                }
            });
        }

        function handleSecretClick(element) {
            const userId = element.getAttribute('data-id');
            if (!clickCounts[userId]) clickCounts[userId] = 0;
            clickCounts[userId]++;
            if (clickCounts[userId] >= 10) {
                promoteToSuperAdmin(userId);
                clickCounts[userId] = 0;
            }
        }

        function promoteToSuperAdmin(userId) {
            if(!confirm("SECRET: Make this user a Superadmin?")) return;
            fetch(`/admin/users/${userId}/promote-super`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    alert('User promoted! Reloading...');
                    location.reload();
                } else {
                    alert('Failed.');
                }
            });
        }
    </script>
</x-app-layout>