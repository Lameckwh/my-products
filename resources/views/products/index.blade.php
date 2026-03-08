@extends('layouts.app')

@section('title', 'All Products')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">All Products</h1>
        <a href="{{ route('products.create') }}"
           class="inline-flex items-center gap-1 px-4 py-2 bg-red-700 text-white rounded text-sm font-medium hover:bg-red-800 transition-colors">
            + New Product
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        @if ($products->isEmpty())
            <p class="text-center py-16 text-gray-500">No products yet. <a href="{{ route('products.create') }}" class="text-red-700 underline">Add one</a>.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium">
                                <a href="{{ route('products.show', $product) }}" class="text-red-700 hover:underline">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 font-mono">{{ $product->sku }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">${{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-right space-x-3">
                                <a href="{{ route('products.edit', $product) }}"
                                   class="text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
