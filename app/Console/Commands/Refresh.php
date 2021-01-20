<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Refresh extends Command
{

    /**
     *  This command is used to reset the application to factory condition.
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Installation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(3);

        // First clear all the directories

        if (\File::exists('public/uploads')) {
            \File::cleanDirectory('public/uploads');
            \File::put('public/uploads/index.php', '');
        }

        if (\File::exists('storage/app/app_installed')) {
            \File::delete('storage/app/app_installed');
        }

        \File::makeDirectory('public/uploads/logo');
        \File::put('public/uploads/logo/index.php', '');
        \File::makeDirectory('public/uploads/avatar');
        \File::put('public/uploads/avatar/index.php', '');
        \File::makeDirectory('public/uploads/images');
        \File::put('public/uploads/images/index.php', '');
        \File::deleteDirectory('storage/app/uploads');
        \File::cleanDirectory('storage/framework/cache');
        \File::cleanDirectory('storage/framework/sessions');
        \File::cleanDirectory('storage/framework/views');
        \File::put('storage/logs/laravel.log', '');

        $bar->advance();

        // Clear cache/routes/views

        \Artisan::call('cache:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');
        \Artisan::call('key:generate');

        $bar->advance();

        envu([
            'APP_NAME'          => 'Mint Invoice',
            'APP_ENV'           => 'local',
            'APP_KEY'           => 'SetAny32CharacterStrongSecretKey',
            'APP_URL'           => '',
            'APP_DEBUG'         => 'false',
            'DB_HOST'           => 'localhost',
            'DB_PORT'           => 3306,
            'DB_DATABASE'       => '',
            'DB_USERNAME'       => '',
            'DB_PASSWORD'       => '',
            'MAIL_DRIVER'       => 'log',
            'MAIL_HOST'         => '',
            'MAIL_PORT'         => '',
            'MAIL_USERNAME'     => '',
            'MAIL_PASSWORD'     => '',
            'MAIL_ENCRYPTION'   => '',
            'MAIL_FROM_NAME'    => 'Hello',
            'MAIL_FROM_ADDRESS' => 'hello@example.com',
        ]);

        $bar->finish();
    }
}
