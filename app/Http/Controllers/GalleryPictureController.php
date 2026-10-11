<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryPictureRequest;
use App\Models\GalleryPicture;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class GalleryPictureController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'location' => ['nullable', 'string', 'max:120'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);
        $query = GalleryPicture::query()->with('user:id,name');
        if (! empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }
        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return view('gallery.index', [
            'pictures' => $query->latest('id')->paginate(12)->withQueryString(),
            'locations' => GalleryPicture::query()->distinct()->orderBy('location')->pluck('location'),
            'uploaders' => User::query()->whereIn('id', GalleryPicture::query()->select('user_id'))->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
        ]);
    }

    public function download(GalleryPicture $galleryPicture): RedirectResponse
    {
        $extension = pathinfo($galleryPicture->picture_path, PATHINFO_EXTENSION);
        $filename = 'memory-'.$galleryPicture->id.'.'.$extension;

        return redirect()->away(Storage::disk('r2')->temporaryUrl($galleryPicture->picture_path, now()->addMinutes(10), [
            'ResponseContentDisposition' => 'attachment; filename="'.$filename.'"',
        ]))->header('Cache-Control', 'private, no-store');
    }

    public function store(GalleryPictureRequest $request): RedirectResponse
    {
        $path = null;
        try {
            $path = $request->file('picture')->store('gallery/'.$request->user()->id, 'r2');
            $picture = new GalleryPicture($request->safe()->only(['caption', 'location']));
            $picture->user()->associate($request->user());
            $picture->picture_path = $path;
            $picture->save();
        } catch (Throwable $exception) {
            report($exception);
            $this->deleteFile($path);

            return redirect()->route('gallery.index')->withInput()->withErrors(['picture' => __('We could not save your picture. Please try again.')]);
        }

        return redirect()->route('gallery.index')->with('status', __('A new memory added to the gallery!'));
    }

    public function show(GalleryPicture $galleryPicture): RedirectResponse
    {
        return redirect()->away(Storage::disk('r2')->temporaryUrl($galleryPicture->picture_path, now()->addMinutes(10)))
            ->header('Cache-Control', 'private, no-store');
    }

    public function edit(GalleryPicture $galleryPicture): View
    {
        Gate::authorize('update', $galleryPicture);

        return view('gallery.edit', ['picture' => $galleryPicture]);
    }

    public function update(GalleryPictureRequest $request, GalleryPicture $galleryPicture): RedirectResponse
    {
        $path = null;
        try {
            if ($request->hasFile('picture')) {
                $path = $request->file('picture')->store('gallery/'.$request->user()->id, 'r2');
            }
            $previousPath = DB::transaction(function () use ($request, $galleryPicture, $path): ?string {
                $picture = GalleryPicture::query()->lockForUpdate()->findOrFail($galleryPicture->id);
                $previousPath = $path ? $picture->picture_path : null;
                $picture->fill($request->safe()->only(['caption', 'location']));
                if ($path !== null) {
                    $picture->picture_path = $path;
                }
                $picture->save();

                return $previousPath;
            });
        } catch (Throwable $exception) {
            report($exception);
            $this->deleteFile($path);

            return redirect()->route('gallery.edit', $galleryPicture)->withInput()->withErrors(['picture' => __('We could not save your changes. Please try again.')]);
        }
        $this->deleteFile($previousPath);

        return redirect()->route('gallery.index')->with('status', __('Your memory has been updated.'));
    }

    public function destroy(GalleryPicture $galleryPicture): RedirectResponse
    {
        Gate::authorize('delete', $galleryPicture);
        try {
            Storage::disk('r2')->delete($galleryPicture->picture_path);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('gallery.index')->withErrors(['gallery' => __('We could not delete your picture. Please try again.')]);
        }
        $galleryPicture->delete();

        return redirect()->route('gallery.index')->with('status', __('Picture deleted.'));
    }

    private function deleteFile(?string $path): void
    {
        if ($path === null) {
            return;
        }
        try {
            Storage::disk('r2')->delete($path);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
