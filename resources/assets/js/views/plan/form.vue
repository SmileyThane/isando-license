<template>
    <form @submit.prevent="proceed" @keydown="planForm.errors.clear($event.target.name)">
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">{{trans('plan.name')}} </label>
                            <input class="form-control" type="text" v-model="planForm.name" name="name" :placeholder="trans('plan.name')">
                            <show-error :form-name="planForm" prop-name="name"></show-error>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">{{trans('plan.code')}}</label>
                            <input class="form-control" type="text" v-model="planForm.code" name="code" :placeholder="trans('plan.code')">
                            <show-error :form-name="planForm" prop-name="code"></show-error>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                           <!-- <label for="">{{trans('plan.max_customer')}}</label>-->
                            <label for="">Max Users</label>
                            <input class="form-control" type="number" v-model="planForm.max_customer" name="max_customer" :placeholder="trans('plan.max_customer')">
                            <show-error :form-name="planForm" prop-name="max_customer"></show-error>
                        </div>
                    </div>
                   <!-- <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="">{{trans('plan.max_document')}}</label>
                            <input class="form-control" type="number" v-model="planForm.max_document" name="max_document" :placeholder="trans('plan.max_document')">
                            <show-error :form-name="planForm" prop-name="max_document"></show-error>
                        </div>
                    </div>-->
                </div>
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <switches class="m-l-20" v-model="planForm.is_active" theme="bootstrap" color="success"></switches> {{trans('plan.status')}}
                            <show-tip type="field" module="plan" tip="tip_status"></show-tip>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <switches class="m-l-20" v-model="planForm.is_default" theme="bootstrap" color="success"></switches> {{trans('plan.default')}}
                            <show-tip type="field" module="plan" tip="tip_default"></show-tip>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="form-group">
                            <switches class="m-l-20" v-model="planForm.is_featured" theme="bootstrap" color="success"></switches> {{trans('plan.featured')}}
                            <show-tip type="field" module="plan" tip="tip_featured"></show-tip>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{{trans('plan.description')}}</label>
                    <autosize-textarea rows="2" name="description" :placeholder="trans('plan.description')" v-model="planForm.description"></autosize-textarea>
                    <show-error :form-name="planForm" prop-name="description"></show-error>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div v-for="plan_price in planForm.plan_prices" class="m-t-10">
                    <h4>{{plan_price.currency.name+'('+plan_price.currency.symbol+')'}}</h4>
                    <div class="row">
                        <div class="col-12 col-sm-6" v-for="frequency in plan_price.frequencies">
                            <label for="">{{frequency.payment_frequency.name}}</label>
                            <input class="form-control" type="number" v-model="frequency.amount" :name="getAmountFieldName(plan_price,frequency)" :placeholder="trans('plan.amount')">
                            <show-error :form-name="planForm" :prop-name="getAmountFieldName(plan_price,frequency)"></show-error>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light pull-right">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/plan" class="btn btn-danger waves-effect waves-light pull-right m-r-10" v-show="id">{{trans('general.cancel')}}</router-link>
    </form>
</template>


<script>
    import switches from 'vue-switches'
    import autosizeTextarea from '../../components/autosize-textarea'

    export default {
        components: {switches, autosizeTextarea},
        data() {
            return {
                planForm: new Form({
                    name : '',
                    code : '',
                    is_active: '',
                    is_default: '',
                    is_featured: '',
                    max_customer: '',
                    max_document: '',
                    description: '',
                    plan_prices: []
                }),
                currencies: [],
                payment_frequencies: []
            };
        },
        props: ['id'],
        mounted() {
            axios.get('/api/plan/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.currencies = response.currencies;
                    this.payment_frequencies = response.payment_frequencies;

                    this.initPlanPrice();

                    if(this.id)
                        this.getPlan();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            initPlanPrice(){
                this.currencies.forEach(currency => {
                    let frequencies = [];
                    this.payment_frequencies.forEach(payment_frequency => {
                        frequencies.push({
                            payment_frequency: payment_frequency,
                            amount: ''
                        })
                    });

                    this.planForm.plan_prices.push({
                        currency: currency,
                        frequencies: frequencies
                    });
                    })
            },
            proceed(){
                if(this.id)
                    this.updatePlan();
                else
                    this.storePlan();
            },
            storePlan(){
                this.planForm.post('/api/plan')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed');
                        this.planForm.plan_prices = [];
                        this.initPlanPrice();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getPlan(){
                axios.get('/api/plan/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.planForm.name = response.name;
                        this.planForm.code = response.code;
                        this.planForm.is_active = response.is_active;
                        this.planForm.is_default = response.is_default;
                        this.planForm.is_featured = response.is_featured;
                        this.planForm.max_customer = response.max_customer;
                        this.planForm.max_document = response.max_document;
                        this.planForm.description = response.description;

                        this.planForm.plan_prices.forEach(plan_price => {
                            plan_price.frequencies.forEach(frequency => {
                              let price = response.plan_price.find(o => o.currency_id == plan_price.currency.id && o.frequency === frequency.payment_frequency.id);
                              frequency.amount = price ? helper.formatNumber(price.amount,plan_price.currency.decimal_place) : 0;
                            })
                        });
                    })
                    .catch(error => {
                        this.$router.push('/plan');
                    });
            },
            updatePlan(){
                this.planForm.patch('/api/plan/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/plan');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getAmountFieldName(plan_price,frequency){
                return 'plan_price_'+plan_price.currency.id+'_'+frequency.payment_frequency.id;
            }
        }
    }
</script>
