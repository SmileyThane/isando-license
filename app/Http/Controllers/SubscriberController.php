<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Subscriber;
use Illuminate\Http\Request;
use App\Repositories\SubscriberRepository;
use App\Repositories\ActivityLogRepository;

class SubscriberController extends Controller
{
	protected $request;
	protected $repo;
	protected $activity;
	protected $module = 'subscriber';

	public function __construct(Request $request, SubscriberRepository $repo, ActivityLogRepository $activity)
	{
		$this->request = $request;
		$this->repo = $repo;
		$this->activity = $activity;
	}

    /**
     * Used to get all Subscribers
     * @get ("/api/subscriber")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Subscriber::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to get Subscriber detail
     * @get ("/api/subscriber/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Subscriber"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', Subscriber::class);

        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Subscriber status
     * @post ("/api/subscriber/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Subscriber"),
     * })
     * @return Response
     */
    public function update($id)
    {
        $this->authorize('update', Subscriber::class);

        $subscriber = $this->repo->findOrFail($id);

        $subscriber->status = ! $subscriber->status;
        $subscriber->date_of_unsubscribe = ($subscriber->status) ? null : Carbon::now();
        $subscriber->save();

        $this->activity->record([
            'module'   => $this->module,
            'module_id' => $subscriber->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('subscriber.updated')]);
    }

    /**
     * Used to delete Subscriber
     * @delete ("/api/subscriber/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Subscriber"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', Subscriber::class);

    	$subscriber = $this->repo->findOrFail($id);

        $this->activity->record([
            'module'   => $this->module,
            'module_id' => $subscriber->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($subscriber);

        return $this->success(['message' => trans('subscriber.deleted')]);
    }
}