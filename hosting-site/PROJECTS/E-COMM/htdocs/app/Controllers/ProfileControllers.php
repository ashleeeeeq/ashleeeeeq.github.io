<?php
namespace App\Controllers;

use App\Models\UserModel;

class ProfileControllers extends BaseController
{
    // ADD THIS INDEX METHOD - This is what's called when clicking profile
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to view your profile');
        }

        $userModel = new UserModel();
        $data['user'] = $userModel->find(session()->get('id'));
        
        if (!$data['user']) {
            return redirect()->to('/login')->with('error', 'User not found');
        }

        return view('customer_profile', $data);
    }

    // ADD UPDATE PROFILE METHOD
    public function update()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to update your profile');
        }

        $rules = [
            'fullname' => 'required|min_length[3]|max_length[100]',
            'address' => 'required|min_length[5]|max_length[500]',
            'mobile' => 'required|min_length[11]|max_length[15]|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        
        $updateData = [
            'fullname' => $this->request->getPost('fullname'),
            'address' => $this->request->getPost('address'),
            'mobile' => $this->request->getPost('mobile')
        ];

        $userModel->update(session()->get('id'), $updateData);

        // Update session
        session()->set('fullname', $this->request->getPost('fullname'));

        return redirect()->to('/profile')->with('success', 'Profile updated successfully');
    }

    // YOUR EXISTING UPLOAD PROFILE PICTURE METHOD
    public function uploadProfilePicture()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'profile_picture' => [
                'label' => 'Profile Picture',
                'rules' => 'uploaded[profile_picture]|max_size[profile_picture,2048]|is_image[profile_picture]|mime_in[profile_picture,image/jpg,image/jpeg,image/png]'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', implode(', ', $validation->getErrors()));
        }

        $file = $this->request->getFile('profile_picture');

        if ($file->isValid() && !$file->hasMoved()) {
            // Create upload directory if it doesn't exist - FIXED PATH
            $uploadPath = FCPATH . 'uploads/profiles/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Generate unique filename
            $newName = $file->getRandomName();

            try {
                // Move uploaded file
                $file->move($uploadPath, $newName);

                // Update user profile picture in database
                $userModel = new UserModel();
                $userModel->update(session()->get('id'), [
                    'profile_picture' => $newName
                ]);

                // Update session
                session()->set('profile_picture', $newName);

                return redirect()->back()->with('success', 'Profile picture updated successfully!');

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error uploading image: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'File upload failed');
    }

    public function removeProfilePicture()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find(session()->get('id'));

        if ($user['profile_picture']) {
            // FIXED PATH
            $uploadPath = FCPATH . 'uploads/profiles/';
            $filename = $user['profile_picture'];
            
            // Delete image file
            if (file_exists($uploadPath . $filename)) {
                unlink($uploadPath . $filename);
            }

            // Update database
            $userModel->update(session()->get('id'), ['profile_picture' => null]);
            session()->remove('profile_picture');
            
            return redirect()->back()->with('success', 'Profile picture removed successfully!');
        }

        return redirect()->back()->with('error', 'No profile picture to remove');
    }

    // YOUR EXISTING GET PROFILE PICTURE METHOD
    public function getProfilePicture($filename)
    {
        // Validate filename to prevent directory traversal
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
            return $this->response->setStatusCode(400);
        }

        $filePath = FCPATH . 'uploads/profile_pictures/' . $filename;
        
        if (file_exists($filePath)) {
            $file = new \CodeIgniter\Files\File($filePath);
            
            return $this->response->setContentType($file->getMimeType())
                                 ->setBody(file_get_contents($filePath));
        }
        
        return $this->response->setStatusCode(404);
    }
}