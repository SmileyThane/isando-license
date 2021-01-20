<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration/basic">{{trans('configuration.configuration')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('auth.authentication')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="authentication"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <h4 class="card-title">{{trans('auth.authentication')}}</h4>
                                <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('auth.token_lifetime')+' '+trans('auth.in_minute')}} <show-tip module="auth" tip="tip_token_lifetime" type="field"></show-tip></label>
                                                        <input class="form-control" type="number" value="" v-model="configForm.token_lifetime" name="token_lifetime" :placeholder="trans('auth.token_lifetime')">
                                                        <show-error :form-name="configForm" prop-name="token_lifetime"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for=""><small>{{trans('auth.reset_password_token_lifetime')+' '+trans('auth.in_minute')}}</small> <show-tip module="auth" tip="tip_reset_password_token_lifetime" type="field"></show-tip></label>
                                                        <input class="form-control" type="number" value="" v-model="configForm.reset_password_token_lifetime" name="reset_password_token_lifetime" :placeholder="trans('auth.reset_password_token_lifetime')">
                                                        <show-error :form-name="configForm" prop-name="reset_password_token_lifetime"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('auth.reset_password')}} <show-tip module="auth" tip="tip_reset_password" type="field"></show-tip></label>
                                                        <div>
                                                            <switches class="" v-model="configForm.reset_password" theme="bootstrap" color="success"></switches>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('auth.two_factor_security')}} <show-tip module="auth" tip="tip_two_factor_security" type="field"></show-tip></label>
                                                        <div>
                                                            <switches class="" v-model="configForm.two_factor_security" theme="bootstrap" color="success"></switches>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('auth.lock_screen')}} <show-tip module="auth" tip="tip_lock_screen" type="field"></show-tip></label>
                                                        <div>
                                                            <switches class="" v-model="configForm.lock_screen" theme="bootstrap" color="success"></switches>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group" v-if="configForm.lock_screen">
                                                        <label for="">{{trans('auth.lock_screen_timeout')+' '+trans('auth.in_minute')}} <show-tip module="auth" tip="tip_lock_screen_timeout" type="field"></show-tip></label>
                                                        <input class="form-control" type="number" value="" v-model="configForm.lock_screen_timeout" name="lock_screen_timeout" :placeholder="trans('auth.lock_screen_timeout')">
                                                        <show-error :form-name="configForm" prop-name="lock_screen_timeout"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('auth.login_throttle')}} <show-tip module="auth" tip="tip_login_throttle" type="field"></show-tip></label>
                                                        <div>
                                                            <switches class="" v-model="configForm.login_throttle" theme="bootstrap" color="success"></switches>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                </div>
                                            </div>
                                            <div v-if="configForm.login_throttle">
                                                <div class="row">
                                                    <div class="col-12 col-md-6">
                                                        <div class="form-group">
                                                            <label for="">{{trans('auth.login_throttle_attempt')}} <show-tip module="auth" tip="tip_login_throttle_attempt" type="field"></show-tip></label>
                                                            <input class="form-control" type="number" value="" v-model="configForm.login_throttle_attempt" name="login_throttle_attempt" :placeholder="trans('auth.login_throttle_attempt')">
                                                            <show-error :form-name="configForm" prop-name="login_throttle_attempt"></show-error>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="form-group">
                                                            <label for="">{{trans('auth.login_throttle_timeout')+' '+trans('auth.in_minute')}} <show-tip module="auth" tip="tip_login_throttle_timeout" type="field"></show-tip></label>
                                                            <input class="form-control" type="number" value="" v-model="configForm.login_throttle_timeout" name="login_throttle_timeout" :placeholder="trans('auth.login_throttle_timeout')">
                                                            <show-error :form-name="configForm" prop-name="login_throttle_timeout"></show-error>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-info waves-effect waves-light m-t-10 pull-right">{{trans('general.save')}}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import configurationSidebar from '../system-config-sidebar'
    import switches from 'vue-switches'

    export default {
        components : { configurationSidebar,switches },
        data() {
            return {
                configForm: new Form({
                    config_type: '',
                    token_lifetime: '',
                    reset_password_token_lifetime: '',
                    reset_password: 0,
                    two_factor_security: 0,
                    lock_screen: 0,
                    lock_screen_timeout: '',
                    login_throttle: 0,
                    login_throttle_attempt: '',
                    login_throttle_timeout: '',
                    providers: []
                },false)
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
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            submit(){
                this.configForm.config_type = 'authentication';
                this.configForm.reset_password = (this.configForm.reset_password) ? 1 : 0;
                this.configForm.two_factor_security = (this.configForm.two_factor_security) ? 1 : 0;
                this.configForm.lock_screen = (this.configForm.lock_screen) ? 1 : 0;
                this.configForm.login_throttle = (this.configForm.login_throttle) ? 1 : 0;
                this.configForm.post('/api/configuration')
                    .then(response => {
                        this.$store.dispatch('setConfig',this.configForm);
                        toastr.success(response.message);
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getConfig(config){
                return helper.getConfig(config);
            }
        }
    }
</script>
