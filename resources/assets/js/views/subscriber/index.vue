<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('subscriber.subscriber')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('subscriber.subscriber')}}</li>
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
                                        <label for="">{{trans('subscriber.email')}}</label>
                                        <input class="form-control" name="email" v-model="filterSubscriberForm.email">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('subscriber.status')}}</label>
                                        <select name="status" class="form-control" v-model="filterSubscriberForm.status">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option value=1>{{trans('subscriber.subscribed')}}</option>
                                            <option value=0>{{trans('subscriber.unsubscribed')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterSubscriberForm.start_date" :end-date.sync="filterSubscriberForm.end_date" :label="trans('general.date_between')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterSubscriberForm.sort_by">
                                            <option value="email">{{trans('subscriber.email')}}</option>
                                            <option value="created_at">{{trans('subscriber.created_at')}}</option>
                                            <option value="status">{{trans('subscriber.status')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterSubscriberForm.order">
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
                        <h4 class="card-title">{{trans('subscriber.subscriber')}}</h4>
                        <h6 class="card-subtitle" v-if="subscribers">{{trans('general.total_result_found',{'count' : subscribers.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="hasPermission('list-subscriber') && subscribers.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('subscriber.email')}}</th>
                                        <th>{{trans('subscriber.status')}}</th>
                                        <th>{{trans('subscriber.ip')}}</th>
                                        <th>{{trans('subscriber.user_agent')}}</th>
                                        <th>{{trans('subscriber.created_at')}}</th>
                                        <th>{{trans('subscriber.updated_at')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="subscriber in subscribers.data">
                                        <td v-text="subscriber.email"></td>
                                        <td v-html="getStatus(subscriber)"></td>
                                        <td v-text="subscriber.ip"></td>
                                        <td v-text="subscriber.user_agent"></td>
                                        <td>{{subscriber.created_at | moment }}</td>
                                        <td>{{subscriber.updated_at | moment }}</td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button :class="['btn',(subscriber.status) ? 'btn-warning' : 'btn-info','btn-sm']" v-if="hasPermission('edit-subscriber')" v-tooltip="trans('subscriber.'+(subscriber.status ? 'unsubscribe' : 'subscribe'))" @click="updateStatus(subscriber)"><i :class="['fas',(subscriber.status) ? 'fa-times' : 'fa-check']"></i></button>
                                                <button class="btn btn-danger btn-sm" :key="subscriber.id" v-if="hasPermission('delete-subscriber')" v-confirm="{ok: confirmDelete(subscriber)}" v-tooltip="trans('general.delete')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!subscribers.total" module="subscriber" title="module_info_title" description="module_info_description" icon="bullhorn"></module-info>
                        <pagination-record :page-length.sync="filterSubscriberForm.page_length" :records="subscribers" @updateRecords="getSubscribers" @change.native="getSubscribers"></pagination-record>
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
                subscribers: {
                    total: 0,
                    data: []
                },
                filterSubscriberForm: {
                    email: '',
                    status: '',
                    start_date: '',
                    end_date: '',
                    order: 'desc',
                    sort_by: 'created_at',
                    page_length: helper.getConfig('page_length')
                },
                showFilterPanel: false
            };
        },
        mounted(){
            this.getSubscribers();
        },
        methods: {
            getSubscribers(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterSubscriberForm);
                axios.get('/api/subscriber?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.subscribers = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmDelete(subscriber){
                return dialog => this.deleteQuery(subscriber);
            },
            deleteQuery(subscriber){
                axios.delete('/api/subscriber/'+subscriber.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getSubscribers();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getStatus(subscriber){
                if(subscriber.status)
                    return '<span class="badge badge-success">'+i18n.subscriber.subscribed+'</span>';
                else if(! subscriber.status)
                    return '<span class="badge badge-danger">'+i18n.subscriber.unsubscribed+'</span>';
            },
            updateStatus(subscriber){
                axios.patch('/api/subscriber/'+subscriber.id).then(response => response.data)
                .then(response => {
                    this.getSubscribers();
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
            filterSubscriberForm: {
                handler(val){
                    this.getSubscribers();
                },
                deep: true
            }
        }
    }
</script>
