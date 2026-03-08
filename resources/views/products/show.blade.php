@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-red-700">&larr; Back to Products</a>
            <h1 class="text-2xl font-semibold mt-2">{{ $product->name }}</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="px-6 py-4 flex items-start justify-between">
                    <span class="text-sm font-medium text-gray-500 w-32 shrink-0">SKU</span>
                    <span class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $product->sku }}</span>
                </div>
                <div class="px-6 py-4 flex items-start justify-between">
                    <span class="text-sm font-medium text-gray-500 w-32 shrink-0">Price</span>
                    <span class="text-sm text-gray-900 dark:text-gray-100">${{ number_format($product->price, 2) }}</span>
                </div>
                <div class="px-6 py-4 flex items-start justify-between">
                    <span class="text-sm font-medium text-gray-500 w-32 shrink-0">Description</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">
                        {{ $product->description ?? '—' }}
                    </span>
                </div>
                <div class="px-6 py-4 flex items-start justify-between">
                    <span class="text-sm font-medium text-gray-500 w-32 shrink-0">Created</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $product->created_at->toFormattedDateString() }}</span>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <a href="{{ route('products.edit', $product) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">
                Edit
            </a>
            <form action="{{ route('products.destroy', $product) }}" method="POST"
                  onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 bg-red-700 text-white rounded text-sm font-medium hover:bg-red-800 transition-colors">
                    Delete
                </button>
            </form>
        </div>
    </div>
@endsection
