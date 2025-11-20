<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            {{-- THE TRIGGER FOR GOD MODE (5 Clicks) --}}
            <h2 id="user-management-header" 
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight cursor-pointer select-none"
                onclick="handleHeaderClick()">
                {{ __('User Management') }}
                
                {{-- Visual indicator if God Mode is active --}}
                @if(isset($isRevealActive) && $isRevealActive)
                    <span class="text-purple-500 text-sm ml-2 font-bold">(GOD MODE ACTIVE)</span>
                @endif
            </h2>

            {{-- Manual Add Button --}}
            <a href="{{ route('admin.users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New User
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
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{-- SECRET CLICK TRIGGER (10 Clicks to Promote) --}}
                                    <span class="cursor-pointer select-none user-name-trigger" 
                                          data-id="{{ $user->id }}" 
                                          onclick="handleSecretClick(this)">
                                        {{ $user->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{-- 1. Superadmin Badge --}}
                                    @if($user->is_superadmin) 
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 border border-purple-200">Superadmin</span> 
                                    @endif

                                    {{-- 2. Regular Badges --}}
                                    @if($user->is_admin) <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Admin</span> @endif
                                    @if($user->is_artist) <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Artist</span> @endif
                                    @if(!$user->is_admin && !$user->is_artist && !$user->is_superadmin) <span class="text-gray-500 text-sm">User</span> @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    
                                    {{-- 1. Toggle Artist --}}
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            {{ $user->is_artist ? 'Remove Artist' : 'Make Artist' }}
                                        </button>
                                    </form>

                                    {{-- SUPERADMIN ONLY ACTIONS --}}
                                    @if(auth()->user()->is_superadmin)
                                        
                                        {{-- 2. Toggle Admin --}}
                                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-amber-600 hover:text-amber-900 mr-3">
                                                {{ $user->is_admin ? 'Demote Admin' : 'Promote Admin' }}
                                            </button>
                                        </form>

                                        {{-- 3. Demote Superadmin (Only visible if target is Superadmin AND not self) --}}
                                        @if($user->is_superadmin && $user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle-super', $user) }}" class="inline-block" onsubmit="return confirm('Demote this Superadmin? They will become invisible again unless promoted.');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-purple-600 hover:text-purple-900 mr-3 font-bold">
                                                    Demote Super
                                                </button>
                                            </form>
                                        @endif

                                    @endif

                                    {{-- 4. Delete --}}
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT FOR SECRET CLICKS --}}
    <script>
        let clickCounts = {};

        // Header Click Counter (for God Mode)
        let headerClicks = 0;

        function handleHeaderClick() {
            headerClicks++;
            console.log("Header clicks:", headerClicks);

            if (headerClicks >= 5) {
                activateGodMode();
                headerClicks = 0; // Reset counter
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
                    location.reload(); // Reload to show the hidden users
                } else {
                    alert("Access Denied: You are not a Superadmin.");
                }
            });
        }

        // Name Click Counter (for Promoting Users)
        function handleSecretClick(element) {
            const userId = element.getAttribute('data-id');
            
            // Initialize count for this user if not exists
            if (!clickCounts[userId]) {
                clickCounts[userId] = 0;
            }

            clickCounts[userId]++;
            console.log(`Clicks for user ${userId}: ${clickCounts[userId]}`);

            if (clickCounts[userId] >= 10) {
                promoteToSuperAdmin(userId);
                clickCounts[userId] = 0; // Reset
            }
        }

        function promoteToSuperAdmin(userId) {
            if(!confirm("SECRET UNLOCKED: Make this user a Superadmin? They will disappear from this list.")) return;

            fetch(`/admin/users/${userId}/promote-super`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    alert('User promoted to Superadmin! Reloading...');
                    location.reload();
                } else {
                    alert('Failed. Are you sure YOU are a superadmin?');
                }
            });
        }
    </script>
</x-app-layout>