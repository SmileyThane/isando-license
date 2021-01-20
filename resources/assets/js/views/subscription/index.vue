<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">Payments<!-- {{trans('subscription.subscriptions')}} --></h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">Payments<!-- {{trans('subscription.subscriptions')}} --></li>
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
                                        <label for="">{{trans('subscription.status')}}</label>
                                        <select name="status" class="form-control" v-model="filterSubscriptionForm.status">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option value=0>{{trans('subscription.failed')}}</option>
                                            <option value=1>{{trans('subscription.complete')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterSubscriptionForm.start_date" :end-date.sync="filterSubscriptionForm.end_date" :label="trans('general.date_between')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterSubscriptionForm.sort_by">
                                            <option value="created_at">{{trans('subscription.created_at')}}</option>
                                            <option value="status">{{trans('subscription.status')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterSubscriptionForm.order">
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
                        <h4 class="card-title">{{trans('subscription.subscription')}}</h4>
                        <h6 class="card-subtitle" v-if="subscriptions">{{trans('general.total_result_found',{'count' : subscriptions.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="hasPermission('list-subscriber') && subscriptions.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('subscription.number')}}</th>
                                        <th>{{trans('subscription.email')}}</th>
                                        <th>{{trans('subscription.domain')}}</th>
                                        <th>{{trans('subscription.amount')}}</th>
                                        <th>{{trans('subscription.method')}}</th>
                                        <th>{{trans('plan.plan')}}</th>
                                        <th>{{trans('subscription.status')}}</th>
                                        <th>{{trans('subscription.created_at')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="subscription in subscriptions.data">
                                        <td v-text="getNumber(subscription)"></td>
                                        <td v-text="subscription.instance.email"></td>
                                        <td v-text="subscription.instance.domain"></td>
                                        <td>
                                            {{formatCurrency(subscription.amount,subscription.currency)}}
                                        </td>
                                        <td>
                                            {{subscription.source === 'paypal' ? 'Paypal' : 'Credit Card'}}
                                        </td>
                                        <td v-text="subscription.plan.name"></td>
                                        <td>
                                            <span v-if="subscription.status" class="badge badge-success">{{trans('subscription.complete')}}</span>
                                            <span v-else class="badge badge-danger">{{trans('subscription.failed')}}</span>
                                        </td>
                                        <td>{{subscription.created_at | moment }}</td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <a :href="`/subscription/${subscription.uuid}/print?token=${authToken}`" v-if="subscription.status" class="btn btn-warning btn-sm" v-tooltip="trans('subscription.print')" target="_blank"><i class="fas fa-print"></i></a>
                                                <a :href="`/subscription/${subscription.uuid}/pdf?token=${authToken}`" v-if="subscription.status" class="btn btn-success btn-sm" v-tooltip="trans('subscription.pdf')" target="_blank"><i class="far fa-file-pdf"></i></a>
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target=".subscription-detail" @click="fetchDetail(subscription)" v-tooltip="trans('subscription.view')" v-if="!subscription.status"><i class="fas fa-arrow-circle-right"></i></button>
                                                <button class="btn btn-danger btn-sm" :key="subscription.id" v-if="hasPermission('delete-subscription') && !subscription.status" v-confirm="{ok: confirmDelete(subscription)}" v-tooltip="trans('subscription.delete_subscription')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!subscriptions.total" module="subscription" title="module_info_title" description="module_info_description" icon="life-ring"></module-info>
                        <pagination-record :page-length.sync="filterSubscriptionForm.page_length" :records="subscriptions" @updateRecords="getSubscriptions" @change.native="getSubscriptions"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade subscription-detail" tabindex="-1" role="dialog" aria-labelledby="subscriptionDetail" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="subscriptionDetail">{{trans('subscription.subscription_detail')}} <span class="badge badge-danger">{{trans('subscription.failed')}}</span></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" v-if="subscription">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <table class="table no-border">
                                    <tr><th>{{trans('subscription.type')}}</th><th class="font-medium">{{subscription.type === 'extend' ? 'Validity Extend' : 'Upgrade'}}</th></tr>
                                    <tr><th>{{trans('subscription.date')}}</th><th class="font-medium">{{subscription.created_at | momentDateTime}}</th></tr>
                                    <tr><th>{{trans('instance.email')}}</th><th class="font-medium" v-if="subscription.instance">{{subscription.instance.email}}</th></tr>
                                    <tr><th>{{trans('instance.domain')}}</th><th v-if="subscription.instance">{{subscription.instance.domain}}</th></tr>
                                    <tr><th>{{trans('plan.plan')}}</th><th class="font-medium" v-if="subscription.plan">{{subscription.plan.name}}</th></tr>
                                    <tr><th>{{trans('subscription.validity')}}</th><th class="font-medium">{{subscription.validity | moment}}</th></tr>
                                    <tr><th>{{trans('subscription.amount')}}</th><th class="font-medium" v-if="subscription.currency">{{formatCurrency(subscription.amount,subscription.currency)}}</th></tr>
                                    <tr><th>{{trans('subscription.fail_reason')}}</th><th class="font-medium">{{subscription.gateway_status || '-'}}</th></tr>
                                </table>
                            </div>
                            <div class="col-12 col-sm-6">
                                <table class="table no-border">
                                    <tr><th>{{trans('user.address_line_1')}}</th><th class="font-medium">{{subscription.address_line_1}}</th></tr>
                                    <tr><th>{{trans('user.address_line_2')}}</th><th class="font-medium">{{subscription.address_line_2}}</th></tr>
                                    <tr><th>{{trans('user.city')}}</th><th class="font-medium">{{subscription.city}}</th></tr>
                                    <tr><th>{{trans('user.state')}}</th><th class="font-medium">{{subscription.state}}</th></tr>
                                    <tr><th>{{trans('user.zipcode')}}</th><th class="font-medium">{{subscription.zipcode}}</th></tr>
                                    <tr><th>{{trans('user.country')}}</th><th class="font-medium">{{countries[subscription.country]}}</th></tr>
                                    <tr><th>IP</th><th class="font-medium">{{subscription.ip}}</th></tr>
                                </table>
                            </div>
                        </div>
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
                subscriptions: {
                    total: 0,
                    data: []
                },
                filterSubscriptionForm: {
                    status: '',
                    start_date: '',
                    end_date: '',
                    order: 'desc',
                    sort_by: 'created_at',
                    page_length: helper.getConfig('page_length')
                },
                countries: [],
                subscription: '',
                showFilterPanel: false
            };
        },
        mounted(){
            this.getSubscriptions();
        },
        methods: {
            getSubscriptions(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterSubscriptionForm);
                axios.get('/api/subscription?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.subscriptions = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getNumber(subscription){
                return (subscription.number) ?  subscription.prefix+''+helper.formatWithPadding(subscription.number,helper.getConfig('subscription_invoice_number_digit')) : '';
            },
            fetchDetail(subscription){
                axios.get('/api/subscription/'+subscription.uuid)
                    .then(response => response.data)
                    .then(response => {
                        this.subscription = response.subscription;
                        this.countries = response.countries;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    })
            },
            confirmDelete(subscription){
                return dialog => this.deleteSubscription(subscription);
            },
            deleteSubscription(subscription){
                axios.delete('/api/subscription/'+subscription.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getSubscriptions();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            formatCurrency(amount, currency){
                return helper.formatCurrency(amount,currency);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(datetime) {
            return helper.formatDateTime(datetime);
          }
        },
        computed: {
            authToken(){
                return localStorage.getItem('auth_token');
            }
        },
        watch: {
            filterSubscriptionForm: {
                handler(val){
                    this.getSubscriptions();
                },
                deep: true
            }
        }
    }
</script>
