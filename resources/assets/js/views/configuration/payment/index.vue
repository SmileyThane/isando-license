<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.payment_configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('configuration.payment_configuration')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                            <h4 class="card-title">{{trans('configuration.payment_configuration')}}</h4>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="">Paypal</label>
                                            <div>
                                                <switches class="" v-model="configForm.subscription_paypal" name="subscription_paypal" theme="bootstrap" color="success"></switches>
                                            </div>
                                        </div>
                                        <div v-if="configForm.subscription_paypal">
                                            <div class="form-group">
                                                <label for="">Paypal Mode</label>
                                                <div>
                                                    <switches class="" v-model="configForm.subscription_paypal_mode" name="subscription_paypal_mode" theme="bootstrap" color="success"></switches>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Paypal Client Id</label>
                                                <input class="form-control" type="text" v-model="configForm.subscription_paypal_client_id" name="subscription_paypal_client_id" placeholder="Paypal Client Id">
                                                <show-error :form-name="configForm" prop-name="subscription_paypal_client_id"></show-error>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Paypal Client Secret</label>
                                                <input class="form-control" type="text" v-model="configForm.subscription_paypal_client_secret" name="subscription_paypal_client_secret" placeholder="Paypal Client Secret">
                                                <show-error :form-name="configForm" prop-name="subscription_paypal_client_secret"></show-error>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="">Stripe</label>
                                            <div>
                                                <switches class="" v-model="configForm.subscription_stripe" name="subscription_stripe" theme="bootstrap" color="success"></switches>
                                            </div>
                                        </div>
                                        <div v-if="configForm.subscription_stripe">
                                            <div class="form-group">
                                                <label for="">Stripe Mode</label>
                                                <div>
                                                    <switches class="" v-model="configForm.subscription_stripe_mode" name="subscription_stripe_mode" theme="bootstrap" color="success"></switches>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Stripe Publishable Key</label>
                                                <input class="form-control" type="text" v-model="configForm.subscription_stripe_publishable_key" name="subscription_stripe_publishable_key" placeholder="Stripe Publishable Key">
                                                <show-error :form-name="configForm" prop-name="subscription_stripe_publishable_key"></show-error>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Stripe Private Key</label>
                                                <input class="form-control" type="text" v-model="configForm.subscription_stripe_private_key" name="subscription_stripe_private_key" placeholder="Stripe Private Key">
                                                <show-error :form-name="configForm" prop-name="subscription_stripe_private_key"></show-error>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <h4 class="card-title">{{trans('configuration.invoice_configuration')}}</h4>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.subscription_invoice_prefix')}}</label>
                                        <input class="form-control" type="text" v-model="configForm.subscription_invoice_prefix" name="subscription_invoice_prefix" :placeholder="trans('configuration.subscription_invoice_prefix')">
                                        <show-error :form-name="configForm" prop-name="subscription_invoice_prefix"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.subscription_invoice_number_digit')}}</label>
                                        <input class="form-control" type="text" v-model="configForm.subscription_invoice_number_digit" name="subscription_invoice_number_digit" :placeholder="trans('configuration.subscription_invoice_number_digit')">
                                        <show-error :form-name="configForm" prop-name="subscription_invoice_number_digit"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.subscription_invoice_start_number')}}</label>
                                        <input class="form-control" type="text" v-model="configForm.subscription_invoice_start_number" name="subscription_invoice_start_number" :placeholder="trans('configuration.subscription_invoice_start_number')">
                                        <show-error :form-name="configForm" prop-name="subscription_invoice_start_number"></show-error>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('configuration.subscription_invoice_tnc')}}</label>
                                <html-editor name="subscription_invoice_tnc" :model.sync="configForm.subscription_invoice_tnc" :isUpdate="updateTnc" @clearErrors="configForm.errors.clear('subscription_invoice_tnc')"></html-editor>
                                <show-error :form-name="configForm" prop-name="subscription_invoice_tnc"></show-error>
                            </div>
                            <button type="submit" class="btn btn-info waves-effect waves-light pull-right m-t-10">{{trans('general.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import switches from 'vue-switches'
    import htmlEditor from '../../../components/html-editor'

    export default {
        components : { switches,htmlEditor },
        data() {
            return {
                configForm: new Form({
                    config_type: '',
                    subscription_paypal: 0,
                    subscription_stripe: 0,
                    subscription_paypal_mode: 0,
                    subscription_stripe_mode: 0,
                    subscription_paypal_client_id: '',
                    subscription_paypal_client_secret: '',
                    subscription_stripe_publishable_key: '',
                    subscription_stripe_private_key: '',
                    subscription_invoice_tnc: '',
                    subscription_invoice_prefix: '',
                    subscription_invoice_number_digit: '',
                    subscription_invoice_start_number: ''
                },false),
                updateTnc: false
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            axios.get('/api/configuration')
                .then(response => response.data)
                .then(response => {
                    this.configForm = helper.formAssign(this.configForm, response);
                    this.updateTnc = true;
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            submit(){
                this.configForm.config_type = 'payment_gateway';
                this.configForm.subscription_paypal = (this.configForm.subscription_paypal) ? 1 : 0;
                this.configForm.subscription_stripe = (this.configForm.subscription_stripe) ? 1 : 0;
                this.configForm.subscription_paypal_mode = (this.configForm.subscription_paypal_mode) ? 1 : 0;
                this.configForm.subscription_stripe_mode = (this.configForm.subscription_stripe_mode) ? 1 : 0;
                this.configForm.post('/api/configuration')
                    .then(response => {
                        this.$store.dispatch('setConfig',this.configForm);
                        toastr.success(response.message);
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
