<?php
namespace App\Repositories;

use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class SubscriberRepository
{
    private $subscriber;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * Get subscriber subscriber
     *
     * @return Subscriber subscriber
     */

    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * Find subscriber with given id or throw an error.
     *
     * @param integer $id
     * @return Subscriber
     */

    public function findOrFail($id)
    {
        $subscriber = $this->subscriber->find($id);

        if (! $subscriber) {
            throw ValidationException::withMessages(['message' => trans('subscriber.could_not_find')]);
        }

        return $subscriber;
    }

    /**
     * Paginate all subscribers using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order       = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $email  = isset($params['email']) ? $params['email'] : null;
        $start_date  = isset($params['start_date']) ? $params['start_date'] : null;
        $end_date    = isset($params['end_date']) ? $params['end_date'] : null;
        $status      = isset($params['status']) ? $params['status'] : null;

        $subscriber = $this->subscriber->filterByEmail($email)->filterByStatus($status)->dateBetween([
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        return $subscriber->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Update given subscriber.
     *
     * @param Subscriber $subscriber
     * @param array $params
     *
     * @return Subscriber
     */
    public function update(Subscriber $subscriber, $params)
    {
    }

    /**
     * Delete subscriber.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Subscriber $subscriber)
    {
        return $subscriber->delete();
    }

    /**
     * Delete multiple subscribers.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->subscriber->whereIn('id', $ids)->delete();
    }
}
