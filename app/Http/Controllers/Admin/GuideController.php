<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    public function index()
    {
        return view('admin.guides.index', ['guides' => Guide::withCount('tours')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.guides.form', ['guide' => new Guide(['experience_years' => 1])]);
    }

    public function store(Request $request)
    {
        $d = $this->data($request);
        $d['slug'] = Str::slug($d['name']) . '-' . Str::lower(Str::random(4));
        Guide::create($d);

        return redirect()->route('admin.guides.index')->with('status', 'Гид добавлен.');
    }

    public function edit(Guide $guide)
    {
        return view('admin.guides.form', ['guide' => $guide]);
    }

    public function update(Request $request, Guide $guide)
    {
        $guide->update($this->data($request));

        return back()->with('status', 'Профиль гида сохранён.');
    }

    public function destroy(Guide $guide)
    {
        if ($guide->tours()->exists()) {
            return back()->withErrors(['guide' => 'Нельзя удалить гида, у которого есть туры. Сначала переназначьте или архивируйте туры.']);
        }
        $guide->delete();

        return redirect()->route('admin.guides.index')->with('status', 'Гид удалён.');
    }

    private function data(Request $request): array
    {
        $d = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'country' => ['required', 'in:' . implode(',', array_keys(config('via.countries')))],
            'city' => ['required', 'string', 'max:80'],
            'specialization' => ['required', 'string', 'max:160'],
            'languages' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:600'],
            'story' => ['nullable', 'string', 'max:4000'],
            'quote' => ['nullable', 'string', 'max:200'],
            'contact_information' => ['nullable', 'string', 'max:160'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'photo_file' => ['nullable', 'image', 'max:4096'],
        ]);
        if ($request->hasFile('photo_file')) {
            $name = Str::random(12) . '.' . $request->file('photo_file')->extension();
            $request->file('photo_file')->move(public_path('uploads'), $name);
            $d['photo'] = 'uploads/' . $name;
        }
        unset($d['photo_file']);

        return $d;
    }
}
