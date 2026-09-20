<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Pager extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Templates
     * --------------------------------------------------------------------------
     *
     * Pagination links are rendered out using views to configure their
     * appearance. This array contains aliases and the view names to
     * use when rendering the links.
     *
     * Within each view, the Pager object will be available as $pager,
     * and the desired group as $pagerGroup;
     *
     * @var array<string, string>
     */
    public $templates = [
        'default'          => 'CodeIgniter\Pager\Views\default',
        'simple'           => 'CodeIgniter\Pager\Views\simple',
        'full-bootstrap'   => 'App\Views\pagers\bootstrap_full',
        'bootstrap_full' => 'App\Views\pagers\bootstrap_full',
    ];


    /**
     * --------------------------------------------------------------------------
     * Items Per Page
     * --------------------------------------------------------------------------
     *
     * The default number of results shown in a single page.
     */
    public int $perPage = 20;
}
