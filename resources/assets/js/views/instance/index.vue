<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">Licenses<!--{{trans('instance.instance')}}--></h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">Licenses<!--{{trans('instance.instance')}}--></li>
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
                                        <label for="">{{trans('instance.domain')}}</label>
                                        <input class="form-control" name="domain" v-model="filterInstanceForm.domain">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('instance.email')}}</label>
                                        <input class="form-control" name="email" v-model="filterInstanceForm.email">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('instance.status')}}</label>
                                        <select name="status" class="form-control" v-model="filterInstanceForm.status">
                                            <option value="">{{trans('general.select_one')}}</option>
                                            <option value="pending">{{trans('instance.pending')}}</option>
                                            <option value="running">{{trans('instance.running')}}</option>
                                            <option value="trial">{{trans('instance.trial')}}</option>
                                            <option value="expired">{{trans('instance.expired')}}</option>
                                            <option value="suspended">{{trans('instance.suspended')}}</option>
                                            <option value="terminated">{{trans('instance.terminated')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterInstanceForm.start_date" :end-date.sync="filterInstanceForm.end_date" :label="trans('general.date_between')"></date-range-picker>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterInstanceForm.sort_by">
                                            <option value="email">{{trans('instance.email')}}</option>
                                            <option value="created_at">{{trans('instance.created_at')}}</option>
                                            <option value="status">{{trans('instance.status')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterInstanceForm.order">
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
                        <h4 class="card-title">{{trans('instance.instance')}}</h4>
                        <h6 class="card-subtitle" v-if="instances">{{trans('general.total_result_found',{'count' : instances.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="hasPermission('list-instance') && instances.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('instance.email')}}</th>
                                        <th>{{trans('instance.domain')}}</th>
                                        <th>{{trans('instance.status')}}</th>
                                        <th>{{trans('plan.plan')}}</th>
                                        <th>{{trans('currency.currency')}}</th>
                                        <th>{{trans('plan.payment_frequency')}}</th>
                                        <th>{{trans('instance.created_at')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="instance in instances.data">
                                        <td v-text="instance.email"></td>
                                        <td v-text="instance.domain"></td>
                                        <td>
                                            <span v-for="status in getInstanceStatus(instance)" :class="['label','label-'+status.color,'m-r-5']">{{status.label}}</span>
                                        </td>
                                        <td v-text="instance.plan.name"></td>
                                        <td v-text="instance.currency.name"></td>
                                        <td v-text="trans('list.'+instance.frequency)"></td>
                                        <td>{{instance.created_at | moment }}</td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <router-link :to="`/instance/${instance.id}`" v-if="hasPermission('list-instance')" class="btn btn-info btn-sm" v-tooltip="trans('instance.view_instance')"><i class="fas fa-arrow-circle-right"></i></router-link>
                                                <button class="btn btn-danger btn-sm" v-if="hasPermission('delete-instance')" :key="instance.id" v-confirm="{ok: confirmDelete(instance)}" v-tooltip="trans('instance.delete_instance')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!instances.total" module="instance" title="module_info_title" description="module_info_description" icon="box"></module-info>
                        <pagination-record :page-length.sync="filterInstanceForm.page_length" :records="instances" @updateRecords="getInstances" @change.native="getInstances"></pagination-record>
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
                instances: {
                    total: 0,
                    data: []
                },
                filterInstanceForm: {
                    email: '',
                    domain: '',
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
            this.getInstances();
        },
        methods: {
            getInstances(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterInstanceForm);
                axios.get('/api/instance?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.instances = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getInstanceStatus(instance){
                return helper.getInstanceStatus(instance);
            },
            confirmDelete(instance){
                return dialog => this.deleteInstance(instance);
            },
            deleteInstance(instance){
                axios.delete('/api/instance/'+instance.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getInstances();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            }
        },
        filters: {
          moment(date) {
            return helper.formatDateTime(date);
          }
        },
        watch: {
            filterInstanceForm: {
                handler(val){
                    this.getInstances();
                },
                deep: true
            }
        }
    }
</script>
