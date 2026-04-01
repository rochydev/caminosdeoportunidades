import { authStore } from "../store/auth";

const AuthenticatedLayout = () => import('../layouts/AdminLayout.vue');
const AuthenticatedUserLayout = () => import('../layouts/UserLayout.vue');
const AuthenticatedCompanyLayout = () => import('../layouts/CompanyLayout.vue');
const GuestLayout = () => import('../layouts/GuestLayout.vue');

async function requireLogin(to, from, next) {
    const auth = authStore();
    await auth.getUser();
    const isLogin = !!auth.authenticated;

    if (!isLogin) return next('/login');

    // Redirigir empresas a su panel
    if (hasCompany(auth.user?.roles ?? []) && !hasAdmin(auth.user?.roles ?? [])) {
        return next('/empresa');
    }
    next();
}

const hasAdmin = (roles = []) =>
    roles.some((role) => role?.name?.toLowerCase().includes('admin'));

async function guest(to, from, next) {
    const auth = authStore()
    let isLogin = !!auth.authenticated;

    if (isLogin) {
        next('/')
    } else {
        next()
    }
}

const hasCompany = (roles = []) =>
    roles.some((role) => role?.name?.toLowerCase() === 'company');

async function requireCompany(to, from, next) {
    const auth = authStore();
    await auth.getUser();
    const isLogin = !!auth.authenticated;
    const user = auth.user;

    if (isLogin) {
        if (hasCompany(user.roles) || hasAdmin(user.roles)) {
            next();
        } else {
            next('/app');
        }
    } else {
        next('/login');
    }
}

async function requireAdmin(to, from, next) {
    const auth = authStore();
    await auth.getUser();
    const isLogin = !!auth.authenticated;
    const user = auth.user;

    if (isLogin) {
        if (hasAdmin(user.roles)) {
            next();
        } else if (hasCompany(user.roles)) {
            next('/empresa');
        } else {
            next('/app');
        }
    } else {
        next('/login');
    }
}

