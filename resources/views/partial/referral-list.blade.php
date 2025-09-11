@foreach($users as $u)
    <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
        <div>
            <div class="font-semibold text-gray-900">{{ $u->name }}</div>
            <div class="text-sm text-gray-600">{{ $u->email }}</div>
            <div class="text-sm text-gray-500">{{ $u->profile->phone ?? '-' }}</div>
        </div>
        <button 
            class="px-3 py-1 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
            onclick="assignReferrer({{ $u->id }})">
            Select
        </button>
    </div>
@endforeach

{{-- <div class="mt-3 flex justify-between items-center">
    {{ $users->links() }}
</div> --}}


