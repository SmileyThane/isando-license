<?php

namespace App\Http\Controllers;

use App\Query;
use Illuminate\Http\Request;
use App\Repositories\QueryRepository;
use App\Repositories\ActivityLogRepository;

class QueryController extends Controller
{
	protected $request;
	protected $repo;
	protected $activity;
	protected $module = 'query';

	public function __construct(Request $request, QueryRepository $repo, ActivityLogRepository $activity)
	{
		$this->request = $request;
		$this->repo = $repo;
		$this->activity = $activity;
	}

    /**
     * Used to get all Queries
     * @get ("/api/query")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Query::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to get Query detail
     * @get ("/api/query/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Query"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', Query::class);

        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Query status
     * @post ("/api/query/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Query"),
     * })
     * @return Response
     */
    public function update($id)
    {
        $this->authorize('update', Query::class);

        $query = $this->repo->findOrFail($id);

        $query->status = request('status');
        $query->save();

        $this->activity->record([
            'module'   => $this->module,
            'module_id' => $query->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('query.updated')]);
    }

    /**
     * Used to delete Query
     * @delete ("/api/query/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Query"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', Query::class);

    	$query = $this->repo->findOrFail($id);

        $this->activity->record([
            'module'   => $this->module,
            'module_id' => $query->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($query);

        return $this->success(['message' => trans('query.deleted')]);
    }
}
