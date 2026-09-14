<?php

namespace App\Controllers;

use App\Models\UserModel;

class AdminProfile extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');

        $model = new UserModel();
        $data['user'] = $model->find($userId);

        return view('adminprofile', $data);
    }

    public function update()
    {
        $userId = session()->get('user_id');
        $model  = new UserModel();
        $user   = $model->find($userId);

        $name  = $this->request->getPost('name');
        $email = $this->request->getPost('email');

        $img = $this->request->getFile('profile_pic');

        $uploadPath = WRITEPATH . 'uploads/profile/';

        // Upload folder
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Handle optional picture upload
        if ($img && $img->isValid() && !$img->hasMoved()) {

            $newName = $img->getRandomName();

            // Move original file first
            $img->move($uploadPath, $newName);

            // === IMAGE MANIPULATION ===
            // Resize (keep aspect ratio)
            \Config\Services::image()
                ->withFile($uploadPath . $newName)
                ->resize(400, 400, true)
                ->save($uploadPath . $newName, 80);

            // Crop to perfect square (avatar)
            \Config\Services::image()
                ->withFile($uploadPath . $newName)
                ->fit(300, 300, 'center')
                ->save($uploadPath . $newName, 80);

            // Delete old image
            if (!empty($user['profile_pic']) &&
                file_exists($uploadPath . $user['profile_pic'])) {
                unlink($uploadPath . $user['profile_pic']);
            }

            $profilePic = $newName;
        } else {
            $profilePic = $user['profile_pic']; // Keep old one
        }

        // Update admin profile (role stays same)
        $model->update($userId, [
            'name'        => $name,
            'email'       => $email,
            'profile_pic' => $profilePic
        ]);

        return redirect()->to(base_url('admin/profile'))->with('success', 'Profile updated successfully!');
    }

    // Serve profile image from writable/
    public function picture($file = null)
    {
        if (!$file) {
            return redirect()->to(base_url('img/default.jpg'));
        }

        $path = WRITEPATH . 'uploads/profile/' . $file;

        if (!file_exists($path)) {
            return redirect()->to(base_url('img/default.jpg'));
        }

        return $this->response
            ->setHeader('Content-Type', mime_content_type($path))
            ->setBody(file_get_contents($path));
    }
}
