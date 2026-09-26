<?php
namespace App\Services;

class FileService
{
    public function moveTempPaper(?string $tempPath): ?string
    {
        if (!$tempPath) return null;

        $source = storage_path('app/public/' . $tempPath);
        if (!file_exists($source)) return null;

        $filename = basename($tempPath);
        $target = 'audience_full_papers/' . $filename;
        $destination = storage_path('app/public/' . $target);

        if (!is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        rename($source, $destination);

        return $target;
    }
}