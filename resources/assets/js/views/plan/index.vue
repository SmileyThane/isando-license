<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('plan.plan')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('plan.plan')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12">
                <transition name="fade" v-if="hasPermission('create-plan')">
                    <div class="card" v-if="showCreatePanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showCreatePanel" @click="showCreatePanel = !showCreatePanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <plan-form @completed="getPlans"></plan-form>
                        </div>
                    </div>
                </transition>
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                                <button class="btn btn-info btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.hide')}}</button>
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('plan.name')}}</label>
                                        <input class="form-control" name="name" v-model="filterPlanForm.name">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.sort_by')}}</label>
                                        <select name="order" class="form-control" v-model="filterPlanForm.sort_by">
                                            <option value="name">{{trans('plan.name')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="form-group">
                                        <label for="">{{trans('general.order')}}</label>
                                        <select name="order" class="form-control" v-model="filterPlanForm.order">
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
                        <button class="btn btn-info btn-sm pull-right" v-if="!showCreatePanel && hasPermission('create-plan')" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> {{trans('general.add_new')}}</button>
                        <button class="btn btn-info btn-sm pull-right m-r-10" v-if="!showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> {{trans('general.filter')}}</button>
                        <h4 class="card-title">{{trans('plan.plan_list')}}</h4>
                        <h6 class="card-subtitle" v-if="plans">{{trans('general.total_result_found',{'count' : plans.total})}}</h6>
                        <h6 class="card-subtitle" v-else>{{trans('general.no_result_found')}}</h6>
                        <div class="table-responsive" v-if="hasPermission('list-plan') && plans.total">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{trans('plan.name')}}</th>
                                        <th>{{trans('plan.status')}}</th>
                                        <th>Max Users</th>
                                       <!-- <th>{{trans('plan.max_customer')}}</th>
                                        <th>{{trans('plan.max_document')}}</th> -->
                                        <th>{{trans('plan.price')}}</th>
                                        <th>{{trans('plan.description')}}</th>
                                        <th class="table-option">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="plan in plans.data">
                                        <td v-html="plan.name+'('+plan.code+')'+getDefault(plan)+getFeatured(plan)"></td>
                                        <td v-html="getStatus(plan)"></td>
                                        <td v-text="plan.max_customer"></td>
                                        <!-- <td v-text="plan.max_document"></td> -->
                                        <td>
                                            <ul style="list-style:none;padding:0">
                                                <li v-for="plan_price in plan.plan_price">
                                                    {{trans('list.'+plan_price.frequency)}} {{formatCurrency(plan_price.amount,plan_price.currency)}}
                                                </li>
                                            </ul>
                                        </td>
                                        <td v-html="nl2br(plan.description)"></td>
                                        <td class="table-option">
                                            <div class="btn-group">
                                                <button class="btn btn-info btn-sm" v-if="hasPermission('edit-plan')" v-tooltip="trans('plan.edit_plan')" @click.prevent="editPlan(plan)"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" :key="plan.id" v-if="hasPermission('delete-plan')" v-confirm="{ok: confirmDelete(plan)}" v-tooltip="trans('plan.delete_plan')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!plans.total" module="plan" title="module_info_title" description="module_info_description" icon="box"></module-info>
                        <pagination-record :page-length.sync="filterPlanForm.page_length" :records="plans" @updateRecords="getPlans" @change.native="getPlans"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import planForm from './form'

    export default {
        components : { planForm },
        data() {
            return {
                plans: {
                    total: 0,
                    data: []
                },
                filterPlanForm: {
                    name: '',
                    sort_by : 'name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                showFilterPanel: false,
                showCreatePanel: false
            };
        },
        mounted(){
            this.getPlans();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getPlans(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterPlanForm);
                axios.get('/api/plan?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.plans = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editPlan(plan){
                this.$router.push('/plan/'+plan.id+'/edit');
            },
            confirmDelete(plan){
                return dialog => this.deletePlan(plan);
            },
            deletePlan(plan){
                axios.delete('/api/plan/'+plan.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getPlans();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getConfig(config){
                return helper.getConfig(config);
            },
            getStatus(plan){
                return plan.is_active ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
            },
            getDefault(plan){
                return plan.is_default ? ' <span class="badge badge-success">'+i18n.plan.default+'</span>' : '';
            },
            getFeatured(plan){
                return plan.is_featured ? ' <span class="badge badge-info">'+i18n.plan.featured+'</span>' : '';
            },
            nl2br(str){
                return helper.nl2br(str);
            },
            formatCurrency(amount, currency){
                return helper.formatCurrency(amount, currency);
            }
        },
        watch: {
            filterPlanForm: {
                handler(val){
                    this.getPlans();
                },
                deep: true
            }
        }
    }
</script>
