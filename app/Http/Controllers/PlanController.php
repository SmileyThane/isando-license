<?php

namespace App\Http\Controllers;

use App\Plan;
use Illuminate\Http\Request;
use App\Http\Requests\PlanRequest;
use App\Repositories\PlanRepository;
use App\Repositories\CurrencyRepository;
use App\Repositories\ActivityLogRepository;

class PlanController extends Controller
{
	protected $request;
	protected $repo;
	protected $activity;
	protected $currency;
	protected $module = 'plan';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
	public function __construct(
		Request $request, 
		PlanRepository $repo, 
		ActivityLogRepository $activity,
		CurrencyRepository $currency
	) {
		$this->request = $request;
		$this->repo = $repo;
		$this->activity = $activity;
		$this->currency = $currency;

        $this->middleware('prohibited.test.mode')->only(['store','update','destroy']);
	}

    /**
     * Used to fetch Pre-Requisites for Plan
     * @get ("/api/plan/pre-requisite")
     * @return Response
     */
	public function preRequisite()
	{
        $this->authorize('preRequisite', Plan::class);

        $payment_frequencies = generateTranslatedSelectOption(getVar('list')['payment_frequency']);
        $currencies = $this->currency->getAll();

        return $this->success(compact('payment_frequencies','currencies'));
	}

    /**
     * Used to get all Plans
     * @get ("/api/plan")
     * @return Response
     */
	public function index()
	{
        $this->authorize('list', Plan::class);

        return $this->ok($this->repo->paginate($this->request->all()));
	}

    /**
     * Used to store Plan
     * @post ("/api/plan")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Plan"),
     *      @Parameter("max_customer", type="integer", required="true", description="Max Customer allowed in Plan"),
     *      @Parameter("max_document", type="integer", required="true", description="Max Document allowed in Plan"),
     *      @Parameter("description", type="text", required="true", description="Description of Plan"),
     *      @Parameter("plan_prices", type="array", required="true", description="Plan Pricing in various Currency"),
     *      @Parameter("is_active", type="boolean", required="optional", description="Is plan active?"),
     * })
     * @return Response
     */
	public function store(PlanRequest $request)
	{
        $this->authorize('create', Plan::class);

		$plan = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $plan->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('plan.added')]);
	}

    /**
     * Used to get Plan detail
     * @get ("/api/currency/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Plan"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', Plan::class);

        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Plan
     * @patch ("/api/plan/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Plan"),
     *      @Parameter("name", type="string", required="true", description="Name of Plan"),
     *      @Parameter("max_customer", type="integer", required="true", description="Max Customer allowed in Plan"),
     *      @Parameter("max_document", type="integer", required="true", description="Max Document allowed in Plan"),
     *      @Parameter("description", type="text", required="true", description="Description of Plan"),
     *      @Parameter("plan_prices", type="array", required="true", description="Plan Pricing in various Currency"),
     *      @Parameter("is_active", type="boolean", required="optional", description="Is plan active?"),
     * })
     * @return Response
     */
    public function update(PlanRequest $request, $id)
    {
        $this->authorize('update', Plan::class);

        $plan = $this->repo->findOrFail($id);
        
        $plan = $this->repo->update($plan, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $plan->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('plan.updated')]);
    }

    /**
     * Used to delete Plan
     * @delete ("/api/plan/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Plan"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', Plan::class);
        
        $plan = $this->repo->deletable($id);

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $plan->id,
            'sub_module' => $plan->detail,
            'activity'  => 'deleted'
        ]);

        $this->repo->delete($plan);

        return $this->success(['message' => trans('plan.deleted')]);
    }
}
