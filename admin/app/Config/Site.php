<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Shared settings between the CI4 admin and the core-PHP front end.
 * Override in .env:  site.siteUrl = 'http://localhost/events/'
 */
class Site extends BaseConfig
{
    public string $siteName = 'BrightPath';

    /** Public URL of the core-PHP website (with trailing slash). */
    public string $siteUrl = 'http://localhost/events/';

    /** Absolute path to the shared /uploads folder (empty = auto-detect ../uploads). */
    public string $uploadPath = '';

    public function __construct()
    {
        parent::__construct();

        if ($this->uploadPath === '') {
            $this->uploadPath = dirname(rtrim(ROOTPATH, '/\\')) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        } else {
            $this->uploadPath = rtrim($this->uploadPath, '/\\') . DIRECTORY_SEPARATOR;
        }

        $this->siteUrl = rtrim($this->siteUrl, '/') . '/';
    }
}
