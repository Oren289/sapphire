<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageUpdateRequest;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProfileImageController extends Controller
{
    public function update(ProfileImageUpdateRequest $request, string $type): RedirectResponse
    {
        $column = $type.'_picture_path';
        $path = null;

        try {
            $path = $request->file($type.'_image')->store('users/'.$request->user()->id.'/'.$type, 'r2');

            $previousPath = DB::transaction(function () use ($request, $column, $path): ?string {
                $user = User::query()->lockForUpdate()->findOrFail($request->user()->id);
                $previousPath = $user->{$column};
                $user->{$column} = $path;
                $user->save();

                return $previousPath;
            });
        } catch (Throwable $exception) {
            report($exception);
            $this->deleteImage($path);

            return redirect()->route('profile.edit')->withErrors([
                $type.'_image' => __('We could not save your picture. Please try again.'),
            ]);
        }

        $this->deleteImage($previousPath);

        return redirect()->route('profile.edit')->with('status', $type.'-picture-updated');
    }

    public function show(Request $request, string $type): RedirectResponse
    {
        $path = $request->user()->{$type.'_picture_path'};
        abort_if($path === null, 404);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('r2');

        return redirect()->away($disk->temporaryUrl($path, now()->addMinutes(10)))
            ->header('Cache-Control', 'private, no-store');
    }

    private function deleteImage(?string $path): void
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
