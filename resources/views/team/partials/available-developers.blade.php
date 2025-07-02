<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
    @foreach($availableDevelopers as $developer)
        <div class="border rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h4 class="font-medium">{{ $developer->name }}</h4>
                    <p class="text-sm text-gray-600">{{ $developer->email }}</p>
                </div>
                <form action="{{ route('team.assign') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="developer_id" value="{{ $developer->id }}">
                    <button type="submit" class="text-blue-600 hover:text-blue-800">
                        Add to Team
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>
