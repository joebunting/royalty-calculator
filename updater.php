<?php
// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

class WP_GitHub_Updater {
    private $slug;
    private $plugin;
    private $github_repo;
    private $access_token;

    public function __construct($config = array()) {
        $this->slug = isset($config['slug']) ? $config['slug'] : '';
        $this->plugin = isset($config['plugin']) ? $config['plugin'] : '';
        $this->github_repo = isset($config['github_repo']) ? $config['github_repo'] : '';
        $this->access_token = isset($config['access_token']) ? $config['access_token'] : '';

        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 10, 3);
    }

    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $remote_version = $this->get_remote_version();
        $current_version = $transient->checked[$this->plugin];

        if (version_compare($current_version, $remote_version, '<')) {
            $obj = new stdClass();
            $obj->slug = $this->slug;
            $obj->new_version = $remote_version;
            $obj->url = "https://github.com/{$this->github_repo}";
            $obj->package = $this->get_remote_package();
            $transient->response[$this->plugin] = $obj;
        }

        return $transient;
    }

    public function plugin_info($false, $action, $response) {
        if ($action !== 'plugin_information') {
            return $false;
        }

        if ($response->slug !== $this->slug) {
            return $false;
        }

        $remote_info = $this->get_remote_info();
        if ($remote_info) {
            $response = new stdClass();
            $response->name = $remote_info->name;
            $response->slug = $this->slug;
            $response->version = $remote_info->tag_name;
            $response->author = $remote_info->author->login;
            $response->homepage = $remote_info->html_url;
            $response->requires = '5.0'; // Adjust as needed
            $response->tested = '6.2'; // Adjust as needed
            $response->downloaded = 0;
            $response->last_updated = $remote_info->published_at;
            $response->sections = array(
                'description' => $remote_info->body,
                'changelog' => $this->get_changelog(),
            );
            $response->download_link = $this->get_remote_package();
        }

        return $response;
    }

    private function get_remote_version() {
        $request = wp_remote_get("https://api.github.com/repos/{$this->github_repo}/releases/latest");
        if (is_wp_error($request)) {
            return false;
        }
        $body = wp_remote_retrieve_body($request);
        $data = json_decode($body);
        return isset($data->tag_name) ? $data->tag_name : false;
    }

    private function get_remote_package() {
        return "https://github.com/{$this->github_repo}/archive/refs/heads/main.zip";
    }

    private function get_remote_info() {
        $request = wp_remote_get("https://api.github.com/repos/{$this->github_repo}/releases/latest");
        if (is_wp_error($request)) {
            return false;
        }
        $body = wp_remote_retrieve_body($request);
        return json_decode($body);
    }

    private function get_changelog() {
        $request = wp_remote_get("https://raw.githubusercontent.com/{$this->github_repo}/main/readme.txt");
        if (is_wp_error($request)) {
            return 'No changelog available.';
        }
        $body = wp_remote_retrieve_body($request);
        $changelog = stristr($body, '== Changelog ==');
        return $changelog ? $changelog : 'No changelog available.';
    }
}
