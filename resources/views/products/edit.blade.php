@extends('layouts.app')

@section('title', 'Edit – ' . $product->name)

@section('content')
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('products.show', $product) }}" class="text-sm text-gray-500 hover:text-red-700">&larr; Back to Product</a>
            <h1 class="text-2xl font-semibold mt-2">Edit Product</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form action="{{ route('products.update', $product) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1">Name <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                               class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('name') border-red-400 @else border-gray-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-medium mb-1">SKU <span class="text-red-600">*</span></label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}"
                               class="w-full border rounded px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-500 @error('sku') border-red-400 @else border-gray-300 @enderror">
                        @error('sku')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium mb-1">Price ($) <span class="text-red-600">*</span></label>
                        <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" min="0" step="0.01"
                               class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('price') border-red-400 @else border-gray-300 @enderror">
                        @error('price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium mb-1">Description</label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('description') border-red-400 @else border-gray-300 @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                            class="px-5 py-2 bg-red-700 text-white rounded text-sm font-medium hover:bg-red-800 transition-colors">
                        Save Changes
                    </button>
                    <a href="{{ route('products.show', $product) }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
