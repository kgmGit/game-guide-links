<?php

namespace App\Console\Commands;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin-user {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '管理者ユーザを作成する';

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
     * @return int
     */
    public function handle()
    {
        $params = $this->arguments();
        $params['password_confirmation'] = $params['password'];

        $createNewUser = new CreateNewUser();
        try {
            $user= $createNewUser->create($params);
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->error($key . ':' . $message);
                }
            }
            return 1;
        }
        $user->admin = true;
        $user->email_verified_at = now();
        $user->save();

        $this->info('管理者権限ユーザを作成しました');
        return 0;
    }
}
