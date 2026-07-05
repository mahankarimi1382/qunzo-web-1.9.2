<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ImageUpload
{
    public function imageUploadTrait($query, $old = null, $folderPath = 'images', $allowExt = ['jpeg', 'png', 'jpg', 'gif', 'svg', 'webp', 'pdf', 'doc', 'docx']): string
    {
        if (config('app.demo')) {
            return $old ?? '';
        }

        $ext = strtolower($query->getClientOriginalExtension());

        abort_if($query->getSize() > 5100000, 403, __('Max file size:5MB '));
        abort_if(! in_array($ext, $allowExt), 403, __('Only allow : jpeg, png, jpg, gif, svg'));

        if ($old != null) {
            self::fileDelete($old);
        }

        $image_name = Str::random(20);
        $image_full_name = $image_name.'.'.$ext;
        $upload_path = 'global/uploads/'.$folderPath.'/';
        $destination = public_path($upload_path);
        $query->move($destination, $image_full_name);

        return $upload_path.$image_full_name;
    }

    protected function fileDelete($path)
    {
        $path = public_path($path);

        if (file_exists($path) && ! is_dir($path)) {
            unlink($path);
        }
    }

    private function deleteDirectory($dirPath)
    {
        if (! is_dir($dirPath)) {
            return;
        }

        $files = array_diff(scandir($dirPath), ['.', '..']);

        foreach ($files as $file) {
            $filePath = $dirPath.DIRECTORY_SEPARATOR.$file;
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }

        rmdir($dirPath);
    }

    public function fileUpload($query, $old = null)
    {
        if (config('app.demo')) {
            return $old;
        }

        $file = $query;
        $file_name = $file->getClientOriginalName();
        $file->move('public/uploads/files/', $file_name);

        if ($old !== null) {
            self::fileDelete($old);
        }

        return str_replace('public/', '', 'public/uploads/files/'.$file_name);
    }
}
