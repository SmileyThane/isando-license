<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{trans('configuration.server_configuration')}}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><router-link to="/home">{{trans('general.home')}}</router-link></li>
                    <li class="breadcrumb-item active">{{trans('configuration.server_configuration')}}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                            <h4 class="card-title">{{trans('configuration.server_configuration')}}</h4>
                            <div class="form-group">
                                <label for="">Choose Server</label>
                                <select v-model="configForm.server" class="custom-select col-12" @change="configForm.errors.clear('server')" name="server">
                                  <option value="">{{trans('general.select_one')}}</option>
                                  <option value="local">Local</option>
                                  <option value="cpanel">cPanel</option>
                                  <option value="plesk">Plesk</option>
                                </select>
                                <show-error :form-name="configForm" prop-name="server"></show-error>
                            </div>
                            <div class="row" v-if="configForm.server === 'cpanel'">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">cPanel Host</label>
                                        <input class="form-control" type="text" v-model="configForm.cpanel_host" name="cpanel_host" placeholder="cPanel Host">
                                        <show-error :form-name="configForm" prop-name="cpanel_host"></show-error>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">cPanel Port</label>
                                        <input class="form-control" type="text" v-model="configForm.cpanel_port" name="cpanel_port" placeholder="cPanel Port">
                                        <show-error :form-name="configForm" prop-name="cpanel_port"></show-error>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">cPanel Username</label>
                                        <input class="form-control" type="text" v-model="configForm.cpanel_username" name="cpanel_username" placeholder="cPanel Username">
                                        <show-error :form-name="configForm" prop-name="cpanel_username"></show-error>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">cPanel Password</label>
                                        <input class="form-control" type="text" v-model="configForm.cpanel_password" name="cpanel_password" placeholder="cPanel Password">
                                        <show-error :form-name="configForm" prop-name="cpanel_password"></show-error>
                                    </div>
                                </div>
                            </div>
                            <div class="row" v-if="configForm.server === 'plesk'">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">Plesk Host</label>
                                        <input class="form-control" type="text" v-model="configForm.plesk_host" name="plesk_host" placeholder="Plesk Host">
                                        <show-error :form-name="configForm" prop-name="plesk_host"></show-error>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">Plesk Username</label>
                                        <input class="form-control" type="text" v-model="configForm.plesk_username" name="plesk_username" placeholder="Plesk Username">
                                        <show-error :form-name="configForm" prop-name="plesk_username"></show-error>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">Plesk Password</label>
                                        <input class="form-control" type="text" v-model="configForm.plesk_password" name="plesk_password" placeholder="Plesk Password">
                                        <show-error :form-name="configForm" prop-name="plesk_password"></show-error>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">Plesk Webspace Id</label>
                                        <input class="form-control" type="text" v-model="configForm.plesk_webspace_id" name="plesk_webspace_id" placeholder="Plesk Webspace Id">
                                        <show-error :form-name="configForm" prop-name="plesk_webspace_id"></show-error>
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
</template>


<script>
    import switches from 'vue-switches'

    export default {
        components : { switches },
        data() {
            return {
                configForm: new Form({
                    config_type: '',
                    server: null,
                    cpanel_host: 0,
                    cpanel_port: 0,
                    cpanel_username: '',
                    cpanel_password: '',
                    plesk_host: '',
                    plesk_password: '',
                    plesk_username: '',
                    plesk_webspace_id: ''
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
                this.configForm.config_type = 'payment_gateway';
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
