<template>
	<aside class="left-sidebar">
        <div class="scroll-sidebar">
           <!-- <div class="user-profile">
                <div class="profile-img"> <img :src="getAuthUser('avatar')" alt="user" /> </div>
            </div>-->
            <nav class="sidebar-nav m-t-20">
                <div class="text-center" v-if="getConfig('maintenance_mode')"><span class="badge badge-danger m-b-10">{{trans('configuration.under_maintenance')}}</span></div>
                <div class="text-center" v-if="!getConfig('mode')"><span class="badge badge-danger m-b-10">{{trans('configuration.test_mode')}}</span></div>
                <ul id="sidebarnav">
                    <li><router-link to="/home" exact><i class="fas fa-home fa-fw"></i> <span class="hide-menu">{{trans('general.home')}}</span></router-link></li>
                    <li v-if="hasPermission('access-configuration')"><router-link to="/instance" exact><i class="fas fa-box fa-fw"></i> <span class="hide-menu"><!-- {{trans('instance.instance')}}-->Licenses</span></router-link></li>
                    <li v-if="hasPermission('access-configuration')"><router-link to="/subscription" exact><i class="fas fa-credit-card fa-fw"></i> <span class="hide-menu">Payments<!-- {{trans('subscription.subscription')}} --></span></router-link></li>
                    <li>
                    <a class="has-arrow" href="#" aria-expanded="false"><i class="fas fa-cogs fa-fw"></i> <span class="hide-menu">Management</span></a> 
                        <ul aria-expanded="false" class="collapse">
                            <li><router-link to="/user" exact><i class="fas fa-users fa-fw"></i> <span class="hide-menu">Users <!--{{trans('user.user')}}--></span></router-link></li>
                    <li v-if="hasPermission('access-configuration')"><router-link to="/currency" exact><i class="fas fa-money-bill-alt fa-fw"></i> <span class="hide-menu">Currencies <!--{{trans('currency.currency')}}--></span></router-link></li>
                    <li v-if="hasPermission('access-configuration')"><router-link to="/plan" exact><i class="fas fa-boxes fa-fw"></i> <span class="hide-menu">Plans <!--{{trans('plan.plan')}}--></span></router-link></li>                             
        </ul>
                    </li>
                    <!--  <li v-if="hasPermission('access-configuration')"><router-link to="/subscriber" exact><i class="fas fa-bullhorn fa-fw"></i> <span class="hide-menu">{{trans('subscriber.subscriber')}}</span></router-link></li>
                    <li v-if="hasPermission('access-configuration')"><router-link to="/query" exact><i class="fas fa-question-circle fa-fw"></i> <span class="hide-menu">{{trans('query.query')}}</span></router-link></li>-->
                </ul>
            </nav>
        </div>
        <div class="sidebar-footer">
            <!-- <router-link v-if="hasPermission('access-configuration')" to="/configuration" class="link" v-tooltip="trans('configuration.configuration')"><i class="fas fa-cogs"></i></router-link>-->
            <router-link to="/profile" class="link" v-tooltip="trans('user.profile')"><i class="fas fa-user"></i></router-link>
            <a href="#" class="link" v-tooltip="trans('auth.logout')" @click.prevent="logout"><i class="fas fa-power-off"></i></a>
        </div>
    </aside>
</template>

<script>
    export default {
        mounted() {
        },
        methods : {
            logout(){
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login');
                })
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getConfig(config){
                return helper.getConfig(config);
            }
        },
        computed: {
        }
    }
</script>
