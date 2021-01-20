<?php

namespace App\Http\Controllers;

use App\Subscription;
use Illuminate\Http\Request;
use App\Repositories\ActivityLogRepository;
use App\Repositories\SubscriptionRepository;

class SubscriptionController extends Controller
{
	protected $request;
	protected $repo;
	protected $activity;
	protected $module = 'subscription';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
	public function __construct(
		Request $request,
		SubscriptionRepository $repo,
		ActivityLogRepository $activity
	) {
		$this->request = $request;
		$this->repo = $repo;
		$this->activity = $activity;
	}

    /**
     * Used to get all Subscriptions
     * @get ("/api/subscription")
     * @return Response
     */
	public function index()
    {
        $this->authorize('list', Subscription::class);

		return $this->ok($this->repo->paginate($this->request->all()));
	}

    /**
     * Used to get Subscription detail
     * @get ("/api/subscription/{id}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Subscription"),
     * })
     * @return Response
     */
	public function show($uuid)
    {
        $this->authorize('view', Subscription::class);

		$subscription = $this->repo->findByUuidOrFail($uuid);
		$countries = getVar('country');

		return $this->success(compact('subscription','countries'));
	}

    /**
     * Used to print Subscription Invoice
     * @get ("/subscription/{uuid}/print?token=auth_token")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Subscription"),
     * })
     * @return Response
     */
	public function print($uuid)
    {
        $this->authorize('print', Subscription::class);

		$subscription = $this->repo->findByUuidOrFail($uuid);

		$this->repo->isFailed($subscription);

        return view('subscription.print', compact('subscription'));
	}

    /**
     * Used to generate PDF Subscription Invoice
     * @get ("/subscription/{uuid}/pdf?token=auth_token")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Subscription"),
     * })
     * @return Response
     */
	public function pdf($uuid)
    {
        $this->authorize('pdf', Subscription::class);

		$subscription = $this->repo->findByUuidOrFail($uuid);

		$this->repo->isFailed($subscription);

        $pdf = \PDF::loadView('subscription.print', compact('subscription'));
        return $pdf->download($uuid.'.pdf');
	}

    /**
     * Used to delete Subscription if failed
     * @delete ("/api/subscription/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Subscription"),
     * })
     * @return Response
     */
	public function destroy($id)
    {
        $this->authorize('delete', Subscription::class);

		$subscription = $this->repo->deletable($id);

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $subscription->id,
            'sub_module' => $subscription->token,
            'activity'  => 'deleted'
        ]);

        $this->repo->delete($subscription);

        return $this->success(['message' => trans('subscription.deleted')]);
	}
}
