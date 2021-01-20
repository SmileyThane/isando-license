<?php

namespace App\Http\Controllers;

use App\Instance;
use Illuminate\Http\Request;
use App\Http\Requests\InstanceRequest;
use App\Repositories\InstanceRepository;
use App\Repositories\ActivityLogRepository;

class InstanceController extends Controller
{
	protected $request;
	protected $repo;
	protected $activity;
	protected $module = 'instance';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
	public function __construct(
		Request $request, 
		InstanceRepository $repo, 
		ActivityLogRepository $activity
	) {
		$this->request = $request;
		$this->repo = $repo;
		$this->activity = $activity;

        $this->middleware('prohibited.test.mode')->only(['update','destroy']);
	}

    /**
     * Used to get all Instances
     * @get ("/api/instance")
     * @return Response
     */
	public function index()
	{
        $this->authorize('list', Instance::class);

		return $this->ok($this->repo->paginate($this->request->all()));
	}

    /**
     * Used to get Instance detail
     * @get ("/api/instance/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Instance"),
     * })
     * @return Response
     */
	public function show($id)
	{	
        $this->authorize('view', Instance::class);

		$instance = $this->repo->findOrFail($id);

		return $this->success(compact('instance'));
	}

    /**
     * Used to update Instance
     * @patch ("/api/instance/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Instance"),
     *      @Parameter("status", type="string", required="true", description="Status of Instance"),
     *      @Parameter("max_customer", type="integer", required="true", description="Max Customer allowed in Instance"),
     *      @Parameter("max_document", type="integer", required="true", description="Max Document allowed in Instance"),
     *      @Parameter("remarks", type="text", required="true", remarks="Update remarks"),
     *      @Parameter("date_of_expiry", type="date", required="true", description="Date of expiry of Instance")
     * })
     * @return Response
     */
	public function update(InstanceRequest $request, $id)
	{
        $this->authorize('update', Instance::class);

		$instance = $this->repo->findOrFail($id);

		$instance = $this->repo->update($instance, $this->request->all());

		$this->activity->record([
			'module' => $this->module,
			'module_id' => $instance->id,
			'activity' => 'updated'
		]);

		return $this->success(['message' => trans('instance.updated')]);
	}

    /**
     * Used to delete Instance
     * @delete ("/api/instance/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Instance"),
     * })
     * @return Response
     */
	public function destroy($id)
	{
        $this->authorize('delete', Instance::class);
        
		$instance = $this->repo->deletable($id);

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $instance->id,
            'activity'  => 'deleted'
        ]);

        $this->repo->delete($instance);

        return $this->success(['message' => trans('instance.deleted')]);
	}
}
