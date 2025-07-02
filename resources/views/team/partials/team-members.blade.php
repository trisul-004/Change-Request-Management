<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
    @foreach($teamMembers as $member)
        <div class="border rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h4 class="font-medium">{{ $member->name }}</h4>
                    <p class="text-sm text-gray-600">{{ $member->email }}</p>
                </div>
                <form action="{{ route('team.remove', $member->supervisor) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to remove this developer from your team?')">
                        Remove
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>
