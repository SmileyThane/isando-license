<?php

namespace App\Http\Controllers;

use App\Query;
use App\Instance;
use Carbon\Carbon;
use App\Subscriber;
use App\Subscription;
use Illuminate\Http\Request;
use App\Repositories\TodoRepository;
use App\Repositories\UserRepository;
use App\Repositories\ActivityLogRepository;

class HomeController extends Controller
{
    protected $user;
    protected $activity;
    protected $todo;
    protected $instance;
    protected $query;
    protected $subscriber;
    protected $subscription;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $user, 
        ActivityLogRepository $activity, 
        TodoRepository $todo, 
        Instance $instance,
        Query $query,
        Subscriber $subscriber,
        Subscription $subscription
    )
    {
        $this->user = $user;
        $this->activity = $activity;
        $this->todo = $todo;
        $this->instance = $instance;
        $this->query = $query;
        $this->subscriber = $subscriber;
        $this->subscription = $subscription;
    }

    /**
     * Used to test web route
     */
    public function test()
    {
    }

    /**
     * Used to get Dashboard statistics
     */
    public function dashboard()
    {
        if (\Auth::user()->can('list-instance')) {
            $instance_stats = [
                'total'      => $this->instance->count(),
                'suspended'  => $this->instance->filterByStatus('suspended')->count(),
                'terminated' => $this->instance->filterByStatus('terminated')->count(),
                'expired'    => $this->instance->filterByStatus('expired')->count(),
                'trial'      => $this->instance->filterByStatus('trial')->count(),
                'pending'    => $this->instance->filterByStatus('pending')->count(),
                'running'    =>  $this->instance->filterByStatus('running')->count()
            ];

            $instance_graph_data = array(
                ['name' => trans('instance.suspended'), 'total' => $instance_stats['suspended'], 'color' => '#ffbb33' ],
                ['name' => trans('instance.terminated'), 'total' => $instance_stats['terminated'], 'color' => '#CC0000' ],
                ['name' => trans('instance.expired'), 'total' => $instance_stats['expired'], 'color' => '#ff4444' ],
                ['name' => trans('instance.trial'), 'total' => $instance_stats['trial'], 'color' => '#33b5e5' ],
                ['name' => trans('instance.running'), 'total' => $instance_stats['running'], 'color' => '#00C851' ]
            );
        }

        if (\Auth::user()->can('list-query')) {
            $query_stats = [
                'total'             => $this->query->count(),
                'replied'           => $this->query->filterByStatus('replied')->count(),
                'unanswered'        => $this->query->filterByStatus('unanswered')->count(),
                'under_process'     => $this->query->filterByStatus('under_process')->count(),
                'awaiting_response' => $this->query->filterByStatus('awaiting_response')->count(),
                'resolved'          => $this->query->filterByStatus('resolved')->count()
            ];
        }

        if (\Auth::user()->can('list-subscriber')) {
            $subscriber_stats = [
                'subscribed'   => $this->subscriber->filterByStatus(1)->count(),
                'unsubscribed' => $this->subscriber->filterByStatus(0)->count()
            ];
        }

        $graph['instance_status'] = generateDoughnutGraphData($instance_graph_data,trans('instance.status'));


        $activity_logs = $this->activity->getQuery();

        if (! \Auth::user()->hasRole(config('system.default_role.admin'))) {
            $activity_logs->filterByUserId(\Auth::user()->id);
        }

        $activity_logs = $activity_logs->orderBy('created_at', 'desc')->take(10)->get();

        $todos = $this->todo->getQuery()->filterByUserId(\Auth::user()->id)->orderBy('created_at', 'desc')->take(5)->get();

        return $this->success(compact('instance_stats','query_stats','subscriber_stats','graph','activity_logs','todos'));
    }
}
