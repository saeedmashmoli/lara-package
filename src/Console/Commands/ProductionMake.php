<?php

namespace ITB\LaraPackage\Console\Commands;

use Composer\Config;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;

/**
 * Class ProductionMake
 * @package ITB\LaraPackage\Console\Commands
 */
class ProductionMake extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'z-production:make';

    private $watch = [
        'php' => [
            'app',
            'config',
            'database',
            'routes',
        ],
        'quasar' => [
            'src'
        ],
        'mix' => [
            'resources'
        ]
    ];

    /**
     * List of files and directories to exclude
     * @var array|string[]
     */
    private array $exclude = [
        '.',
        '..',
        '.git',
        '.idea',
        '.quasar',
        '.vscode',
        'dist',
        'node_modules',
        'public',
        'py',
        'storage',
        'tmp',
        //'vendor',
    ];

    private ?int $last_run = null;
    private ?array $config = null;
    private ?int $now = null;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create a backup and update production server with the latest version and make plugin zip file.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->initLastRun();
        $this->initConfig();
        $this->initNow();
    }

    /**
     * @return void
     */
    public function handle()
    {
        if (!Config::get('app.debug')) {
            $this->raiseError('This tools runs only on development machine!');
        }


        //$this->syncWithIntermediate();
        //$this->buildFront();
        $this->makeDecodeZipFile();
        //$this->uploadDecodeZipFileToEncoderServer();
        //$this->makeEncodeZipFile();
        //$this->downloadEncodeZipFile();
        //$this->makeUpdateZipFile();
        //$this->uploadUpdateZipFile2DestinationServer();
        //$this->makeBackupZipFileOnDestinationServer();
        //$this->updateDestinationServer();
        //$this->updatePermissions();
        //$this->getVersion();
        //$this->completeJob();
    }

    /**
     * @return void
     */
    protected function syncWithIntermediate()
    {
        $intermediate_name = Config::get('production.intermediate.name');
        $intermediate_path = Config::get('production.intermediate.path');
        $intermediate_user = Config::get('production.intermediate.user');

        $exclude = '--exclude \'' . join('\' --exclude \'', $this->exclude) . '\'';
        $commands = ['rsync -avPzL --delete ' . $exclude .
            ' --chown=' . $intermediate_user . ':' . $intermediate_user . ' ' .
            base_path() . '/ ' . $intermediate_name . ':' . $intermediate_path];
        $this->process($commands);

        $this->successMessage('syncWithIntermediate');
    }

    /**
     * @return void
     */
    protected function buildFront()
    {
        $front = $this->config['front'];
        $intermediate_name = $this->config['intermediate']['name'];
        $intermediate_user = $this->config['intermediate']['user'];
        $intermediate_path = $this->config['intermediate']['path'];
        $commands = [];
        
        switch ($front) {
            case 'quasar':
                $commands = [
                    'cd ' . $intermediate_path,
                    'rm -rf dist',
                    'quasar build',
                ];
                break;
            case 'mix':
                $this->raiseError('mix not supported yet');
                break;
            default:
                $this->raiseError($front . ' not supported yet');
        }

        $this->remoteProcess($intermediate_name, $intermediate_user, $commands);

        $this->successMessage('buildFront');
    }

    protected function makeDecodeZipFile()
    {
        $intermediate_name = $this->config['intermediate']['name'];
        $intermediate_user = $this->config['intermediate']['user'];
        $intermediate_path = $this->config['intermediate']['path'];
        $now = $this->now;

        $commands = [
            'cd ' . $intermediate_path,
            'mkdir -p tmp/' . $now . '-decode',
        ];

        $this->remoteProcess($intermediate_name, $intermediate_user, $commands);

        $this->successMessage('makeDecodeZipFile');
    }

    protected function uploadDecodeZipFileToEncoderServer()
    {
        // todo
        $this->line('uploadDecodeZipFileToEncoderServer...');
    }

    protected function makeEncodeZipFile()
    {
        // todo
        $this->line('makeEncodeZipFile...');
    }

    protected function downloadEncodeZipFile()
    {
        // todo
        $this->line('downloadEncodeZipFile...');
    }

    protected function makeUpdateZipFile()
    {
        // todo
        $this->line('makeUpdateZipFile...');
    }

    protected function uploadUpdateZipFile2DestinationServer()
    {
        // todo
        $this->line('uploadUpdateZipFile2DestinationServer...');
    }

    protected function makeBackupZipFileOnDestinationServer()
    {
        // todo
        $this->line('makeBackupZipFileOnDestinationServer...');
    }

    protected function updateDestinationServer()
    {
        // todo
        $this->line('updateDestinationServer...');
    }

    protected function updatePermissions()
    {
        // todo
        $this->line('updatePermissions...');
    }

    /**
     * @return void
     */
    protected function getVersion()
    {
        $destination_server = Config::get('production.destination.name');
        $destination_path = Config::get('production.destination.path');
        $destination_user = Config::get('production.destination.user');

        $commands = [
            'cd ' . $destination_path . '/laravel',
            'php artisan z-version:get',
        ];

        $process = $this->remoteProcess($destination_server, $destination_user, $commands);
        $version = $process->getOutput();
        $this->call('z-version:update', ['version' => $version]);
        $this->successMessage('getVersion');
    }

    /**
     * @return void
     */
    protected function completeJob()
    {
        $timestamp = 1600000000; // $timestamp = now()->timestamp;

        $commands = [
            'cd ' . base_path(),
            'echo "' . $timestamp . '" > ' . 'storage/logs/laravel-production.log',
        ];

        $this->process($commands);
        $this->successMessage('completeJob');
    }

    /**
     * @param array $commands
     * @return Process
     */
    private function process(array $commands): Process
    {
        $timeout = Config::get('production.timeout', 60);
        $commands = join(' && ', $commands);
        $process = Process::fromShellCommandline($commands, null, null, null, $timeout);
        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput();
            $this->raiseError($error);
        }

        return $process;
    }

    /**
     * @param string $server
     * @param string $user
     * @param array $commands
     * @return Process
     */
    private function remoteProcess(string $server, string $user, array $commands): Process
    {
        $commands = array_merge(['sudo -su ' . $user], $commands);
        $commands = "\n" . join("\n", $commands) . "\n";
        $commands = ['ssh ' . $server . ' <<EOF' . $commands . 'EOF'];

        return $this->process($commands);
    }

    /**
     * @param $error
     * @return void
     */
    private function raiseError($error)
    {
        $this->line('<fg=red>error: ' . $error . '! </>');
        die();
    }

    /**
     * @param $msg
     * @return void
     */
    private function successMessage($msg)
    {
        $this->line('<fg=green>success: ✔ ' . $msg . '</>');
    }

    private function isModified($directory, $file)
    {
        $last_time = $this->lastRun();

        if (!in_array($file, ['.', '..'])) {
            if (filemtime(base_path() . '/' . $directory . '/' . $file) > $last_time) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    private function initLastRun()
    {
        if (!$this->last_run) {
            $log = base_path() . '/storage/logs/laravel-production.log';
            if (file_exists($log)) {
                $this->last_run = (int) file_get_contents($log);
            } else {
                $this->last_run = 1600000000;
            }
        }
    }

    /**
     * @return void
     */
    private function initConfig()
    {
        if (!$this->config) {
            $this->config = Config::get('production');
        }
    }

    /**
     * @return void
     */
    private function initNow()
    {
        if (!$this->now) {
            $this->now = now()->timestamp;
        }
    }

}