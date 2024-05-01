<?php

namespace App\Actions\Service;

use App\Actions\UpdatesRecord;
use App\Config\PermissionsConfig;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UpdateService extends UpdatesRecord
{
    public function update(Model $record, array $data): bool
    {
        if (isset($data['image']) && $data['image']) {
            $path = Storage::putFile("public/services/$record->id/logo", $data['image']);
            if ($path) {
                if ($record->logo_path) {
                    Storage::delete($record->logo_path);
                }
                $data['logo_path'] = $path;
            }
        }
        $this->validate($data, $record);

        /** @var Service $record */
        $record->service_categories()->sync($data['service_categories_ids'] ?? []);

        return $record->update($data);
    }

    public function rules(Model $record): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image.*' => ['image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
