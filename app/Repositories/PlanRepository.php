<?php
namespace App\Repositories;

use App\Plan;
use App\PlanPrice;
use Carbon\Carbon;
use App\Repositories\InstanceRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Validation\ValidationException;

class PlanRepository
{
    private $plan;
    protected $plan_price;
    protected $instance;
    protected $subscription;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Plan $plan, 
        PlanPrice $plan_price, 
        InstanceRepository $instance,
        SubscriptionRepository $subscription
    ) {
        $this->plan = $plan;
        $this->plan_price = $plan_price;
        $this->instance = $instance;
        $this->subscription = $subscription;
    }

    /**
     * Get plan count
     *
     * @return integer
     */

    public function count()
    {
        return $this->plan->count();
    }

    /**
     * Get plan query
     *
     * @return Plan query
     */

    public function getQuery()
    {
        return $this->plan;
    }

    /**
     * Get first plan
     *
     * @return Plan query
     */

    public function first()
    {
        return $this->plan->with('planPrice','planPrice.currency')->first();
    }

    /**
     * Get default plan
     *
     * @return Plan query
     */

    public function default()
    {
        return $this->plan->with('planPrice','planPrice.currency')->filterByIsDefault(1)->first();
    }

    /**
     * Get featured query
     *
     * @return Plan query
     */

    public function featured()
    {
        return $this->plan->with('planPrice','planPrice.currency')->filterByIsFeatured(1)->first();
    }

    /**
     * Find plan with given id or throw an error.
     *
     * @param integer $id
     * @return Plan
     */

    public function findOrFail($id)
    {
        $plan = $this->plan->with('planPrice','planPrice.currency')->find($id);

        if (! $plan) {
            throw ValidationException::withMessages(['message' => trans('plan.could_not_find')]);
        }

        return $plan;
    }

    /**
     * Find plan with given slug or throw an error.
     *
     * @param string $slug
     * @return Plan
     */

    public function findBySlugOrFail($slug)
    {
        $plan = $this->plan->with('planPrice','planPrice.currency')->filterByExactSlug($slug);

        if (! $plan) {
            throw ValidationException::withMessages(['message' => trans('plan.could_not_find')]);
        }

        return $plan;
    }

    /**
     * Paginate all plans using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order      = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $name     = isset($params['name']) ? $params['name'] : null;

        $query = $this->plan->with('planPrice','planPrice.currency')->filterByName($name);

        return $query->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new plan.
     *
     * @param array $params
     * @return Plan
     */
    public function create($params)
    {
        $this->validatePrice($params);

        $plan = $this->plan->forceCreate($this->formatParams($params));

        $this->updateDefault($plan->id, $params);

        $this->updateFeatured($plan->id, $params);

        return $this->updatePrice($plan, $params);
    }

    /**
     * Update plan is_default field.
     *
     * @param integer $plan_id
     * @param array $params
     * @return void
     */
    private function updateDefault($plan_id, $params)
    {
        $is_default = (isset($params['is_default']) && $params['is_default']) ? 1 : 0;

        if ($is_default) {
            $this->plan->whereNotNull('id')->update(['is_default' => 0]);
            $this->plan->filterById($plan_id)->update(['is_default' => 1]);
        }

        if (! $this->default() && $this->count() <= 1) {
            $this->first()->update(['is_default' => 1]);
        }
    }

    /**
     * Update plan is_featured field.
     *
     * @param integer $plan_id
     * @param array $params
     * @return void
     */
    private function updateFeatured($plan_id, $params)
    {
        $is_featured = (isset($params['is_featured']) && $params['is_featured']) ? 1 : 0;

        if ($is_featured) {
            $this->plan->whereNotNull('id')->update(['is_featured' => 0]);
            $this->plan->filterById($plan_id)->update(['is_featured' => 1]);
        } else {
            $this->plan->filterById($plan_id)->update(['is_featured' => 0]);
        }
    }

    /**
     * Validate plan price.
     *
     * @param array $params
     * @return void
     */
    private function validatePrice($params = array())
    {
        $plan_prices = isset($params['plan_prices']) ? $params['plan_prices'] : [];
        $errors = array();

        if (! $plan_prices) {
            $errors['message'] = [trans('currency.no_currency_found')];
        }

        foreach ($plan_prices as $plan_price) {
            $frequencies = isset($plan_price['frequencies']) ? $plan_price['frequencies'] : [];
            $currency = isset($plan_price['currency']) ? $plan_price['currency'] : [];

            foreach ($frequencies as $frequency) {
                $amount = isset($frequency['amount']) ? $frequency['amount'] : 0;

                if (! is_numeric($amount) || $amount < 0) {
                    $errors['plan_price_'.$currency['id'].'_'.$frequency['payment_frequency']['id']] = [trans('validation.numeric',['attribute' => trans('plan.price')])];
                }

                if ($amount < 0) {
                    $errors['plan_price_'.$currency['id'].'_'.$frequency['payment_frequency']['id']] = [trans('validation.min.numeric',['min' => trans('plan.price')])];
                }
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Update plan price
     *
     * @param Plan $plan
     * @param array $params
     * @return Plan
     */
    private function updatePrice(Plan $plan, $params = array())
    {
        $plan_prices = isset($params['plan_prices']) ? $params['plan_prices'] : [];

        foreach ($plan_prices as $plan_price) {
            $frequencies = isset($plan_price['frequencies']) ? $plan_price['frequencies'] : [];
            $currency = isset($plan_price['currency']) ? $plan_price['currency'] : [];

            foreach ($frequencies as $frequency) {
                $price = $this->plan_price->firstOrCreate([
                    'currency_id' => $currency['id'],
                    'plan_id' => $plan->id,
                    'frequency' => $frequency['payment_frequency']['id']
                ]);

                $price->amount = $frequency['amount'];
                $price->save();
            }
        }

        return $plan;
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $action = 'create')
    {
        $formatted = [
            'name'         => isset($params['name']) ? $params['name'] : null,
            'code'         => isset($params['code']) ? $params['code'] : null,
            'max_customer' => isset($params['max_customer']) ? $params['max_customer'] : 0,
            'max_document' => isset($params['max_document']) && $params['max_document']!='' ? $params['max_document'] : 0,
            'description'  => isset($params['description']) ? $params['description'] : null,
            'is_active'    => (isset($params['is_active']) && $params['is_active']) ? 1 : 0
        ];

        $formatted['slug'] = createSlug($formatted['name']);

        return $formatted;
    }

    /**
     * Update given plan.
     *
     * @param Plan $plan
     * @param array $params
     *
     * @return Plan
     */
    public function update(Plan $plan, $params)
    {
        $this->validatePrice($params);

        $plan->forceFill($this->formatParams($params, 'update'))->save();

        $plan = $this->updatePrice($plan, $params);

        $this->updateDefault($plan->id, $params);

        $this->updateFeatured($plan->id, $params);

        return $plan;
    }

    /**
     * Find plan & check it can be deleted or not.
     *
     * @param integer $id
     * @return Plan
     */
    public function deletable($id)
    {
        $plan = $this->findOrFail($id);

        if ($plan->instances()->count()) {
            throw ValidationException::withMessages(['message' => trans('plan.has_many_instances')]);
        }

        if ($plan->subscriptions->count()) {
            throw ValidationException::withMessages(['message' => trans('plan.has_many_subscriptions')]);
        }
        
        return $plan;
    }

    /**
     * Delete plan.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Plan $plan)
    {
        return $plan->delete();
    }

    /**
     * Delete multiple plans.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->plan->whereIn('id', $ids)->delete();
    }
}
