<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadService
{
    public function __construct(
        private string $uploadDir,
        private string $publicPath,
    ) {}

    public function upload(UploadedFile $file): string
    {
        $filename = uniqid('recipe_', true) . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $file->move($this->uploadDir, $filename);

        return $this->publicPath . '/' . $filename;
    }
}
