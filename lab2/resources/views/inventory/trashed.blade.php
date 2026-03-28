@extends('layouts.app')

@section('title', 'Deleted Supplies')

@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">
                    <i class="fas fa-trash"></i> Deleted Supplies
                </h4>
            </div>

            <div class="card-body">

                @if($items->count() > 0)

                    <!-- INFO ALERT -->
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        These supplies have been removed. You may restore or permanently delete them.
                    </div>

                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Deleted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($items as $item)
                                <tr>

                                    <!-- NAME -->
                                    <td>{{ $item->name }}</td>

                                    <!-- CATEGORY -->
                                    <td>{{ $item->category ?? 'N/A' }}</td>

                                    <!-- QUANTITY -->
                                    <td>{{ $item->quantity }}</td>

                                    <!-- DELETED DATE -->
                                    <td>
                                        {{ $item->deleted_at->format('M d, Y h:i A') }}
                                    </td>

                                    <!-- ACTIONS -->
                                    <td>
                                        <!-- RESTORE -->
                                        <form action="{{ route('inventory.restore', $item->id) }}"
                                              method="POST"
                                              style="display:inline;">
                                            @csrf
                                            @method('PATCH')

                                            <button class="btn btn-sm btn-success">
                                                <i class="fas fa-undo"></i> Restore
                                            </button>
                                        </form>

                                        <!-- DELETE FOREVER -->
                                        <form action="{{ route('inventory.forceDelete', $item->id) }}"
                                              method="POST"
                                              style="display:inline;"
                                              onsubmit="return confirm('Permanently delete this supply? This cannot be undone!');">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i> Delete Forever
                                            </button>
                                        </form>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION -->
                    <div class="d-flex justify-content-center">
                        {{ $items->links() }}
                    </div>

                @else

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Trash is empty. No deleted supplies found.
                    </div>

                @endif

                <!-- BACK BUTTON -->
                <div class="mt-3">
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Inventory
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
