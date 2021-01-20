<?php
namespace App\Repositories;

use App\Instance;
use Illuminate\Validation\ValidationException;

class ConnectionRepository
{

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
    ) {
    }

    /**
     * Connect to instance database
     *
     * @param Instance $instance
     * @return void
     */
    public function toInstance(Instance $instance)
    {
        if (! $instance->database) {
            return;
        }

        config([
            'database.connections.secondary.database' => $instance->database->database_name
        ]);

        try {
            \DB::connection('secondary')->getPdo();
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['message' => 'There is some issue with database connection. Please try again after some time!']);
        }

        \DB::disconnect('primary');
        \DB::setDefaultConnection('secondary');
    }

    /**
     * Connect to default database
     *
     * @return void
     */
    public function toDefault()
    {
        \DB::disconnect('secondary');
        \DB::setDefaultConnection('primary');
    }
}