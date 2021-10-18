<?php

namespace Codecycler\Satellite\Console;

use DB;
use October\Rain\Network\Http;
use Illuminate\Console\Command;
use System\Models\PluginVersion;
use System\Classes\UpdateManager;
use Codecycler\Satellite\Models\Settings;

class Transmit extends Command
{
    protected $name = 'satellite:transmit';

    protected $description = 'Transmits data to mission control';

    public function handle()
    {
        $server = Settings::get('server');
        $key = Settings::get('project_id');
        $data = $this->prepareData($key);

        Http::post($server . '/api/missioncontrol/receiver', function ($http) use ($key, $data) {
            $http->data($data);
            $http->header('x-Satellite-Id', $key);
            $http->setOption(CURLOPT_SSL_VERIFYHOST, false);
        });
    }

    protected function prepareData($key)
    {
        $projectDetails = UpdateManager::instance()->getProjectDetails();

        return [
            'message' => 'new-check',
            'site_id' => $key,
            'env' => [
                'app_name' => env('APP_NAME'),
                'app_env' => env('APP_ENV'),
                'app_locale' => env('APP_LOCALE'),
                'app_debug' => env('APP_DEBUG'),
                'php_version' => phpversion(),
                'database_version' => DB::select('SELECT VERSION() as version;')[0]->version,
                'backend_timezone' => config('backend.timezone'),
            ],
            'site' => [
                'current_build' => UpdateManager::instance()->getCurrentVersion(),
                'project_name' => $projectDetails->name,
                'project_owner' => $projectDetails->owner,
            ],
            'plugins' => $this->getPlugins(),
        ];
    }

    public function getPlugins()
    {
        $plugins = PluginVersion::all();
        $output = [];

        foreach ($plugins as $plugin) {
            $output[] = [
                'code' => $plugin->code,
                'version' => $plugin->version,
                'created_at' => $plugin->created_at,
            ];
        }

        return $output;
    }
}
