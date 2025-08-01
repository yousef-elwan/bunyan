<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuthRepository implements AuthRepositoryInterface
{

    public function checkOldPassword(User $user, string $oldPassword): bool
    {
        return Hash::check($oldPassword, $user->password);
    }

    public function updateInfo(User $user, array $data): bool
    {
        try {

            if (isset($data['image']) && $data['image'] instanceof UploadedFile)
            {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }

                $path = $data['image']->store('images', 'public');
                $data['image'] = $path;
            }
            else
            {
                unset($data['image']);
            }

            return $user->update($data);

        } catch (\Exception $e) {
            Log::error('Error updating user info: ' . $e->getMessage());
            return false;
        }
    }
    public function updatePassword(User $user, string $newPassword): bool
    {
        $user->password = Hash::make($newPassword);
        return $user->save();
    }
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }
}
