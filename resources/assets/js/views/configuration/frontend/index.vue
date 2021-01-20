<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.frontend_configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('configuration.frontend_configuration')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{trans('configuration.frontend_configuration')}}</h4>
                        <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.website')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.website" name="website" :placeholder="trans('configuration.website')">
                                        <show-error :form-name="configForm" prop-name="website"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.admin_email')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.admin_email" name="admin_email" :placeholder="trans('configuration.admin_email')">
                                        <show-error :form-name="configForm" prop-name="admin_email"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.support_email')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.support_email" name="support_email" :placeholder="trans('configuration.support_email')">
                                        <show-error :form-name="configForm" prop-name="support_email"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.skype_account')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.skype_account" name="skype_account" :placeholder="trans('configuration.skype_account')">
                                        <show-error :form-name="configForm" prop-name="skype_account"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.twitter_account')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.twitter_account" name="twitter_account" :placeholder="trans('configuration.twitter_account')">
                                        <show-error :form-name="configForm" prop-name="twitter_account"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.facebook_account')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.facebook_account" name="facebook_account" :placeholder="trans('configuration.facebook_account')">
                                        <show-error :form-name="configForm" prop-name="facebook_account"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.github_account')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.github_account" name="github_account" :placeholder="trans('configuration.github_account')">
                                        <show-error :form-name="configForm" prop-name="github_account"></show-error>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.enable_trial_period')}}</label>
                                        <div>
                                            <switches class="" v-model="configForm.enable_trial_period" name="enable_trial_period" theme="bootstrap" color="success"></switches>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group" v-if="configForm.enable_trial_period">
                                        <label for="">{{trans('configuration.trial_period_days')}}</label>
                                        <input class="form-control" type="text" value="" v-model="configForm.trial_period_days" name="trial_period_days" :placeholder="trans('configuration.trial_period_days')">
                                        <show-error :form-name="configForm" prop-name="trial_period_days"></show-error>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.website_maintenance_mode')}}</label>
                                        <div>
                                            <switches class="" v-model="configForm.website_maintenance_mode" name="website_maintenance_mode" theme="bootstrap" color="success"></switches>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4" v-if="configForm.website_maintenance_mode">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.website_maintenance_mode_message')}}</label>
                                        <autosize-textarea rows="2" name="website_maintenance_mode_message" :placeholder="trans('configuration.website_maintenance_mode_message')" v-model="configForm.website_maintenance_mode_message"></autosize-textarea>
                                        <show-error :form-name="configForm" prop-name="website_maintenance_mode_message"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4" v-if="configForm.website_maintenance_mode">
                                    <div class="form-group">
                                        <label for="">{{trans('configuration.website_maintenance_exclude_ip')}} <show-tip module="configuration" tip="tip_website_maintenance_exclude_ip" type="field"></show-tip></label>
                                        <autosize-textarea rows="2" name="website_maintenance_exclude_ip" :placeholder="trans('configuration.website_maintenance_exclude_ip')" v-model="configForm.website_maintenance_exclude_ip"></autosize-textarea>
                                        <show-error :form-name="configForm" prop-name="website_maintenance_exclude_ip"></show-error>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('configuration.unavailable_sub_domain')}} <show-tip module="configuration" tip="tip_unavailable_sub_domain" type="field"></show-tip></label>
                                <autosize-textarea rows="2" name="unavailable_sub_domain" :placeholder="trans('configuration.unavailable_sub_domain')" v-model="configForm.unavailable_sub_domain"></autosize-textarea>
                                <show-error :form-name="configForm" prop-name="unavailable_sub_domain"></show-error>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('configuration.tnc')}}</label>
                                <html-editor name="website_tnc" :model.sync="configForm.website_tnc" :isUpdate="updateTnc" @clearErrors="configForm.errors.clear('website_tnc')"></html-editor>
                                <show-error :form-name="configForm" prop-name="website_tnc"></show-error>
                            </div>
                            <div class="form-group">
                                <label for="">{{trans('configuration.privacy_policy')}}</label>
                                <html-editor name="website_privacy_policy" :model.sync="configForm.website_privacy_policy" :isUpdate="updatePp" @clearErrors="configForm.errors.clear('website_privacy_policy')"></html-editor>
                                <show-error :form-name="configForm" prop-name="website_privacy_policy"></show-error>
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
    import autosizeTextarea from '../../../components/autosize-textarea'

    export default {
        components : { switches,htmlEditor,autosizeTextarea },
        data() {
            return {
                configForm: new Form({
                    config_type: '',
                    website: '',
                    admin_email: '',
                    support_email: '',
                    skype_account: '',
                    twitter_account: '',
                    facebook_account: '',
                    github_account: '',
                    website_tnc: '',
                    website_privacy_policy: '',
                    enable_trial_period: false,
                    trial_period_days: 0,
                    website_maintenance_mode: false,
                    website_maintenance_mode_message: '',
                    website_maintenance_exclude_ip: '',
                    unavailable_sub_domain: '',
                    subscription_tnc: ''
                },false),
                updateTnc: false,
                updatePp: false
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
                    this.updatePp = true;
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            submit(){
                this.configForm.config_type = 'frontend';
                this.configForm.enable_trial_period = (this.configForm.enable_trial_period) ? 1 : 0;
                this.configForm.website_maintenance_mode = (this.configForm.website_maintenance_mode) ? 1 : 0;
                this.configForm.trial_period_days = (this.configForm.enable_trial_period) ? this.configForm.trial_period_days : 0;
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
