<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Remotelywork\Installer\Repository\App;
use ZipArchive;

use function base_path;
use function is_array;
use function is_dir;
use function json_decode;

class AddonController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:addon-manage'),
        ];
    }

    public function index(): View
    {
        $addons = $this->getAllAddons();

        return view('backend.addons.index', compact('addons'));
    }

    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'addon' => ['required', 'file', 'mimes:zip'],
        ]);

        // Check zip extension
        if (extension_loaded('zip') === false) {
            notify()->warning(__('Zip extension is not loaded in your server. Please enable zip extension to upload addon.'), 'warning');

            return back();
        }

        $file = $request->file('addon');

        $tempPath = storage_path('app/addons');
        if (! File::isDirectory($tempPath)) {
            File::makeDirectory($tempPath, 0755, true);
        }

        $originalName = $file->getClientOriginalName();
        $tempFilePath = $tempPath.'/'.$originalName;

        $file->move($tempPath, $originalName);

        $zip = new ZipArchive;

        if ($zip->open($tempFilePath) !== true) {
            File::delete($tempFilePath);

            notify()->warning(__('Unable to open addon ZIP file.'), 'warning');

            return back();
        }

        $addonsBasePath = base_path('modules/Addons');
        if (! File::isDirectory($addonsBasePath)) {
            File::makeDirectory($addonsBasePath, 0755, true);
        }

        // Try to detect root directory of the addon
        $rootName = $zip->getNameIndex(0);
        $rootDir = $rootName;

        if ($rootDir && str_ends_with($rootDir, '/')) {
            $rootDir = rtrim($rootDir, '/');
        } else {
            // Fallback to filename if ZIP has no top directory
            $rootDir = pathinfo($originalName, PATHINFO_FILENAME);
        }

        $targetPath = $addonsBasePath.'/'.$rootDir;

        if (File::isDirectory($targetPath)) {
            $zip->close();
            File::delete($tempFilePath);

            notify()->warning(__('An addon with this folder already exists.'), 'warning');

            return back();
        }

        $zip->extractTo($addonsBasePath);
        $zip->close();
        File::delete($tempFilePath);

        $pluginJsonPath = $targetPath.'/plugin.json';

        if (! File::exists($pluginJsonPath)) {
            // Cleanup extracted folder if it does not look like a valid addon
            File::deleteDirectory($targetPath);

            notify()->warning(__('Uploaded addon is invalid. Missing plugin.json file.'), 'warning');

            return back();
        }

        $data = json_decode(File::get($pluginJsonPath), true) ?: [];

        // Ensure some required keys exist
        if (! isset($data['name']) || ! isset($data['provider'])) {
            File::deleteDirectory($targetPath);

            notify()->warning(__('Uploaded addon is invalid. Required fields are missing in plugin.json.'), 'warning');

            return back();
        }

        // Set active and license_key
        $data['active'] = (bool) ($data['active'] ?? false);
        if (! array_key_exists('license_key', $data)) {
            $data['license_key'] = null;
        }

        File::put($pluginJsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        notify()->success(__('Addon uploaded successfully.'), 'success');

        return redirect()->route('admin.addons.index');
    }

    public function activate(Request $request, string $slug): RedirectResponse
    {
        $licenseKey = $request->get('license_key');

        try {
            $this->updateAddonStatus($slug, true, $licenseKey);
            Artisan::call('optimize:clear');
            notify()->success(__('Addon activated successfully.'), 'success');
        } catch (\Throwable $th) {
            notify()->error($th->getMessage(), 'error');
        }

        return back();
    }

    public function deactivate(string $slug): RedirectResponse
    {
        try {
            $this->updateAddonStatus($slug, false, null);
            Artisan::call('optimize:clear');
            notify()->success(__('Addon deactivated successfully.'), 'success');
        } catch (\Throwable $th) {
            notify()->error($th->getMessage(), 'error');
        }

        return back();
    }

    public function destroy(string $slug): RedirectResponse
    {
        $addonsPath = base_path('modules/Addons');

        if (! is_dir($addonsPath)) {
            abort(404);
        }

        $directories = File::directories($addonsPath);
        $deleted = false;

        foreach ($directories as $addonPath) {
            $pluginJsonPath = $addonPath.'/plugin.json';

            if (! File::exists($pluginJsonPath)) {
                continue;
            }

            $data = json_decode(File::get($pluginJsonPath), true) ?: [];

            if (! is_array($data)) {
                continue;
            }

            $directory = basename($addonPath);
            $currentSlug = $data['slug'] ?? $directory;

            if ($currentSlug !== $slug) {
                continue;
            }

            File::deleteDirectory($addonPath);
            $deleted = true;

            break;
        }

        if (! $deleted) {
            abort(404);
        }

        notify()->success(__('Addon deleted successfully.'), 'success');

        return back();
    }

    private function getAllAddons(): Collection
    {
        $addonsPath = base_path('modules/Addons');

        if (! is_dir($addonsPath)) {
            return collect();
        }

        return collect(File::directories($addonsPath))
            ->map(function (string $addonPath) {
                $pluginJsonPath = $addonPath.'/plugin.json';

                if (! File::exists($pluginJsonPath)) {
                    return null;
                }

                $data = json_decode(File::get($pluginJsonPath), true) ?: [];

                if (! is_array($data)) {
                    return null;
                }

                $directory = basename($addonPath);

                $data['directory'] = $directory;
                $data['slug'] = $data['slug'] ?? $directory;
                $data['active'] = (bool) ($data['active'] ?? false);
                $data['license_key'] = $data['license_key'] ?? null;

                return $data;
            })
            ->filter()
            ->values();
    }

    private function updateAddonStatus(string $slug, bool $status, ?string $licenseKey): void
    {
        $addonsPath = base_path('modules/Addons');

        if (! is_dir($addonsPath)) {
            abort(404);
        }

        $directories = File::directories($addonsPath);
        $updated = false;

        if ($status === false) {
            App::forgetLicenseCache($slug);
        }

        if ($status === true && $licenseKey === null) {
            throw new Exception('License key is required to activate the addon.');
        }

        if ($status === true && $licenseKey !== null) {
            $validated = App::validateAddonLicense($slug, $licenseKey);

            if (! $validated) {
                throw new Exception('License key is invalid.');
            }
        }

        foreach ($directories as $addonPath) {
            $pluginJsonPath = $addonPath.'/plugin.json';

            if (! File::exists($pluginJsonPath)) {
                continue;
            }

            $data = json_decode(File::get($pluginJsonPath), true) ?: [];

            if (! is_array($data)) {
                continue;
            }

            $currentSlug = $data['slug'];

            if ($currentSlug !== $slug) {
                continue;
            }

            $data['slug'] = $currentSlug;
            $data['active'] = $status;

            if ($licenseKey !== null) {
                $data['license_key'] = $licenseKey;
            }

            File::put($pluginJsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            $updated = true;

            break;
        }

        if (! $updated) {
            throw new Exception('Something went wrong. Please try again.');
        }
    }
}
