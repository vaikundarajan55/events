<?php

namespace App\Controllers;

use App\Models\AdminModel;

class Auth extends BaseController
{
    private const MAX_ATTEMPTS = 5;
    private const LOCK_SECONDS = 300;

    public function login()
    {
        return view('auth/login');
    }

    public function attempt()
    {
        $session = session();

        // Simple brute-force protection (per session)
        $lockedUntil = (int) $session->get('login_locked_until');
        if ($lockedUntil > time()) {
            $mins = (int) ceil(($lockedUntil - time()) / 60);
            return redirect()->back()->with('error', "Too many attempts. Try again in {$mins} minute(s).");
        }

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Enter a valid email and a password of 6+ characters.');
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $pass  = (string) $this->request->getPost('password');
        $admin = (new AdminModel())->where('email', $email)->first();

        if (! $admin || ! password_verify($pass, $admin['password'])) {
            $tries = (int) $session->get('login_tries') + 1;
            $session->set('login_tries', $tries);
            if ($tries >= self::MAX_ATTEMPTS) {
                $session->set('login_locked_until', time() + self::LOCK_SECONDS);
                $session->set('login_tries', 0);
            }
            return redirect()->back()->withInput()->with('error', 'Email or password is incorrect.');
        }

        $session->regenerate(true);
        $session->remove(['login_tries', 'login_locked_until']);
        $session->set([
            'admin_logged_in' => true,
            'admin_id'        => $admin['id'],
            'admin_name'      => $admin['name'],
            'admin_email'     => $admin['email'],
        ]);

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $admin['name'] . '!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}
