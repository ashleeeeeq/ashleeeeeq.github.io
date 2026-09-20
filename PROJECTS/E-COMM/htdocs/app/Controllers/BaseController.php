<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'session'];

    /**
     * Session instance
     */
    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') ? true : false;
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin(): bool
    {
        return $this->session->get('role') === 'admin';
    }

    /**
     * Check if user is customer
     */
    protected function isCustomer(): bool
    {
        return $this->session->get('role') === 'customer';
    }

    /**
     * Redirect if not logged in
     */
    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Please login first');
        }
        return null;
    }

    /**
     * Redirect if not admin
     */
    protected function requireAdmin()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        if (!$this->isAdmin()) {
            return redirect()->to('/')->with('error', 'Access denied. Admin privileges required.');
        }
        return null;
    }

    /**
     * Redirect if not customer
     */
    protected function requireCustomer()
    {
        $redirect = $this->requireLogin();
        if ($redirect) return $redirect;

        if (!$this->isCustomer()) {
            return redirect()->to('/admin/dashboard')->with('error', 'Access denied. Customer area only.');
        }
        return null;
    }

    /**
     * Get current user ID
     */
    protected function getCurrentUserId()
    {
        return $this->session->get('id');
    }

    /**
     * Get current user role
     */
    protected function getCurrentUserRole()
    {
        return $this->session->get('role');
    }

    /**
     * Get current user fullname
     */
    protected function getCurrentUserFullname()
    {
        return $this->session->get('fullname');
    }
}