<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount('menus')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->toString().'%'))
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Category::create($attributes);

        return back()->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($category)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update($attributes);

        return redirect()->route('categories.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->menus()->exists()) {
            return back()->withErrors(['category' => 'Kategori masih digunakan menu. Pindahkan atau hapus menu terkait terlebih dahulu.']);
        }

        try {
            $category->delete();
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors(['category' => 'Kategori tidak dapat dihapus karena masih digunakan data lain.']);
        }

        return back()->with('status', 'Kategori berhasil dihapus.');
    }
}
