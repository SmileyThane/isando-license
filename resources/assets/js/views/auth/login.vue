<template>
    <section id="wrapper">
        <div class="login-register" style="background-image:url(/images/background.jpg);">
            <div class="login-box card">
                <div class="card-body">
                    <form class="form-horizontal form-material" id="loginform" @submit.prevent="submit" @keydown="loginForm.errors.clear($event.target.name)">
                        <h3 class="box-title m-b-20">{{trans('auth.login')}}</h3>
                        <div class="form-group ">
                            <input type="text" name="email" class="form-control" :placeholder="trans('auth.email')" v-model="loginForm.email">
                            <show-error :form-name="loginForm" prop-name="email"></show-error>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" :placeholder="trans('auth.password')" v-model="loginForm.password">
                            <show-error :form-name="loginForm" prop-name="password"></show-error>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">{{trans('auth.sign_in')}}</button>
                        </div>
                        <!-- <div class="form-group m-b-0">
                            <div class="col-sm-12 text-center">
                                <p v-if="getConfig('reset_password')">{{trans('auth.forgot_your_password?')}} <router-link to="/password" class="text-info m-l-5"><b>{{trans('auth.reset_here!')}}</b></router-link></p>
                            </div>
                        </div> -->
                    </form>
                </div>
                <guest-footer></guest-footer>
            </div>
        </div>

    </section>
</template>

<script>
    import guestFooter from '../../layouts/guest-footer.vue'

    export default {
        data() {
            return {
                loginForm: new Form({
                    email: '',
                    password: ''
                })
            }
        },
        components: {
            guestFooter
        },
        mounted(){
        },
        methods: {
            submit(){
                this.loginForm.post('/api/auth/login')
                    .then(response =>  {
                        localStorage.setItem('auth_token',response.token);
                        axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');
                        this.$store.dispatch('setAuthStatus');
                        this.$store.dispatch('setLastActivity');
                        toastr.success(response.message);

                        if(helper.getConfig('two_factor_security') && response.two_factor_code){
                            this.$store.dispatch('setTwoFactorCode',response.two_factor_code);
                            this.$router.push('/auth/security');
                        }
                        else {
                            this.$store.dispatch('resetTwoFactorCode');
                            this.$router.push('/home');
                        }
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
