<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use Illuminate\Http\UploadedFile;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class ValidatedFileDTO extends ValidatedDTO
{
    public UploadedFile $file;

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }

    protected function rules(): array
    {
        return [
            'file' => 'required|file|mimes:jpg,jpeg,png',
        ];
    }
}
