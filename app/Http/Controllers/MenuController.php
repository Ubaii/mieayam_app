<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $menus = Menu::query()
            ->with('category')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->toString().'%'))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'active'))
            ->orderBy('name')
            ->get();
        $categories = Category::query()->orderBy('name')->get();

        return view('menus.index', compact('menus', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('is_active', true)],
            'price' => ['required', 'integer', 'min:1', 'max:100000000'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $attributes['is_active'] = $attributes['is_active'] ?? true;

        Menu::create($attributes);

        return back()->with('status', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu): View
    {
        return view('menus.edit', [
            'menu' => $menu,
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'integer', 'min:1', 'max:100000000'],
            'is_active' => ['required', 'boolean'],
        ]);

        $menu->update($attributes);

        return redirect()->route('menus.index')->with('status', 'Menu berhasil diperbarui.');
    }

    public function toggleStatus(Menu $menu): RedirectResponse
    {
        $isActive = ! $menu->is_active;
        $menu->update(['is_active' => $isActive]);

        return back()->with('status', $isActive
            ? 'Menu berhasil diaktifkan kembali.'
            : 'Menu ditandai kosong sementara.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return back()->with('status', 'Menu berhasil dihapus.');
    }
}
