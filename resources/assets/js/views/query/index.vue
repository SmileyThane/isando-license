<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('query.query')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('query.query')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('query.keyword')}}</label>
                                        <input class="form-control" name="keyword" v-model="filterQueryForm.keyword">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('query.first_name')}}</label>
                                        <input class="form-control" name="first_name" v-model="filterQueryForm.first_name">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('query.last_name')}}</label>
                                        <input class="form-control" name="last_name" v-model="filterQueryForm.last_name">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('query.email')}}</label>
                                        <input class="form-control" name="email" v-model="filterQueryForm.email">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('query.phone')}}</label>
                                        <input class="form-control" name="phone" v-model="filterQueryForm.phone">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('query.status')}}</label>
                                        <select name="status" class="form-control" v-model="filterQueryForm.status">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option value="replied">{{trans('query.replied')}}</option>
                                            <option value="unanswered">{{trans('query.unanswered')}}</option>
                                            <option value="under_process">{{trans('query.under_process')}}</option>
                                            <option value="awaiting_response">{{trans('query.awaiting_response')}}</option>
                                            <option value="resolved">{{trans('query.resolved')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterQueryForm.start_date" :end-date.sync="filterQueryForm.end_date" :label="trans('general.date_between')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterQueryForm.sort_by">
                                            <option value="first_name">{{trans('query.first_name')}}</option>
                                            <option value="last_name">{{trans('query.last_name')}}</option>
                                            <option value="email">{{trans('query.email')}}</option>
                                            <option value="phone">{{trans('query.phone')}}</option>
                                            <option value="subject">{{trans('query.subject')}}</option>
                                            <option value="created_at">{{trans('query.created_at')}}</option>
                                            <option value="status">{{trans('query.status')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterQueryForm.order">
                                            <option value="asc">{{trans('general.ascending')}}</option>
                                            <option value="desc">{{trans('general.descending')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="!showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> {{trans('general.filter')}}</button>
                        <h4 class="card-title">{{trans('query.query')}}</h4>
                        <h6 class="card-subtitle" v-if="queries">{{trans('general.total_result_found',{'count' : queries.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="hasPermission('list-query') && queries.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('query.first_name')}}</th>
                                        <th>{{trans('query.last_name')}}</th>
                                        <th>{{trans('query.status')}}</th>
                                        <th>{{trans('query.email')}}</th>
                                        <th>{{trans('query.phone')}}</th>
                                        <th>{{trans('query.subject')}}</th>
                                        <th>{{trans('query.created_at')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="query in queries.data">
                                        <td v-text="query.first_name"></td>
                                        <td v-text="query.last_name"></td>
                                        <td v-html="getStatus(query)"></td>
                                        <td v-text="query.email"></td>
                                        <td v-text="query.phone"></td>
                                        <td v-text="query.subject"></td>
                                        <td>{{query.created_at | moment }}</td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".query-detail" @click="fetchQuery(query)" v-tooltip="trans('query.view')"><i class="fas fa-arrow-circle-right"></i></button>
                                                <button class="btn btn-danger btn-sm" :key="query.id" v-if="hasPermission('delete-query')" v-confirm="{ok: confirmDelete(query)}" v-tooltip="trans('general.delete')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!queries.total" module="query" title="module_info_title" description="module_info_description" icon="question-circle"></module-info>
                        <pagination-record :page-length.sync="filterQueryForm.page_length" :records="queries" @updateRecords="getQueries" @change.native="getQueries"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade query-detail" tabindex="-1" role="dialog" aria-labelledby="queryDetail" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="queryDetail">{{trans('query.query')}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" v-if="query">
                        <h4>{{query.subject}}
                            <span class="pull-right">{{query.created_at | moment}}</span>
                        </h4>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <th>{{trans('query.name')}}</th>
                                            <td>{{query.first_name+' '+query.last_name}}</td>
                                        </tbody>
                                        <tbody>
                                            <th>{{trans('query.email')}}</th>
                                            <td>{{query.email}}</td>
                                        </tbody>
                                        <tbody>
                                            <th>{{trans('query.phone')}}</th>
                                            <td>{{query.phone}}</td>
                                        </tbody>
                                        <tbody>
                                            <th>{{trans('query.ip')}}</th>
                                            <td>{{query.ip}}</td>
                                        </tbody>
                                        <tbody>
                                            <th>{{trans('query.user_agent')}}</th>
                                            <td>{{query.user_agent}}</td>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6" v-if="hasPermission('edit-query')">
                                <h4>{{trans('query.update_status')}}</h4>
                                <div class="form-group">
                                    <div class="radio radio-info">
                                        <input type="radio" value="unanswered" id="status_unanswered" v-model="status" :checked="query.status === 'unanswered'">
                                        <label for="status_unanswered"> {{trans('query.unanswered')}} </label>
                                    </div>
                                    <div class="radio radio-info">
                                        <input type="radio" value="replied" id="status_replied" v-model="status" :checked="query.status === 'replied'">
                                        <label for="status_replied"> {{trans('query.replied')}} </label>
                                    </div>
                                    <div class="radio radio-info">
                                        <input type="radio" value="under_process" id="status_under_process" v-model="status" :checked="query.status === 'under_process'">
                                        <label for="status_under_process"> {{trans('query.under_process')}} </label>
                                    </div>
                                        <div class="radio radio-info">
                                    <input type="radio" value="awaiting_response" id="status_awaiting_response" v-model="status" :checked="query.status === 'awaiting_response'">
                                        <label for="status_awaiting_response"> {{trans('query.awaiting_response')}} </label>
                                    </div>
                                        <div class="radio radio-info">
                                    <input type="radio" value="resolved" id="status_resolved" v-model="status" :checked="query.status === 'resolved'">
                                        <label for="status_resolved"> {{trans('query.resolved')}} </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info btn-sm" @click="updateStatus">{{trans('general.save')}}</button>
                                </div>
                            </div>
                        </div>
                        <div v-html="query.body"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">{{trans('general.close')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import dateRangePicker from '../../components/date-range-picker'

    export default {
        components: {dateRangePicker},
        data(){
            return {
                queries: {
                    total: 0,
                    data: []
                },
                filterQueryForm: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    keyword: '',
                    status: '',
                    start_date: '',
                    end_date: '',
                    order: 'desc',
                    sort_by: 'created_at',
                    page_length: helper.getConfig('page_length')
                },
                query: {},
                showFilterPanel: false,
                status: '',
                id: ''
            };
        },
        mounted(){
            this.getQueries();
        },
        methods: {
            getQueries(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterQueryForm);
                axios.get('/api/query?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.queries = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            fetchQuery(query){
                axios.get('/api/query/'+query.id)
                    .then(response => response.data)
                    .then(response => {
                        this.query = response;
                        this.status = response.status;
                        this.id = response.id;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmDelete(query){
                return dialog => this.deleteQuery(query);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            deleteQuery(query){
                axios.delete('/api/query/'+query.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getQueries();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getStatus(query){
                if(query.status === 'unanswered')
                    return '<span class="badge badge-danger">'+i18n.query.unanswered+'</span>';
                else if(query.status === 'under_process')
                    return '<span class="badge badge-info">'+i18n.query.under_process+'</span>';
                else if(query.status === 'replied')
                    return '<span class="badge badge-success">'+i18n.query.replied+'</span>';
                else if(query.status === 'awaiting_response')
                    return '<span class="badge badge-warning">'+i18n.query.awaiting_response+'</span>';
                else if(query.status === 'resolved')
                    return '<span class="badge badge-success">'+i18n.query.resolved+'</span>';
            },
            nl2br(str){
                return helper.nl2br(str);
            },
            updateStatus(){
                axios.patch('/api/query/'+this.id,{
                    id: this.id,
                    status: this.status
                }).then(response => response.data)
                .then(response => {
                    this.getQueries();
                    toastr.success(response.message);
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
            }
        },
        filters: {
          moment(date) {
            return helper.formatDateTime(date);
          }
        },
        watch: {
            filterQueryForm: {
                handler(val){
                    this.getQueries();
                },
                deep: true
            }
        }
    }
</script>
