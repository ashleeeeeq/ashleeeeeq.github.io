<?php

use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;

it('accepts supported image and pdf uploads', function () {
    $service = new FileUploadService();

    $image = UploadedFile::fake()->image('photo.jpg');
    $pdf = UploadedFile::fake()->create('report.pdf', 200, 'application/pdf');

    expect($service->validate($image))->toMatchArray(['valid' => true]);
    expect($service->validate($pdf))->toMatchArray(['valid' => true]);
});

it('rejects unsupported file types and oversized files', function () {
    $service = new FileUploadService();

    $textFile = UploadedFile::fake()->create('notes.txt', 10, 'text/plain');
    $largePdf = UploadedFile::fake()->create('large.pdf', 6000, 'application/pdf');

    expect($service->validate($textFile)['valid'])->toBeFalse();
    expect($service->validate($largePdf)['valid'])->toBeFalse();
});

it('formats file sizes for display', function () {
    expect(FileUploadService::formatFileSize(1024))->toBe('1 KB');
    expect(FileUploadService::formatFileSize(5 * 1024 * 1024))->toBe('5 MB');
});