export default [
    {
        path: '/',
        component: GuestLayout,
        children: [
            {
                path: '/',
                name: 'home',
                component: () => import('../views/public/home/index.vue'),
            },
            {
                path: 'ofertas',
                name: 'ofertas.index',
                component: () => import('../views/public/ofertas/index.vue'),
            },
            {
                path: 'ofertas/:id',
                name: 'ofertas.show',
                component: () => import('../views/public/ofertas/show.vue'),
            },

            {
                path: 'login',
                name: 'auth.login',
                component: () => import('../views/auth/login/Login.vue'),
                beforeEnter: guest,
            },
            {
                path: 'login/empresa',
                name: 'auth.login.empresa',
                component: () => import('../views/auth/login/LoginEmpresa.vue'),
                beforeEnter: guest,
            },
            {
                path: 'register',
                name: 'auth.register',
                component: () => import('../views/auth/register/index.vue'),
                beforeEnter: guest,
            },
            {
                path: 'forgot-password',
                name: 'auth.forgot-password',
                component: () => import('../views/auth/passwords/Email.vue'),
                beforeEnter: guest,
            },
            {
                path: 'reset-password/:token',
                name: 'auth.reset-password',
                component: () => import('../views/auth/passwords/Reset.vue'),
                beforeEnter: guest,
            },
        ]
    },

    {
        path: '/app',
        component: AuthenticatedUserLayout,
        name: 'app',
        beforeEnter: requireLogin,
        meta: { breadCrumb: 'Inicio' },
        children: [
            {
                name: 'app.index',
                path: '',
                component: () => import('../views/user/index.vue'),
                meta: { breadCrumb: 'Inicio', hideBreadcrumb: true },
            },
            {
                name: 'app.profile',
                path: 'profile',
                component: () => import('../views/user/profile.vue'),
                meta: { breadCrumb: 'Mi Perfil' },
            },
            {
                name: 'app.cv',
                path: 'cv',
                component: () => import('../views/user/cv.vue'),
                meta: { breadCrumb: 'Mi Currículum' },
            },
            {
                name: 'app.candidaturas',
                path: 'candidaturas',
                component: () => import('../views/user/candidaturas.vue'),
                meta: { breadCrumb: 'Mis Candidaturas' },
            },
        ]
    },


    {
        path: '/empresa',
        component: AuthenticatedCompanyLayout,
        name: 'empresa',
        beforeEnter: requireCompany,
        meta: { breadCrumb: 'Empresa' },
        children: [
            {
                name: 'empresa.index',
                path: '',
                component: () => import('../views/empresa/index.vue'),
                meta: {
                    breadCrumb: 'Dashboard',
                    hideBreadcrumb: true
                }
            },
            {
                name: 'empresa.ofertas',
                path: 'ofertas',
                component: () => import('../views/empresa/ofertas.vue'),
                meta: { breadCrumb: 'Mis Ofertas' }
            },
            {
                name: 'empresa.candidaturas',
                path: 'candidaturas',
                component: () => import('../views/empresa/candidaturas.vue'),
                meta: { breadCrumb: 'Candidaturas' }
            },
            {
                name: 'empresa.profile',
                path: 'perfil',
                component: () => import('../views/user/profile.vue'),
                meta: { breadCrumb: 'Perfil Empresa' }
            },
        ]
    },

    {
        path: '/admin',
        component: AuthenticatedLayout,
        beforeEnter: requireAdmin,
        meta: { breadCrumb: 'Dashboard' },
        children: [
            {
                name: 'admin.index',
                path: '',
                component: () => import('../views/admin/index.vue'),
                meta: {
                    breadCrumb: 'Admin',
                    hideBreadcrumb: true
                }
            },
            {
                name: 'profile.index',
                path: 'profile',
                component: () => import('../views/admin/profile/index.vue'),
                meta: { breadCrumb: 'Profile' }
            },

            {
                name: 'categories',
                path: 'categories',
                meta: { breadCrumb: 'Categories' },
                children: [
                    {
                        name: 'categories.index',
                        path: '',
                        component: () => import('../views/admin/categories/Index.vue'),
                        meta: {
                            breadCrumb: 'View category',
                            hideBreadcrumb: true
                        }
                    },
                ]
            },

            {
                name: 'permissions',
                path: 'permissions',
                meta: { breadCrumb: 'Permisos' },
                children: [
                    {
                        name: 'permissions.index',
                        path: '',
                        component: () => import('../views/admin/permissions/Index.vue'),
                        meta: {
                            breadCrumb: 'Permissions',
                            hideBreadcrumb: true
                        }
                    },
                ]
            },
            {
                name: 'users',
                path: 'users',
                meta: { breadCrumb: 'Usuarios' },
                children: [
                    {
                        name: 'users.index',
                        path: '',
                        component: () => import('../views/admin/users/Index.vue'),
                        meta: {
                            breadCrumb: 'Usuarios',
                            hideBreadcrumb: true // Ocultar breadcrumb del layout porque la Card tiene su propio header
                        }
                    },
                    {
                        name: 'users.create',
                        path: 'create',
                        component: () => import('../views/admin/users/Create.vue'),
                        meta: {
                            breadCrumb: 'Crear Usuario',
                            linked: false
                        }
                    },
                    {
                        name: 'users.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/users/Edit.vue'),
                        meta: {
                            breadCrumb: 'Editar Usuario',
                            linked: false
                        }
                    }
                ]
            },

            {
                name: 'roles',
                path: 'roles',
                meta: { breadCrumb: 'Roles' },
                children: [
                    {
                        name: 'roles.index',
                        path: '',
                        component: () => import('../views/admin/roles/Index.vue'),
                        meta: {
                            breadCrumb: 'Roles',
                            hideBreadcrumb: true
                        }
                    },
                    {
                        name: 'admin.roles.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/roles/Edit.vue'),
                        meta: {
                            breadCrumb: 'Editar Rol',
                            linked: false
                        }
                    }
                ]
            },
        ]
    },
    {
        path: "/:pathMatch(.*)*",
        name: 'NotFound',
        component: () => import("../views/errors/404.vue"),
    },
];
