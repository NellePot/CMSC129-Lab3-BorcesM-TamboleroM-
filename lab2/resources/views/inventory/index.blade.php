@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary-blue text-white">
                <h4 class="mb-0">
                    <i class="fas fa-boxes"></i> Emergency Supplies
                </h4>
            </div>

            <div class="card-body">

                <!-- Search -->
                <form method="GET" action="{{ route('inventory.index') }}" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control"
                               placeholder="Search supplies..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-6">
                        <button class="btn btn-blue">
                            <i class="fas fa-search"></i> Search
                        </button>

                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </a>

                        <a href="{{ route('inventory.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Supply
                        </a>
                    </div>
                </form>

                <!-- TABLE -->
                @if($items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Min Stock</th>
                                <th>Status</th>
                                <th>Expiration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($items as $item)
                            <tr class="{{ $item->is_low_stock ? 'table-danger' : '' }}">

                                <!-- Name -->
                                <td>{{ $item->name }}</td>

                                <!-- Quantity -->
                                <td>{{ $item->quantity }}</td>

                                <!-- Min Stock -->
                                <td>{{ $item->minimum_stock }}</td>

                                <!-- Stock Status -->
                                <td>
                                    @if($item->is_low_stock)
                                        <span class="badge bg-danger">Low Stock</span>
                                    @else
                                        <span class="badge bg-success">OK</span>
                                    @endif
                                </td>

                                <!-- Expiration -->
                                <td>
                                    @if($item->expiry_status == 'expired')
                                        <span class="badge bg-danger">Expired</span>
                                    @elseif($item->expiry_status == 'warning')
                                        <span class="badge bg-warning text-dark">Near Expiry</span>
                                    @else
                                        <span class="badge bg-success">Safe</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('inventory.show', $item) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('inventory.edit', $item) }}"
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('inventory.destroy', $item) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this item?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $items->links() }}
                </div>

                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No supplies found.
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
