<?php

namespace App\Repositories\Eloquent;

use App\Data\DynamicFilterData;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\AutoFIlterAndSortService;
use App\Traits\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserRepository implements UserRepositoryInterface
{
    use FileManagerTrait;

    public function __construct(
        private readonly User $model,
    ) {}

    /**
     * get data with dynamic options.
     *
     * @param DynamicFilterData $dynamicFilterData
     **/
    public function getList(DynamicFilterData $dynamicFilterData): array
    {
        return (new AutoFIlterAndSortService($this->model))->dynamicFilter($dynamicFilterData);
    }

    public function store(array $data): Model
    {
        $createData = collect($data);

        DB::beginTransaction();
        $createdModel = $this->model->create($createData->toArray());
        DB::commit();

        return $createdModel;
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function update(Model $model, array $data): Model
    {
        $updateData = Arr::except($data, []);

        DB::beginTransaction();
        $model->update($updateData);
        DB::commit();

        $model->refresh();
        return $model;
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        $filePath = USERS_IMAGE_NAME . '/' . $model->image;
        $this->deleteFile(filePath: $filePath, disk: 'asset');
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();
        $filePath = USERS_IMAGE_NAME . '/' . $model->image;
        $this->deleteFile(filePath: $filePath, disk: 'asset');
        return true;
    }

    public function checkOldPassword(User $user, string $oldPassword): bool
    {
        return Hash::check($oldPassword, $user->password);
    }

    public function updateInfo(User $user, array $data): bool
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $path = $data['image']->store('images', 'public');
            $data['image'] = $path;
        } else {
            unset($data['image']);
        }

        return $user->update($data);
    }
    public function updatePassword(User $user, string $newPassword): bool
    {
        // $user->password = Hash::make($newPassword);
        $user->password = $newPassword;
        return $user->save();
    }

    public function proceedImage($file, string|null $oldFileName = null): bool|string
    {
        $folderPath = USERS_IMAGE_NAME;
        if ($oldFileName) {
            $image =  $this->updateFile(dir: $folderPath . '/', oldFile: $oldFileName, file: $file, disk: 'asset');
        } else {
            $image =  $this->upload(dir: $folderPath . '/', file: $file, disk: 'asset');
        }
        return $image;
    }

    public function proceedImageDelete($file, string|null $oldFileName = null)
    {
        $folderPath = USERS_IMAGE_NAME;
        return  $this->deleteFile(filePath: $folderPath . '/' . $oldFileName, disk: 'asset');
    }
}
