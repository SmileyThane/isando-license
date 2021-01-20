<?php
namespace App\Repositories;

use App\Subscription;
use Illuminate\Validation\ValidationException;

class SubscriptionRepository
{
	protected $subscription;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
	public function __construct(Subscription $subscription)
	{
		$this->subscription = $subscription;
	}

    /**
     * Find subscription with given id or throw an error.
     *
     * @param integer $id
     * @return Instance
     */
    public function findOrFail($id)
    {
        $subscription = $this->subscription->with('plan','currency','instance')->find($id);

        if (! $subscription) {
            throw ValidationException::withMessages(['message' => trans('subscription.could_not_find')]);
        }

        return $subscription;
    }

    /**
     * Find subscription with given uuid or throw an error.
     *
     * @param integer $id
     * @return Instance
     */
    public function findByUuidOrFail($uuid)
    {
        $subscription = $this->subscription->with('plan','currency','instance')->filterByUuid($uuid)->first();

        if (! $subscription) {
            throw ValidationException::withMessages(['message' => trans('subscription.could_not_find')]);
        }

        return $subscription;
    }

    /**
     * Find subscription is failed.
     *
     * @param Subscription $subscription
     * @return void
     */
    public function isFailed(Subscription $subscription)
    {
        if (! $subscription->status) {
            throw ValidationException::withMessages(['message' => trans('subscription.could_not_find')]);
        }
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
        $status      = isset($params['status']) ? $params['status'] : 0;
        $start_date  = isset($params['start_date']) ? $params['start_date'] : null;
        $end_date    = isset($params['end_date']) ? $params['end_date'] : null;

        $query = $this->subscription->with('plan','currency','instance')->filterByStatus($status)->dateBetween([
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        return $query->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Find subscription & check it can be deleted or not.
     *
     * @param integer $id
     * @return Subscription
     */
    public function deletable($id)
    {
        $subscription = $this->findOrFail($id);

        if ($subscription->status) {
            throw ValidationException::withMessages(['message' => trans('subscription.cannot_delete_successful_subscription')]);
        }
        
        return $subscription;
    }

    /**
     * Delete subscription.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Subscription $subscription)
    {
        return $subscription->delete();
    }

    /**
     * Delete multiple subscriptions.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->subscription->whereIn('id', $ids)->delete();
    }
}