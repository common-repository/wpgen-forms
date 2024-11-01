<?php

namespace WPGenForms;

use WPGenForms\Includes\Actions as WPGenFormsActions;
use WPGenForms\Includes\Filters as WPGenFormsFilters;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Plugin
{
    public function __construct()
    {
        $this->loadComposerDependencies();
        $this->loadLibraries();

        $this->loadActions();
        $this->loadFilters();
    }

    public function loadComposerDependencies()
    {
        require_once WPGEN_FORMS_PATH . 'vendor/autoload.php';
    }

    public function loadLibraries()
    {
        return 0;
    }

    public function loadActions()
    {
        new WPGenFormsActions();
    }

    public function loadFilters()
    {
        new WPGenFormsFilters();
    }
}

new Plugin();