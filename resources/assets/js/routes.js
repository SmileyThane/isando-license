import VueRouter from 'vue-router'
import store from './store'

let routes = [
    {
        path: '/',                                       // all the routes which needs authentication + two factor authentication + lock screen
        component: require('./layouts/default-page'),
       //  meta: { validate: ['auth','two_factor','lock_screen','valid_license'] },
        meta: { validate: ['auth','two_factor','lock_screen'] },
        children: [
            {
                path: '/',
                component: require('./views/pages/home')
            },
            {
                path: '/home',
                component: require('./views/pages/home')
            },
            {
                path: '/about',
                component: require('./views/pages/about')
            },
            {
                path: '/update',
                component: require('./views/pages/update')
            },
            {
                path: '/support',
                component: require('./views/pages/support')
            },
            {
                path: '/profile',
                component: require('./views/pages/profile')
            },
            {
                path: '/change-password',
                component: require('./views/pages/change-password')
            },
            {
                path: '/blank',
                component: require('./views/pages/blank')
            },
            {
                path: '/configuration',
                component: require('./views/configuration/basic/index'),
            },
            {
                path: '/configuration/logo',
                component: require('./views/configuration/logo/index'),
            },
            {
                path: '/configuration/mail',
                component: require('./views/configuration/mail/index'),
            },
            {
                path: '/backup',
                component: require('./views/backup/index')
            },
            {
                path: '/configuration/basic',
                component: require('./views/configuration/basic/index')
            },
            {
                path: '/configuration/system',
                component: require('./views/configuration/system/index')
            },
            {
                path: '/configuration/role',
                component: require('./views/configuration/role/index')
            },
            {
                path: '/configuration/authentication',
                component: require('./views/configuration/authentication/index')
            },
            {
                path: '/configuration/sms',
                component: require('./views/configuration/sms/index')
            },
            {
                path: '/configuration/instance',
                component: require('./views/configuration/instance/index')
            },
            {
                path: '/configuration/scheduled-job',
                component: require('./views/configuration/scheduled-job/index')
            },
            {
                path: '/configuration/ip-filter',
                component: require('./views/configuration/ip-filter/index')
            },
            {
                path: '/configuration/ip-filter/:id/edit',
                component: require('./views/configuration/ip-filter/edit')
            },
            {
                path: '/configuration/permission',
                component: require('./views/configuration/permission/index')
            },
            {
                path: '/configuration/permission/assign',
                component: require('./views/configuration/permission/assign')
            },
            {
                path: '/configuration/locale',
                component: require('./views/configuration/locale/index')
            },
            {
                path: '/configuration/locale/:id/edit',
                component: require('./views/configuration/locale/edit')
            },
            {
                path: '/configuration/locale/:locale',
                component: require('./views/configuration/locale/view')
            },
            {
                path: '/configuration/locale/:locale/:module',
                component: require('./views/configuration/locale/view')
            },
            {
                path: '/email-template',
                component: require('./views/email-template/index')
            },
            {
                path: '/email-template/:id/edit',
                component: require('./views/email-template/edit')
            },
            {
                path: '/email-log',
                component: require('./views/email-log/index')
            },
            {
                path: '/activity-log',
                component: require('./views/activity-log/index')
            },
            {
                path: '/todo',
                component: require('./views/todo/index')
            },
            {
                path: '/todo/:id/edit',
                component: require('./views/todo/edit')
            },
            {
                path: '/user',
                component: require('./views/user/index')
            },
            {
                path: '/user/:id',
                component: require('./views/user/basic')
            },
            {
                path: '/user/:id/basic',
                component: require('./views/user/basic')
            },
            {
                path: '/user/:id/contact',
                component: require('./views/user/contact')
            },
            {
                path: '/user/:id/avatar',
                component: require('./views/user/avatar')
            },
            {
                path: '/user/:id/social',
                component: require('./views/user/social')
            },
            {
                path: '/user/:id/password',
                component: require('./views/user/password')
            },
            {
                path: '/user/:id/email',
                component: require('./views/user/email')
            },
            {
                path: '/message',
                component: require('./views/message/index')
            },
            {
                path: '/message/compose',
                component: require('./views/message/index')
            },
            {
                path: '/message/inbox',
                component: require('./views/message/inbox')
            },
            {
                path: '/message/sent',
                component: require('./views/message/sent')
            },
            {
                path: '/message/important',
                component: require('./views/message/important')
            },
            {
                path: '/message/trash',
                component: require('./views/message/trash')
            },
            {
                path: '/message/draft',
                component: require('./views/message/draft')
            },
            {
                path: '/message/:uuid/draft',
                component: require('./views/message/edit-draft')
            },
            {
                path: '/message/:uuid',
                component: require('./views/message/view')
            },
            {
                path: '/currency',
                component: require('./views/currency/index')
            },
            {
                path: '/currency/:id/edit',
                component: require('./views/currency/edit')
            },
            {
                path: '/plan',
                component: require('./views/plan/index')
            },
            {
                path: '/plan/:id/edit',
                component: require('./views/plan/edit')
            },
            {
                path: '/configuration/server',
                component: require('./views/configuration/server/index')
            },
            {
                path: '/configuration/payment',
                component: require('./views/configuration/payment/index')
            },
            {
                path: '/query',
                component: require('./views/query/index')
            },
            {
                path: '/subscriber',
                component: require('./views/subscriber/index')
            },
            {
                path: '/configuration/frontend',
                component: require('./views/configuration/frontend/index')
            },
            {
                path: '/instance',
                component: require('./views/instance/index')
            },
            {
                path: '/instance/:id',
                component: require('./views/instance/view')
            },
            {
                path: '/subscription',
                component: require('./views/subscription/index')
            }
        ]
    },
    /*{
        path: '/',                                       // all the routes which needs authentication + two factor authentication + lock screen
        component: require('./layouts/default-page'),
        meta: { validate: ['auth','two_factor','lock_screen'] },
        children: [
            {
                path: '/license',
                component: require('./views/license/index'),
            },
        ]
    },*/
    {
        path: '/',
        component: require('./layouts/guest-page'),
        meta: { validate: ['auth'] },
        children: [
            {
                path: '/auth/security',
                component: require('./views/auth/security'),
            },
            {
                path: '/auth/lock',
                component: require('./views/auth/lock'),
            },
        ]
    },
    {
        path: '/',                      // all the routes which can be access without authentication
        component: require('./layouts/guest-page'),
        meta: { validate: ['guest'] },
        children: [
            {
                path: '/',
                component: require('./views/auth/login')
            },
            {
                path: '/login',
                component: require('./views/auth/login')
            },
            {
                path: '/password',
                component: require('./views/auth/password')
            },
            {
                path: '/password/reset/:token',
                component: require('./views/auth/reset')
            },
            {
                path: '/install',
                component: require('./views/install/index')
            }
        ]
    },
    {
        path: '/',
        component : require('./layouts/guest-page'),
        children: [
            {
                path: '/terms-and-conditions',
                component: require('./views/pages/terms-and-conditions')
            }
        ]
    },
    {
        path: '/',
        component : require('./layouts/error-page'),
        children: [
            {
                path: '/terms-and-conditions',
                component: require('./views/pages/terms-and-conditions')
            },
            {
                path: '/ip-restricted',
                component: require('./views/errors/ip-restricted')
            },
            {
                path: '/maintenance',
                component: require('./views/errors/maintenance')
            }
        ]
    },
    {
        path: '*',
        component : require('./layouts/error-page'),
        children: [
            {
                path: '*',
                component: require('./views/errors/page-not-found')
            }
        ]
    }
];

