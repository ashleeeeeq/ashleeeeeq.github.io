<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $id = session()->get('user_id');

        $user = $userModel->find($id);

        return view('profile', ['user' => $user]);
    }


    public function updateProfile()
    {
        $userModel = new UserModel();
        $id = session()->get('user_id');

        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        $img = $this->request->getFile('profile_pic');

        if ($img && $img->isValid() && !$img->hasMoved()) {

            // Upload folder: writable/uploads/profile/
            $uploadPath = WRITEPATH . 'uploads/profile/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // New randomized filename
            $newName = $img->getRandomName();
            $fullPath = $uploadPath . $newName;

            // --- IMAGE MANIPULATION ---
            // Resize → Crop → Compress
            $imageService = \Config\Services::image();

            $imageService->withFile($img)
                ->resize(500, 500, true, 'height')   // resize maintaining aspect ratio
                ->crop(500, 500)                    // crop to perfect square
                ->save($fullPath, 80);              // save with 80% quality

            // Store new filename in DB
            $data['profile_pic'] = $newName;

            // Delete old photo
            $old = $userModel->find($id)['profile_pic'] ?? null;
            if ($old && file_exists($uploadPath . $old)) {
                unlink($uploadPath . $old);
            }
        }

        $userModel->update($id, $data);

        return redirect()->to('/profile')->with('success', 'Profile updated successfully!');
    }


    // Serve image from writable/uploads/profile/
    public function picture($filename = null)
    {
        // no filename? → show default
        if (!$filename) {
            return redirect()->to(base_url('img/default.jpg'));
        }

        $path = WRITEPATH . 'uploads/profile/' . $filename;

        // file missing? → show default
        if (!file_exists($path)) {
            return redirect()->to(base_url('img/default.jpg'));
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setBody(file_get_contents($path));
    }
}