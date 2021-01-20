<?php
namespace App\Repositories;

use App\Instance;
use App\InstanceUpdateRecord;
use App\InstanceDatabaseRecord;
use Symfony\Component\Process\Process;
use App\Repositories\ConnectionRepository;
use Illuminate\Validation\ValidationException;

class InstanceRepository
{
    protected $instance;
    protected $instance_update_record;
    protected $instance_database_record;
    protected $connection;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Instance $instance,
        InstanceUpdateRecord $instance_update_record,
        InstanceDatabaseRecord $instance_database_record,
        ConnectionRepository $connection
    ) {
        $this->instance = $instance;
        $this->instance_update_record = $instance_update_record;
        $this->instance_database_record = $instance_database_record;
        $this->connection = $connection;
    }

    /**
     * Get instance query
     *
     * @return Instance query
     */

    public function getQuery()
    {
        return $this->instance;
    }

    /**
     * Find instance with given id or throw an error.
     *
     * @param integer $id
     * @return Instance
     */

    public function findOrFail($id)
    {
        $instance = $this->instance->with('plan','plan.planPrice','currency')->find($id);

        if (! $instance) {
            throw ValidationException::withMessages(['message' => trans('instance.could_not_find')]);
        }

        return $instance;
    }

    /**
     * Paginate all instances using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order       = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $email       = isset($params['email']) ? $params['email'] : null;
        $domain      = isset($params['domain']) ? $params['domain'] : null;
        $instance_id = isset($params['instance_id']) ? $params['instance_id'] : null;
        $status      = isset($params['status']) ? $params['status'] : 0;
        $start_date  = isset($params['start_date']) ? $params['start_date'] : null;
        $end_date    = isset($params['end_date']) ? $params['end_date'] : null;

        $query = $this->instance->with('plan','currency')->filterByInstanceId($instance_id)->filterbyEmail($email)->filterByDomain($domain)->filterByStatus($status)->dateBetween([
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        return $query->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Update given instance.
     *
     * @param Instance $instance
     * @param array $params
     *
     * @return Instance
     */
    public function update(Instance $instance, $params = array())
    {
        $status                      = isset($params['status']) ? $params['status'] : running;
        $date_of_expiry              = isset($params['date_of_expiry']) ? $params['date_of_expiry'] : null;
        $max_customer                = isset($params['max_customer']) && $params['max_customer']!='' ? $params['max_customer'] : 1;
        $max_document                = isset($params['max_document']) && $params['max_customer']!='' ? $params['max_document'] : 1;
        $remarks                     = isset($params['remarks']) ? $params['remarks'] : null;
        $maintenance_mode            = (isset($params['maintenance_mode']) && $params['maintenance_mode']) ? 1 : 0;
        $maintenance_mode_message    = isset($params['maintenance_mode_message']) ? $params['maintenance_mode_message'] : null;
        $exclude_ip_from_maintenance = isset($params['exclude_ip_from_maintenance']) ? $params['exclude_ip_from_maintenance'] : null;

        $instance->forceFill([
            'status'                      => $status,
            'date_of_expiry'              => $date_of_expiry,
            'max_customer'                => $max_customer,
            'max_document'                => $max_document,
            'maintenance_mode'            => $maintenance_mode,
            'maintenance_mode_message'    => $maintenance_mode_message,
            'exclude_ip_from_maintenance' => $exclude_ip_from_maintenance
        ])->save();

        if ($instance->status === 'expired' && $instance->date_of_expiry > date('Y-m-d')) {
            $instance->status = 'running';
            $instance->save();
        }

        $this->instance_update_record->forceCreate([
            'instance_id'    => $instance->id,
            'status'         => $status,
            'date_of_expiry' => $date_of_expiry,
            'max_customer'   => $max_customer,
            'max_document'   => $max_document,
            'remarks'        => $remarks
        ]);

        return $instance;
    }

    /**
     * Find instance & check it can be deleted or not.
     *
     * @param integer $id
     * @return Instance
     */
    public function deletable($id)
    {
        $instance = $this->findOrFail($id);

        if ($instance->subscriptions()->count()) {
            throw ValidationException::withMessages(['message' => trans('instance.has_many_subscriptions')]);
        }
        
        return $instance;
    }

    /**
     * Delete instance.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Instance $instance)
    {
        $instance_id = $instance->id;
        $instance_db = $instance->database->database_name;
        $instance_db_id = $instance->db_id;

        $upload_path = getAppPath('storage/app/uploads/'.$instance->domain);
        $image_path = getAppPath('public/uploads/images/'.$instance->domain);
        $avatar_path = getAppPath('public/uploads/avatar/'.$instance->domain);
        $logo_path = getAppPath('public/uploads/logo/'.$instance->domain);

        \File::deleteDirectory($upload_path);
        \File::deleteDirectory($image_path);
        \File::deleteDirectory($avatar_path);
        \File::deleteDirectory($logo_path);

        $this->connection->toInstance($instance);

        $this->deleteDatabase($instance_db);

        $this->connection->toDefault();

        $instance = $this->findOrFail($instance_id);

        $instance->delete();

        $this->instance_database_record->find($instance_db_id)->delete();
    }

    private function deleteDatabase($database)
    {
        \DB::statement('SET foreign_key_checks = 0');

        $colname = 'Tables_in_'.$database;

        $tables = \DB::select('SHOW TABLES');

        foreach($tables as $table) {
            $droplist[] = $table->$colname;
        }

        $droplist = implode(',', $droplist);

        \DB::beginTransaction();
        \DB::statement("DROP TABLE $droplist");
        \DB::commit();

        \DB::statement('SET foreign_key_checks = 1');
    }

    /**
     * Delete multiple instances.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->instance->whereIn('id', $ids)->delete();
    }
}