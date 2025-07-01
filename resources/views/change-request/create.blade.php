<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold mb-6">Create Change Request</h2>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Select Portal</h3>
                        <livewire:portal-selector />
                    </div>

                    <!-- Rest of your change request form -->
                    <form id="changeRequestForm" class="mt-6">
                        <!-- Your other form fields will go here -->
                        
                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                Create Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Listen for portal selection
        Livewire.on('portalSelected', (data) => {
            const portalId = data.portalId;
            // You can store the selected portal ID in a hidden form field
            // or use it however you need in your application
            console.log('Selected portal:', portalId);
        });
    </script>
    @endpush
</x-app-layout> 