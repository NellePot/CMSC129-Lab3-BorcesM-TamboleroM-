@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Inventory Management</h2>
            <p class="text-muted">Tracking supplies for critical operations.</p>
        </div>
        <a href="{{ route('inventory.create') }}" class="btn btn-primary px-4 py-2">
            <i class="fas fa-plus me-2"></i> Add New Item
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <small class="text-muted">Total Items</small>
                <h3 class="fw-bold">{{ $items->total() }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card border-danger">
                <small class="text-muted">Critical Status</small>
                <h3 class="fw-bold text-danger">{{ $criticalCount ?? 0 }} <i class="fas fa-exclamation-triangle ms-2"></i></h3>
                <small class="text-danger">(LOW/OUT)</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <small class="text-muted">Newest Addition</small>
                <h3 class="fw-bold">{{ $items->first()->name ?? 'N/A' }}</h3>
                <small class="text-muted">Added recently</small>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs border-0 mb-3" id="inventoryTabs">
        <li class="nav-item">
            <a class="nav-link active bg-transparent text-white border-0 border-bottom border-primary" href="#">Active Inventory</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-muted border-0" href="{{ route('inventory.trashed') }}">Trash</a>
        </li>
    </ul>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="bg-light bg-opacity-10">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Expiration</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $item->name }}</td>
                            <td><span class="text-muted">{{ $item->category }}</span></td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                @if($item->quantity <= 0)
                                    <span class="badge badge-out-of-stock">OUT OF STOCK</span>
                                @elseif($item->is_low_stock)
                                    <span class="badge badge-low-stock">LOW STOCK</span>
                                @else
                                    <span class="badge badge-in-stock">IN STOCK</span>
                                @endif
                            </td>

                            <td>
                                @if($item->expiration_date)
                                    {{ \Carbon\Carbon::parse($item->expiration_date)->format('M d, Y') }}
                                    @if($item->expiry_status == 'expired')
                                        <span class="text-danger ms-1">⚠️ Expired</span>
                                    @elseif($item->expiry_status == 'warning')
                                        <span class="text-warning ms-1">⚠️ Near Expiry</span>
                                    @endif
                                @else
                                    <span class="text-muted">No Expiration</span>
                                @endif
                            </td>

                            <td class="pe-4 text-end">
                                <a href="{{ route('inventory.edit', $item) }}" class="btn btn-sm btn-outline-info me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
