<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('general.home')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">{{trans('general.home')}}</li>
                </ol>
            </div>
        </div>
        <div class="row" v-if="hasPermission('list-instance')">
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Licenses Status<!--{{trans('instance.instance')+' '+trans('instance.status')}}--></h4>
                        <table class="table browser m-t-30 no-border">
                            <tbody>
                                <tr>
                                    <td>{{trans('general.total')}}</td>
                                    <td class="text-right"><span class="label label-info">{{instance_stats.total}}</span></td>
                                </tr>
                                <tr>
                                    <td>{{trans('instance.running')}}</td>
                                    <td class="text-right"><span class="label label-success">{{instance_stats.running}}</span></td>
                                </tr>
                                <tr>
                                    <td>{{trans('instance.expired')}}</td>
                                    <td class="text-right"><span class="label label-danger">{{instance_stats.expired}}</span></td>
                                </tr>
                                <tr>
                                    <td>{{trans('instance.suspended')}}</td>
                                    <td class="text-right"><span class="label label-warning">{{instance_stats.suspended}}</span></td>
                                </tr>
                                <tr>
                                    <td>{{trans('instance.terminated')}}</td>
                                    <td class="text-right"><span class="label label-danger">{{instance_stats.terminated}}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Licenses Statistics<!--{{trans('instance.instance')+' '+trans('general.statistics')}}--></h4>
                        <doughnut-graph :graph="graph.instance_status" v-show="graph.instance_status.labels.length"></doughnut-graph>
                        <h6 class="card-subtitle" v-if="!graph.instance_status.labels.length">{{trans('general.no_result_found')}}</h6>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('activity.activity_log')}}</h4>
                        <h6 class="card-subtitle" v-if="!activity_logs.length">{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="activity_logs.length">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th v-if="hasRole('admin')">{{trans('user.user')}}</th>
                                        <th>{{trans('activity.activity')}}</th>
                                        <th class="table-option">{{trans('activity.date_time')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="activity_log in activity_logs">
                                        <td v-if="hasRole('admin')" v-text="activity_log.user.profile.first_name+' '+activity_log.user.profile.last_name"></td>
                                        <td>{{trans('activity.'+activity_log.activity,{activity: trans(activity_log.module+'.'+activity_log.module)})}}</td>
                                        <td class="table-option">{{activity_log.created_at | momentDateTime }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
               <!-- <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('general.misc')+' '+trans('general.statistics')}}</h4>
                        <table class="table browser m-t-30 no-border">
                            <tbody>
                                <template v-if="hasPermission('list-query')">
                                    <tr>
                                        <td>{{trans('general.total')+' '+trans('query.query')}}</td>
                                        <td class="text-right"><span class="label label-info">{{query_stats.total}}</span></td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('query.replied')+' '+trans('query.query')}}</td>
                                        <td class="text-right"><span class="label label-success">{{query_stats.replied}}</span></td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('query.unanswered')+' '+trans('query.query')}}</td>
                                        <td class="text-right"><span class="label label-danger">{{query_stats.unanswered}}</span></td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('query.under_process')+' '+trans('query.query')}}</td>
                                        <td class="text-right"><span class="label label-warning">{{query_stats.under_process}}</span></td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('query.awaiting_response')+' '+trans('query.query')}}</td>
                                        <td class="text-right"><span class="label label-info">{{query_stats.awaiting_response}}</span></td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('query.resolved')+' '+trans('query.query')}}</td>
                                        <td class="text-right"><span class="label label-success">{{query_stats.resolved}}</span></td>
                                    </tr>
                                </template>
                                <template v-if="hasPermission('list-subscriber')">
                                    <tr>
                                        <td>{{trans('subscriber.subscribed')+' '+trans('user.user')}}</td>
                                        <td class="text-right"><span class="label label-success">{{subscriber_stats.subscribed}}</span></td>
                                    </tr>
                                    <tr>
                                        <td>{{trans('subscriber.unsubscribed')+' '+trans('user.user')}}</td>
                                        <td class="text-right"><span class="label label-danger">{{subscriber_stats.unsubscribed}}</span></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>-->
            </div>
        </div>
<!--
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('activity.activity_log')}}</h4>
                        <h6 class="card-subtitle" v-if="!activity_logs.length">{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="activity_logs.length">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th v-if="hasRole('admin')">{{trans('user.user')}}</th>
                                        <th>{{trans('activity.activity')}}</th>
                                        <th class="table-option">{{trans('activity.date_time')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="activity_log in activity_logs">
                                        <td v-if="hasRole('admin')" v-text="activity_log.user.profile.first_name+' '+activity_log.user.profile.last_name"></td>
                                        <td>{{trans('activity.'+activity_log.activity,{activity: trans(activity_log.module+'.'+activity_log.module)})}}</td>
                                        <td class="table-option">{{activity_log.created_at | momentDateTime }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('todo.todo')}}</h4>
                        <h6 class="card-subtitle" v-if="!todos.length">{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="todos.length">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('todo.title')}}</th>
                                        <th>{{trans('todo.status')}}</th>
                                        <th class="table-option">{{trans('todo.date')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="todo in todos">
                                        <td v-text="todo.title"></td>
                                        <td v-html="getStatus(todo)"></td>
                                        <td class="table-option">{{todo.date | moment}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->
    </div>
</template>

<script>
    import doughnutGraph from '../graph/doughnut-graph'

    export default {
        components: {doughnutGraph},
        mounted(){
            axios.get('/api/dashboard')
                .then(response => response.data)
                .then(response => {
                    this.instance_stats = response.instance_stats;
                    this.query_stats = response.query_stats;
                    this.subscriber_stats = response.subscriber_stats;
                    this.graph = response.graph;
                    this.activity_logs = response.activity_logs;
                    this.todos = response.todos;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                })
        },
        methods: {
            getStatus(todo){
                return todo.status ? ('<span class="label label-success">'+i18n.todo.complete+'</span>') : ('<span class="label label-danger">'+i18n.todo.incomplete+'</span>') ;
            },
            hasRole(role){
                return helper.hasRole(role);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            }
        },
        data() {
            return {
                instance_stats: [],
                query_stats: [],
                subscriber_stats: [],
                graph: {
                    instance_status: {
                        labels: []
                    }
                },
                activity_logs: {},
                todos: {}
            }
        },
        computed: {
        },
        filters: {
          momentDateTime(date) {
            return helper.formatDateTime(date);
          },
          moment(date) {
            return helper.formatDate(date);
          }
        },
    }
</script>
