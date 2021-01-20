<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item"><router-link to="/configuration/basic">{{trans('configuration.configuration')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('instance.instance')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="instance"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                    <div class="row">
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.instance_maintenance_mode')}}</label>
                                                <div>
                                                    <switches class="" v-model="configForm.instance_maintenance_mode" name="instance_maintenance_mode" theme="bootstrap" color="success"></switches>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4" v-if="configForm.instance_maintenance_mode">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.instance_maintenance_mode_message')}}</label>
                                                <autosize-textarea rows="2" name="instance_maintenance_mode_message" :placeholder="trans('configuration.instance_maintenance_mode_message')" v-model="configForm.instance_maintenance_mode_message"></autosize-textarea>
                                                <show-error :form-name="configForm" prop-name="instance_maintenance_mode_message"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4" v-if="configForm.instance_maintenance_mode">
                                            <div class="form-group">
                                                <label for="">{{trans('configuration.instance_maintenance_exclude_ip')}} <show-tip module="configuration" tip="tip_instance_maintenance_exclude_ip" type="field"></show-tip></label>
                                                <autosize-textarea rows="2" name="instance_maintenance_exclude_ip" :placeholder="trans('configuration.instance_maintenance_exclude_ip')" v-model="configForm.instance_maintenance_exclude_ip"></autosize-textarea>
                                                <show-error :form-name="configForm" prop-name="instance_maintenance_exclude_ip"></show-error>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-info waves-effect waves-light pull-right m-t-10">{{trans('general.save')}}</button>
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
    import autosizeTextarea from '../../../components/autosize-textarea'

    export default {
        components : { configurationSidebar,switches,autosizeTextarea },
        data() {
            return {
                configForm: new Form({
                    config_type: '',
                    instance_maintenance_mode: '',
                    instance_maintenance_mode_message: '',
                    instance_maintenance_exclude_ip: ''
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
                this.configForm.config_type = 'instance';
                this.configForm.instance_maintenance_mode = (this.configForm.instance_maintenance_mode) ? 1 : 0;
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
