<?php
namespace App\Repositories;

use App\Query;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class QueryRepository
{
    private $query;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Get query query
     *
     * @return Query query
     */

    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Find query with given id or throw an error.
     *
     * @param integer $id
     * @return Query
     */

    public function findOrFail($id)
    {
        $query = $this->query->find($id);

        if (! $query) {
            throw ValidationException::withMessages(['message' => trans('query.could_not_find')]);
        }

        return $query;
    }

    /**
     * Paginate all queries using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order       = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $first_name  = isset($params['first_name']) ? $params['first_name'] : null;
        $last_name   = isset($params['last_name']) ? $params['last_name'] : null;
        $email       = isset($params['email']) ? $params['email'] : null;
        $phone       = isset($params['phone']) ? $params['phone'] : null;
        $keyword     = isset($params['keyword']) ? $params['keyword'] : null;
        $start_date  = isset($params['start_date']) ? $params['start_date'] : null;
        $end_date    = isset($params['end_date']) ? $params['end_date'] : null;
        $status      = isset($params['status']) ? $params['status'] : null;

        $query = $this->query->filterByFirstName($first_name)->filterByLastName($last_name)->filterByEmail($email)->filterByPhone($phone)->filterByStatus($status)->filterBySubjectOrBody($keyword)->dateBetween([
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        return $query->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Update given query.
     *
     * @param Query $query
     * @param array $params
     *
     * @return Query
     */
    public function update(Query $query, $params)
    {
    }

    /**
     * Delete query.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Query $query)
    {
        return $query->delete();
    }

    /**
     * Delete multiple queries.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->query->whereIn('id', $ids)->delete();
    }
}
