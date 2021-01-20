<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">License<!-- {{trans('instance.instance')}} --></h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">License<!-- {{trans('instance.instance')}} --></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">License Details<!--{{trans('instance.instance_detail')}}--></h4>
                        <div class="d-flex align-items-center flex-row m-t-10">
                            <div class="p-2 display-5 text-info">
                                <span v-if="instance.status === 'pending'"><i class="fas fa-exclamation-triangle text-info"></i></span>
                                <span v-if="instance.status === 'running'"><i class="fas fa-check-circle text-success"></i></span>
                                <span v-if="instance.status === 'expired'"><i class="fas fa-lock text-danger"></i></span>
                                <span v-if="instance.status === 'suspended'"><i class="fas fa-ban text-warning"></i></span>
                                <span v-if="instance.status === 'terminated'"><i class="fas fa-times-circle text-danger"></i></span>
                            </div>
                            <div class="p-2">
                                <h3 class="m-b-0">
                                    {{trans('instance.'+instance.status)}}

                                    <span v-if="instance.is_trial" class="badge badge-danger text-right">{{trans('subscription.trial')}}</span>
                                </h3>
                                <small v-if="instance.status != 'pending'">{{trans('subscription.till')}} {{instance.date_of_expiry | moment}}</small>
                            </div>
                        </div>
                        <table class="table no-border">
                            <tr>
                                <td>{{trans('instance.email')}}</td>
                                <td class="text-right">{{instance.email}}</td>
                            </tr>
                            <tr>
                                <td>{{trans('instance.domain')}}</td>
                                <td class="text-right">{{instance.domain}}</td>
                            </tr>
                            <tr>
                                <td>{{trans('user.name')}}</td>
                                <td class="text-right">{{instance.first_name+' '+instance.last_name}}</td>
                            </tr>
                            <tr>
                                <td>{{trans('plan.plan')}}</td>
                                <td class="text-right">{{instance.plan.name}}  {{getPrice}}</td>
                            </tr>
                            <tr>
                                <td>{{trans('instance.created_at')}}</td>
                                <td class="text-right">{{instance.created_at | momentDateTime}}</td>
                            </tr>
                            <tr>
                                <td>{{trans('instance.updated_at')}}</td>
                                <td class="text-right">{{instance.updated_at | momentDateTime}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="card" v-if="instance.status != 'pending' && hasPermission('edit-instance')">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('instance.update')}}</h4>
                        <form @submit.prevent="update" @keydown="updateInstanceForm.errors.clear($event.target.name)">
                            <div class="form-group">
                                <label for="">{{trans('instance.status')}}</label>
                                <select name="status" class="form-control" v-model="updateInstanceForm.status">
                                    <option value="running">{{trans('instance.running')}}</option>
                                    <option value="expired">{{trans('instance.expired')}}</option>
                                    <option value="suspended">{{trans('instance.suspended')}}</option>
                                    <option value="terminated">{{trans('instance.terminated')}}</option>
                                </select>
                                <show-error :form-name="updateInstanceForm" prop-name="status"></show-error>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('subscription.validity')}}</label>
                                <datepicker v-model="updateInstanceForm.date_of_expiry" :bootstrapStyling="true" name="date_of_expiry" @selected="updateInstanceForm.errors.clear('date_of_expiry')"></datepicker>
                                <show-error :form-name="updateInstanceForm" prop-name="date_of_expiry"></show-error>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('instance.max_customer')}}</label>
                                <input class="form-control" type="text" name="max_customer" v-model="updateInstanceForm.max_customer">
                                <show-error :form-name="updateInstanceForm" prop-name="max_customer"></show-error>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('instance.max_document')}}</label>
                                <input class="form-control" type="text" name="max_document" v-model="updateInstanceForm.max_document">
                                <show-error :form-name="updateInstanceForm" prop-name="max_document"></show-error>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('instance.remarks')}}</label>
                                <autosize-textarea rows="2" name="remarks" :placeholder="trans('configuration.remarks')" v-model="updateInstanceForm.remarks"></autosize-textarea>
                                <show-error :form-name="updateInstanceForm" prop-name="remarks"></show-error>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('configuration.single_instance_maintenance_mode')}}</label>
                                <div>
                                    <switches class="" v-model="updateInstanceForm.maintenance_mode" name="maintenance_mode" theme="bootstrap" color="success"></switches>
                                </div>
                            </div>
                            <template v-if="updateInstanceForm.maintenance_mode">
                                <div class="form-group">
                                    <label for="">{{trans('configuration.instance_maintenance_mode_message')}}</label>
                                    <autosize-textarea rows="2" name="maintenance_mode_message" :placeholder="trans('configuration.instance_maintenance_mode_message')" v-model="updateInstanceForm.maintenance_mode_message"></autosize-textarea>
                                    <show-error :form-name="updateInstanceForm" prop-name="maintenance_mode_message"></show-error>
                                </div>
                                <div class="form-group">
                                    <label for="">{{trans('configuration.instance_maintenance_exclude_ip')}} <show-tip module="configuration" tip="tip_instance_maintenance_exclude_ip" type="field"></show-tip></label>
                                    <autosize-textarea rows="2" name="exclude_ip_from_maintenance" :placeholder="trans('configuration.instance_maintenance_exclude_ip')" v-model="updateInstanceForm.exclude_ip_from_maintenance"></autosize-textarea>
                                    <show-error :form-name="updateInstanceForm" prop-name="exclude_ip_from_maintenance"></show-error>
                                </div>
                            </template>
                            <button type="submit" class="btn btn-info waves-effect waves-light pull-right">{{trans('general.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Payments Overview<!--{{trans('instance.instance_detail')}}--></h4>
                        <h6 class="card-subtitle" v-if="subscriptions">{{trans('general.total_result_found',{'count' : subscriptions.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="subscriptions.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('subscription.date')}}</th>
                                        <th>{{trans('subscription.amount')}}</th>
                                        <th>{{trans('subscription.plan')}}</th>
                                        <th>{{trans('subscription.method')}}</th>
                                        <th>{{trans('subscription.validity')}}</th>
                                        <th>{{trans('subscription.status')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="subscription in subscriptions.data">
                                        <td>{{subscription.created_at | moment}}</td>
                                        <td v-text="formatCurrency(subscription.amount,subscription.currency)"></td>
                                        <td v-text="subscription.plan.name"></td>
                                        <td v-text="subscription.method === 'paypal' ? 'Paypal' : 'Credit Card'"></td>
                                        <td>{{subscription.validity | moment}}</td>
                                        <td>
                                            <span v-if="subscription.status" class="badge badge-success">{{trans('subscription.complete')}}</span>
                                            <span v-else class="badge badge-danger">{{trans('subscription.failed')}}</span>
                                        </td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!subscriptions.total" module="subscription" title="module_info_title" icon="life-ring"></module-info>
                        <pagination-record :page-length.sync="filterSubscriptionForm.page_length" :records="subscriptions" @updateRecords="getSubscriptions" @change.native="getSubscriptions"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import autosizeTextarea from '../../components/autosize-textarea'
    import datepicker from 'vuejs-datepicker'
    import switches from 'vue-switches'

    export default {
        components:{autosizeTextarea,datepicker,switches},
        data() {
            return {
                id:this.$route.params.id,
                instance: {
                    plan: {
                        plan_price: []
                    },
                    currency: {

                    }
                },
                subscriptions: {
                    total: 0,
                    data: []
                },
                filterSubscriptionForm: {
                    instance_id : '',
                    page_length: helper.getConfig('page_length')
                },
                updateInstanceForm : new Form({
                    status: '',
                    max_customer: '',
                    max_document: '',
                    date_of_expiry: '',
                    remarks: '',
                    maintenance_mode: false,
                    maintenance_mode_message: '',
                    exclude_ip_from_maintenance: ''
                },false)
            };
        },
        mounted(){
            if(!helper.hasPermission('list-instance')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            this.getInstance();
        },
        methods: {
            getInstance(){
                axios.get('/api/instance/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.instance = response.instance;
                        this.filterSubscriptionForm.instance_id = this.instance.id;
                        this.updateInstanceForm.status = this.instance.status;
                        this.updateInstanceForm.max_customer = this.instance.max_customer;
                        this.updateInstanceForm.max_document = this.instance.max_document;
                        this.updateInstanceForm.date_of_expiry = this.instance.date_of_expiry;
                        this.updateInstanceForm.maintenance_mode = this.instance.maintenance_mode;
                        this.updateInstanceForm.maintenance_mode_message = this.instance.maintenance_mode_message;
                        this.updateInstanceForm.exclude_ip_from_maintenance = this.instance.exclude_ip_from_maintenance;
                        this.getSubscriptions();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.$router.push('/instance');
                    });

            },
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
            update(){
                this.updateInstanceForm.maintenance_mode = (this.updateInstanceForm.maintenance_mode) ? 1 : 0;
                this.updateInstanceForm.date_of_expiry = (this.updateInstanceForm.date_of_expiry) ? moment(this.updateInstanceForm.date_of_expiry).format('YYYY-MM-DD') : null;
                this.updateInstanceForm.patch('/api/instance/'+this.instance.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.getInstance();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            },
            formatCurrency(amount, currency){
                return helper.formatCurrency(amount, currency);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            }
        },
        computed: {
            getPrice(){
                let price = this.instance.plan.plan_price.find(o => o.frequency === this.instance.frequency && o.currency_id == this.instance.currency.id);

                return (price) ? ('('+helper.formatCurrency(price.amount, this.instance.currency)+' / '+ (price.frequency === 'monthly' ? 'Mo' : 'Yr') +')') : '';
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(datetime) {
            return helper.formatDateTime(datetime);
          }
        }
    }
</script>