const router = new VueRouter({
    routes,
    linkActiveClass: 'active',
    mode: 'history',
    scrollBehavior (to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    }
});

router.beforeEach((to, from, next) => {
    // For force logout
    // store.dispatch('resetAuthUserDetail');

    helper.check()
        .then(response => {
            
            console.log("check()");
            
            // Initialize toastr notification
            helper.notification();
            if(helper.getConfig('failed_install') && to.fullPath != '/install')
                return next({ path: '/install' })

            // Check for IP Restriction; If restricted IP found, redirect to "/ip-restricted" route
            if(helper.getConfig('ip_filter') && localStorage.getItem('ip_restricted') && to.fullPath != '/ip-restricted')
                return next({ path: '/ip-restricted' })

            // Check for Maintenance mode; If maintenance mode is on, redirect to "/maintenance" route
            if(helper.isAuth() && !helper.hasRole('admin') && helper.getConfig('maintenance_mode') && to.fullPath != '/maintenance')
                return next({ path: '/maintenance' })

            if (to.matched.some(m => m.meta.validate)){
                const m = to.matched.find(m => m.meta.validate);

                // Check for authentication; If no, redirect to "/login" route
                if (m.meta.validate.indexOf('auth') > -1){
                    if(!helper.isAuth()){
                        toastr.error(i18n.auth.auth_required);
                        return next({ path: '/login' })
                    }
                }

                // Check for two factor security; If enabled, redirect to "/auth/security" route after login
                if (m.meta.validate.indexOf('two_factor') > -1){
                    if(helper.getConfig('two_factor_security') && helper.getTwoFactorCode()){
                        return next({ path: '/auth/security' })
                    }
                }

                // Check for screen lock; If enabled, redirect to "/auth/lock" route after screen lock timeout
                if (m.meta.validate.indexOf('lock_screen') > -1){
                    if(helper.getConfig('lock_screen') && helper.isScreenLocked()){
                        return next({ path: '/auth/lock' })
                    }
                }

                // Check for screen lock; If enabled, redirect to "/auth/lock" route after screen lock timeout
                /* if (m.meta.validate.indexOf('valid_license') > -1){
                    if(!helper.getConfig('l')){
                        toastr.error(i18n.license.invalid_license);
                        return next({ path: '/license' })
                    }
                }*/

                // Check for authentication; If authenticated, redirect to "/home" route
                if (m.meta.validate.indexOf('guest') > -1){
                    if(helper.isAuth()){
                        toastr.error(i18n.auth.guest_required);
                        return next({ path: '/home' })
                    }
                }
            }

        return next()
    })
    .catch(error => {
        console.log("check() Error");
                // Authentication check fail, redirected back to "/login" route
        store.dispatch('resetAuthUserDetail');
        // return next({ path: '/login' })
        return false
    });
});

export default router;
